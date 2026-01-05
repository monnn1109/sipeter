<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'applicant_type' => 'required|in:mahasiswa,dosen,staff,umum',
            'applicant_name' => 'required|string|max:255',
            'applicant_identifier' => 'required|string|max:50',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'required|string|max:20',
            'applicant_address' => 'required|string|max:500',
            'applicant_unit' => 'nullable|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'quantity' => 'required|integer|min:1|max:10',
            'purpose' => 'required|string|max:500',
            'needed_date' => 'required|date|after:today',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'applicant_type.required' => 'Jenis pemohon harus dipilih',
            'applicant_name.required' => 'Nama lengkap harus diisi',
            'applicant_identifier.required' => 'NIM/NIP/NIK harus diisi',
            'applicant_email.required' => 'Email harus diisi',
            'applicant_email.email' => 'Format email tidak valid',
            'applicant_phone.required' => 'No. HP harus diisi',
            'applicant_address.required' => 'Alamat harus diisi',
            'document_type_id.required' => 'Jenis dokumen harus dipilih',
            'document_type_id.exists' => 'Jenis dokumen tidak valid',
            'quantity.required' => 'Jumlah eksemplar harus diisi',
            'quantity.min' => 'Jumlah minimal 1 eksemplar',
            'quantity.max' => 'Jumlah maksimal 10 eksemplar',
            'purpose.required' => 'Keperluan harus diisi',
            'needed_date.required' => 'Tanggal dibutuhkan harus diisi',
            'needed_date.after' => 'Tanggal dibutuhkan minimal besok',
            'attachment.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'attachment.max' => 'Ukuran file maksimal 2MB',
        ];
    }
}
