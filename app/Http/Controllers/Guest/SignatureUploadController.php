<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, SignatureToken, DocumentSignature, SignatureAuthority};
use App\Events\{SignatureUploaded, SignatureRequested};
use App\Http\Requests\SignatureUploadRequest;
use App\Enums\{SignatureStatus, DocumentStatus};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Storage};
use Illuminate\Support\Str;

class SignatureUploadController extends Controller
{
    public function show($token)
    {
        try {
            $signature = DocumentSignature::where(function($query) use ($token) {
                    $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.token')) = ?", [$token])
                        ->orWhereRaw("JSON_EXTRACT(metadata, '$.token') = ?", ["\"{$token}\""]);
                })
                ->with([
                    'documentRequest.documentType',
                    'signatureAuthority'
                ])
                ->first();

            if (!$signature) {
                Log::warning('Signature token not found', ['token' => substr($token, 0, 10) . '...']);
                return redirect()->route('signature.error', ['message' => 'invalid_token']);
            }

            $tokenExpiry = $signature->metadata['token_expires_at'] ?? null;
            if ($tokenExpiry && now()->greaterThan($tokenExpiry)) {
                Log::warning('Signature token expired', [
                    'token' => substr($token, 0, 10) . '...',
                    'expired_at' => $tokenExpiry
                ]);
                return redirect()->route('signature.error', ['message' => 'expired']);
            }

            if ($signature->status !== SignatureStatus::REQUESTED) {
                Log::warning('Signature already used', [
                    'token' => substr($token, 0, 10) . '...',
                    'current_status' => $signature->status->value
                ]);
                return redirect()->route('signature.error', ['message' => 'already_used']);
            }

            return view('guest.signature-upload', [
                'token' => $token,
                'documentRequest' => $signature->documentRequest,
                'authority' => $signature->signatureAuthority,
                'signature' => $signature,
            ]);

        } catch (\Exception $e) {
            Log::error('Signature upload page error', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('signature.error', ['message' => 'invalid_token']);
        }
    }

    public function upload(SignatureUploadRequest $request, $token)
    {
        Log::info('🔥 SIGNATURE UPLOAD ATTEMPT', [
            'token' => substr($token, 0, 10) . '...',
            'has_file' => $request->hasFile('signature_file'),
            'file_name' => $request->hasFile('signature_file') ? $request->file('signature_file')->getClientOriginalName() : 'NO FILE',
        ]);

        try {
            $signature = DocumentSignature::where(function($query) use ($token) {
                    $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.token')) = ?", [$token])
                        ->orWhereRaw("JSON_EXTRACT(metadata, '$.token') = ?", ["\"{$token}\""]);
                })
                ->with([
                    'documentRequest',
                    'signatureAuthority'
                ])
                ->first();

            if (!$signature) {
                Log::error('❌ Signature not found in upload', ['token' => substr($token, 0, 10)]);
                return back()->withErrors(['error' => 'Token tidak valid.'])->withInput();
            }

            $tokenExpiry = $signature->metadata['token_expires_at'] ?? null;
            if ($tokenExpiry && now()->greaterThan($tokenExpiry)) {
                Log::error('❌ Token expired', ['token' => substr($token, 0, 10), 'expired_at' => $tokenExpiry]);
                return back()->withErrors(['error' => 'Token sudah kadaluarsa. Silakan hubungi admin.'])->withInput();
            }

            if ($signature->status !== SignatureStatus::REQUESTED) {
                Log::error('❌ Token already used', [
                    'token' => substr($token, 0, 10),
                    'status' => $signature->status->value
                ]);
                return back()->withErrors(['error' => 'Token sudah digunakan sebelumnya.'])->withInput();
            }

            DB::beginTransaction();

            $documentRequest = $signature->documentRequest;
            $authority = $signature->signatureAuthority;
            $currentLevel = $signature->signature_level ?? 1;

            $signatureFile = $request->file('signature_file');

            if (!$signatureFile->isValid()) {
                DB::rollBack();
                Log::error('❌ Invalid file', ['token' => substr($token, 0, 10)]);
                return back()->withErrors(['signature_file' => 'File tidak valid.'])->withInput();
            }

            $filename = 'signature_' . $documentRequest->request_code . '_L' . $currentLevel . '_' . time() . '.' . $signatureFile->getClientOriginalExtension();
            $path = $signatureFile->storeAs('signatures/level_' . $currentLevel, $filename, 'public');

            if (!$path) {
                DB::rollBack();
                Log::error('❌ Failed to store file', ['token' => substr($token, 0, 10)]);
                return back()->withErrors(['signature_file' => 'Gagal menyimpan file.'])->withInput();
            }

            Log::info('✅ File stored successfully', ['path' => $path, 'filename' => $filename]);

            $signature->update([
                'signature_file' => $path,
                'qr_code_file' => null,
                'status' => SignatureStatus::UPLOADED,
                'uploaded_at' => now(),
                'notes' => $request->notes,
            ]);

            Log::info('✅ Signature updated', ['signature_id' => $signature->id]);

            $documentRequest->update([
                'status' => DocumentStatus::SIGNATURE_UPLOADED,
                'current_signature_step' => $currentLevel,
            ]);

            Log::info('✅ Document request updated', ['document_id' => $documentRequest->id]);

            event(new SignatureUploaded($documentRequest, $signature, $authority));

            Log::info('✅ Event fired: SignatureUploaded');

            if ($currentLevel < 3) {
                $nextLevel = $currentLevel + 1;

                $existingNextLevel = DocumentSignature::where('document_request_id', $documentRequest->id)
                    ->where('signature_level', $nextLevel)
                    ->first();

                if (!$existingNextLevel) {
                    $nextAuthority = SignatureAuthority::getActiveByLevel($nextLevel);

                    if ($nextAuthority) {
                        $nextToken = Str::random(64);
                        $nextTokenExpiry = now()->addDays(7);

                        $nextSignature = DocumentSignature::create([
                            'document_request_id' => $documentRequest->id,
                            'signature_authority_id' => $nextAuthority->id,
                            'signature_level' => $nextLevel,
                            'status' => SignatureStatus::REQUESTED,
                            'requested_at' => now(),
                            'metadata' => [
                                'token' => $nextToken,
                                'token_expires_at' => $nextTokenExpiry->toDateTimeString(),
                                'auto_triggered_from_level' => $currentLevel,
                            ]
                        ]);

                        $nextUploadLink = route('signature.upload.show', $nextToken);

                        event(new SignatureRequested($nextSignature, $documentRequest, $nextAuthority, $nextUploadLink));

                        Log::info('✅ Auto-triggered next signature level', [
                            'document_id' => $documentRequest->id,
                            'from_level' => $currentLevel,
                            'to_level' => $nextLevel,
                            'next_authority' => $nextAuthority->name,
                        ]);
                    } else {
                        Log::warning('⚠️ Next level authority not found', [
                            'document_id' => $documentRequest->id,
                            'next_level' => $nextLevel,
                        ]);
                    }
                } else {
                    Log::info('ℹ️ Next level signature already exists', [
                        'document_id' => $documentRequest->id,
                        'next_level' => $nextLevel,
                        'existing_status' => $existingNextLevel->status->value,
                    ]);
                }
            } else {
                Log::info('🎉 All signature levels uploaded', [
                    'document_id' => $documentRequest->id,
                    'document_code' => $documentRequest->request_code,
                ]);
            }

            DB::commit();

            Log::info('✅ Transaction committed successfully');

            return redirect()->route('signature.upload.success', $signature->id)
                ->with('success', 'Tanda tangan berhasil diupload!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ SIGNATURE UPLOAD FAILED', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Gagal mengupload tanda tangan: ' . $e->getMessage()])->withInput();
        }
    }

    public function success($signatureId)
    {
        try {
            $signature = DocumentSignature::with([
                'documentRequest.documentType',
                'signatureAuthority'
            ])->findOrFail($signatureId);

            return view('guest.signature-upload-success', [
                'signature' => $signature,
                'documentRequest' => $signature->documentRequest,
                'authority' => $signature->signatureAuthority,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Signature success page error', [
                'signature_id' => $signatureId,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('signature.error', ['message' => 'not_found']);
        }
    }

    public function error(Request $request, $message = null)
    {
        $errorMessages = [
            'expired' => [
                'title' => 'Token Kadaluarsa',
                'message' => 'Link upload tanda tangan sudah kadaluarsa. Silakan hubungi admin untuk mendapatkan link baru.',
            ],
            'already_used' => [
                'title' => 'Token Sudah Digunakan',
                'message' => 'Tanda tangan sudah diupload sebelumnya. Jika ada kesalahan, silakan hubungi admin.',
            ],
            'invalid_token' => [
                'title' => 'Token Tidak Valid',
                'message' => 'Link upload tanda tangan tidak valid. Pastikan Anda menggunakan link yang benar dari email/WhatsApp.',
            ],
            'not_found' => [
                'title' => 'Data Tidak Ditemukan',
                'message' => 'Data tanda tangan tidak ditemukan.',
            ],
            'default' => [
                'title' => 'Terjadi Kesalahan',
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi admin.',
            ],
        ];

        $error = $errorMessages[$message] ?? $errorMessages['default'];

        return view('guest.signature-error', [
            'title' => $error['title'],
            'message' => $error['message'],
        ]);
    }
}
