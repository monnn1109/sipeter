<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Http\Requests\MahasiswaDocumentRequest;
use App\Events\DocumentRequestSubmitted;
use App\Helpers\DocumentCodeGenerator;
use App\Enums\ApplicantType;
use App\Enums\DocumentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class MahasiswaDocumentController extends Controller
{
    public function form()
    {
        try {
            $documentTypes = DocumentType::where('is_active', true)
                ->orderBy('name')
                ->get()
                ->filter(function($type) {
                    $applicableFor = is_string($type->applicable_for)
                        ? json_decode($type->applicable_for, true)
                        : $type->applicable_for;

                    return is_array($applicableFor) && in_array('mahasiswa', $applicableFor);
                });

            if ($documentTypes->isEmpty()) {
                return redirect()->route('home')
                    ->withErrors(['error' => 'Belum ada jenis dokumen tersedia.']);
            }

            return view('guest.mahasiswa-form', compact('documentTypes'));

        } catch (\Exception $e) {
            Log::error('Form load error', ['error' => $e->getMessage()]);
            return redirect()->route('home')->withErrors(['error' => 'Gagal memuat form.']);
        }
    }

    public function submit(MahasiswaDocumentRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $documentType = DocumentType::where('id', $validated['document_type_id'])
                ->where('is_active', true)
                ->first();

            if (!$documentType) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['document_type_id' => 'Jenis dokumen tidak valid.']);
            }

            $requestCode = DocumentCodeGenerator::generate(
                $documentType->code_prefix,
                $validated['applicant_nim']
            );

            $documentRequest = DocumentRequest::create([
                'request_code' => $requestCode,
                'document_type_id' => $documentType->id,
                'applicant_type' => ApplicantType::MAHASISWA,
                'applicant_name' => $validated['applicant_name'],
                'applicant_identifier' => $validated['applicant_nim'],
                'applicant_email' => $validated['applicant_email'],
                'applicant_phone' => $validated['applicant_phone'],
                'applicant_unit' => $validated['applicant_unit'],
                'applicant_address' => $validated['applicant_address'],
                'purpose' => $validated['purpose'],
                'notes' => $validated['notes'] ?? null,
                'delivery_method' => $validated['delivery_method'],
                'status' => DocumentStatus::SUBMITTED,
            ]);

            DB::commit();

            try {
                event(new DocumentRequestSubmitted($documentRequest));
            } catch (\Exception $e) {
                Log::warning('Event failed', ['error' => $e->getMessage()]);
            }

            $request->session()->forget(['errors', '_old_input']);

            return redirect()
                ->route('mahasiswa.success', $documentRequest->id)
                ->with('success', 'Pengajuan berhasil dibuat!')
                ->with('request_code', $documentRequest->request_code);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submit error', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function success($id)
    {
        try {
            $documentRequest = DocumentRequest::with('documentType')->findOrFail($id);
            return view('guest.mahasiswa-success', compact('documentRequest'));
        } catch (\Exception $e) {
            return redirect()->route('home')->withErrors(['error' => 'Dokumen tidak ditemukan.']);
        }
    }

    /**
     * ✅ Show tracking search page (GET)
     */
    public function tracking()
    {
        return view('guest.tracking', [
            'documents' => collect(),
            'search' => null
        ]);
    }

    /**
     * ✅ FIXED: Process tracking search (POST)
     * Search by: request_code, name, NIM/NIP, unit
     */
    public function trackingCheck(Request $request)
    {
        try {
            $validated = $request->validate([
                'search' => 'required|string|min:2|max:100'
            ], [
                'search.required' => 'Masukkan kode dokumen, nama, NIM, atau program studi',
                'search.min' => 'Pencarian minimal 2 karakter',
                'search.max' => 'Pencarian maksimal 100 karakter'
            ]);

            $search = trim($validated['search']);

            // ✅ Search dengan multiple criteria
            $documents = DocumentRequest::query()
                ->with(['documentType'])
                ->where(function($query) use ($search) {
                    $query->where('request_code', 'LIKE', "%{$search}%")
                          ->orWhere('applicant_name', 'LIKE', "%{$search}%")
                          ->orWhere('applicant_identifier', 'LIKE', "%{$search}%")
                          ->orWhere('applicant_unit', 'LIKE', "%{$search}%");
                })
                ->latest('created_at')
                ->limit(50)
                ->get();

            // ✅ If only 1 document found, auto redirect to detail
            if ($documents->count() === 1) {
                $doc = $documents->first();

                Log::info('Tracking: Auto redirect to detail', [
                    'request_code' => $doc->request_code,
                    'id' => $doc->id
                ]);

                return redirect()->route('mahasiswa.tracking.detail', $doc->request_code);
            }

            // ✅ If multiple or no documents, show search results page
            return view('guest.tracking', [
                'documents' => $documents,
                'search' => $search
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Tracking search error', [
                'error' => $e->getMessage(),
                'search' => $request->search ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['search' => 'Terjadi kesalahan. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * ✅ FIXED: Show tracking detail page
     * Support both request_code (recommended) and id
     */
    public function trackingDetail($identifier)
    {
        try {
            Log::info('Tracking detail accessed', [
                'identifier' => $identifier,
                'type' => is_numeric($identifier) ? 'id' : 'code'
            ]);

            // ✅ FIXED: Proper eager loading WITHOUT .user relationship
            $documentRequest = DocumentRequest::with([
                'documentType',
                'verifications' => function($query) {
                    $query->orderBy('verification_level', 'asc');
                },
                'verifications.authority', // ✅ FIXED: Remove .user (doesn't exist)
                'documentSignatures' => function($query) {
                    $query->orderBy('signature_level', 'asc');
                },
                'documentSignatures.authority', // ✅ FIXED: Remove .user (doesn't exist)
                'activities' => function($query) {
                    $query->latest();
                },
                'activities.performedBy'
            ])
            ->where(function($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', $identifier);
                } else {
                    $query->where('request_code', $identifier);
                }
            })
            ->firstOrFail();

            Log::info('Document found', [
                'id' => $documentRequest->id,
                'code' => $documentRequest->request_code,
                'status' => $documentRequest->status->value,
                'verifications_count' => $documentRequest->verifications->count()
            ]);

            return view('guest.tracking-detail', compact('documentRequest'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Document not found', [
                'identifier' => $identifier
            ]);

            return redirect()
                ->route('mahasiswa.tracking')
                ->withErrors(['error' => 'Dokumen tidak ditemukan. Pastikan kode dokumen benar.']);

        } catch (\Exception $e) {
            Log::error('Tracking detail error', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('mahasiswa.tracking')
                ->withErrors(['error' => 'Gagal memuat detail dokumen. Silakan coba lagi.']);
        }
    }

    /**
     * Get document type details (AJAX)
     */
    public function getDocumentType($id)
    {
        try {
            $documentType = DocumentType::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $documentType->id,
                    'name' => $documentType->name,
                    'description' => $documentType->description,
                    'processing_time' => $documentType->processing_days . ' hari',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document type not found'
            ], 404);
        }
    }
}
