<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, DocumentType};
use App\Http\Requests\InternalDocumentRequest;
use App\Events\DocumentRequestSubmitted;
use App\Helpers\DocumentCodeGenerator;
use App\Enums\DeliveryMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentRequestController extends Controller
{
    public function form()
    {
        $user = auth()->user();

        if (!$user->isInternal()) {
            abort(403, 'Unauthorized access');
        }

        $applicableFor = $user->role->value;

        $documentTypes = DocumentType::active()
            ->where(function($query) use ($applicableFor) {
                $query->whereJsonContains('applicable_for', $applicableFor)
                    ->orWhereJsonContains('applicable_for', 'all');

                $query->orWhere('applicable_for', 'LIKE', "%\"{$applicableFor}\"%")
                    ->orWhere('applicable_for', 'LIKE', '%"all"%');

                if (in_array($applicableFor, ['dosen', 'staff'])) {
                    $query->orWhereJsonContains('applicable_for', 'internal')
                        ->orWhere('applicable_for', 'LIKE', '%"internal"%');
                }
            })
            ->orderBy('name')
            ->get();

        return view('internal.document-form', [
            'documentTypes' => $documentTypes,
            'user' => $user,
            'title' => 'Pengajuan Dokumen',
            'active' => 'document-request'
        ]);
    }

    public function store(InternalDocumentRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $user->nip_nidn . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('documents/attachments/internal', $filename, 'public');
            }

            $requestCode = DocumentCodeGenerator::generate($request->document_type_id);

            $deliveryMethod = $request->delivery_method === 'download'
                ? DeliveryMethod::DOWNLOAD
                : DeliveryMethod::PICKUP;

            $documentRequest = DocumentRequest::create([
                'request_code' => $requestCode,
                'user_id' => $user->id,
                'user_type' => 'internal',

                'applicant_type' => $user->role->value,
                'applicant_name' => $user->name,
                'applicant_identifier' => $user->nip_nidn ?? $user->email,
                'applicant_email' => $user->email,
                'applicant_phone' => $user->phone_number,
                'applicant_address' => $request->applicant_address ?? null,
                'applicant_unit' => $user->unit ?? '-',

                'document_type_id' => $request->document_type_id,
                'quantity' => $request->quantity ?? 1,
                'purpose' => $request->purpose,
                'needed_date' => $request->needed_date ?? null,
                'notes' => $request->notes ?? null,

                'attachment_path' => $attachmentPath,
                'delivery_method' => $deliveryMethod,

                'status' => 'submitted',
                'submitted_at' => now(),

                'current_verification_step' => 0,
            ]);

            event(new DocumentRequestSubmitted($documentRequest));

            DB::commit();

            return redirect()
                ->route('internal.my-documents.show', $documentRequest->id)
                ->with('success', 'Pengajuan berhasil dibuat! Kode: ' . $documentRequest->request_code);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error storing document request: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan permohonan. Silakan coba lagi.');
        }
    }

    public function getDocumentType($id)
    {
        $user = auth()->user();
        $applicableFor = $user->role->value;

        $documentType = DocumentType::active()
            ->where(function($query) use ($applicableFor) {
                $query->whereJsonContains('applicable_for', $applicableFor)
                    ->orWhereJsonContains('applicable_for', 'all')
                    ->orWhere('applicable_for', 'LIKE', "%\"{$applicableFor}\"%")
                    ->orWhere('applicable_for', 'LIKE', '%"all"%');

                if (in_array($applicableFor, ['dosen', 'staff'])) {
                    $query->orWhereJsonContains('applicable_for', 'internal')
                        ->orWhere('applicable_for', 'LIKE', '%"internal"%');
                }
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $documentType->id,
                'name' => $documentType->name,
                'code_prefix' => $documentType->code_prefix,
                'description' => $documentType->description,
                'processing_days' => $documentType->processing_days,
                'required_fields' => $documentType->required_fields,
            ]
        ]);
    }
}
