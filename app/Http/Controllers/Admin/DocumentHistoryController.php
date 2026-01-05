<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\DocumentType;

class DocumentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['documentType', 'activities', 'approvedBy'])
                                ->orderBy('created_at', 'desc');

        if ($request->filled('applicant_type')) {
            $query->where('applicant_type', $request->applicant_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('request_code', 'like', $searchTerm)
                  ->orWhere('applicant_name', 'like', $searchTerm)
                  ->orWhere('applicant_identifier', 'like', $searchTerm)
                  ->orWhere('applicant_email', 'like', $searchTerm)
                  ->orWhere('applicant_phone', 'like', $searchTerm);
            });
        }

        $documents = $query->paginate(15)->withQueryString();

        $documentTypes = DocumentType::orderBy('name')->get();

        return view('admin.documents.history', [
            'title' => 'Riwayat Pengajuan',
            'subtitle' => 'Semua riwayat pengajuan dokumen dengan filter lengkap',
            'documents' => $documents,
            'documentTypes' => $documentTypes,
        ]);
    }


    public function export(Request $request)
    {
        $format = $request->input('export', 'excel');

        if (!in_array($format, ['excel', 'pdf'])) {
            return back()->with('error', 'Format export tidak valid');
        }

        $query = DocumentRequest::with(['documentType', 'activities', 'approvedBy'])
                                ->orderBy('created_at', 'desc');

        if ($request->filled('applicant_type')) {
            $query->where('applicant_type', $request->applicant_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('request_code', 'like', $searchTerm)
                  ->orWhere('applicant_name', 'like', $searchTerm)
                  ->orWhere('applicant_identifier', 'like', $searchTerm)
                  ->orWhere('applicant_email', 'like', $searchTerm)
                  ->orWhere('applicant_phone', 'like', $searchTerm);
            });
        }

        $documents = $query->get();

        return back()->with('info', 'Fitur export ' . strtoupper($format) . ' sedang dalam pengembangan. Total data yang akan diexport: ' . $documents->count());
    }
}
