<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter by date range
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        // Filter by keyword search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uQuery) use ($search) {
                      $uQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Get distinct action types and users for filtering dropdowns
        $users = User::where('is_admin', true)->orWhereIn('id', function($q) {
            $q->select('user_id')->from('activity_logs');
        })->orderBy('name')->get();

        $actionTypes = ActivityLog::select('action')->distinct()->pluck('action');

        $actionLabels = [
            'upload_ransum' => 'Upload Ransum',
            'import_ransum' => 'Import Ransum',
            'upload_signature_photo' => 'Upload Foto Ttd',
            'add_ransum_item' => 'Tambah Item Ransum',
            'finalize_ransum' => 'Finalisasi Ransum',
            'edit_ransum_item' => 'Edit Item Ransum',
            'delete_ransum_item' => 'Hapus Item Ransum',
            'download_do' => 'Download DO',
            'download_invoice' => 'Download Invoice Ransum',
            'download_ams' => 'Download Surat AMS',
            'delete_ransum' => 'Hapus Ransum Upload',
            'download_total_pdf' => 'Download PDF Total',
            'download_list_pdf' => 'Download PDF List',
            'download_po_ransum' => 'Download PO Ransum',
            'update_po_status_ransum' => 'Update Status PO Ransum',
            'update_order_status' => 'Update Status Order',
            'download_order_invoice' => 'Download Invoice Order',
            'download_order_totalsheet' => 'Download Excel Total Sheet',
            'download_order_po' => 'Download PO Order',
            'update_po_status_order' => 'Update Status PO Order',
            'create_product' => 'Tambah Produk',
            'update_product' => 'Edit Produk',
            'delete_product' => 'Hapus Produk',
            'create_vendor' => 'Tambah Vendor',
            'update_vendor' => 'Edit Vendor',
            'delete_vendor' => 'Hapus Vendor',
            'create_warehouse' => 'Tambah Warehouse',
            'delete_warehouse' => 'Hapus Warehouse',
            'create_user' => 'Tambah User',
            'delete_admin' => 'Hapus Admin',
            'create_admin' => 'Tambah Admin',
            'update_user' => 'Edit User/Admin',
            'delete_user' => 'Hapus User',
            'create_ship' => 'Tambah Kapal',
            'upload_export_file' => 'Upload File Export',
            'download_export_file' => 'Download File Export',
            'delete_export_file' => 'Hapus File Export',
        ];

        return view('admin.history.index', compact('logs', 'users', 'actionTypes', 'actionLabels'));
    }
}
