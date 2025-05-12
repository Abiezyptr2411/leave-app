<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CutiController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $role = session('role');

        if (in_array($role, [1, 2])) {
            $totalCuti = Cuti::count();
            $cutiDisetujui = Cuti::where('status', 'disetujui')->count();
            $cutiDitolak = Cuti::where('status', 'ditolak')->count();
            $cutiPending = Cuti::where('status', 'pending')->count();

            $chartData = Cuti::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->whereMonth('created_at', now()->month)
                ->get();

            $cutiPendingList = Cuti::latest()
                ->where(function ($query) use ($role) {
                    if ($role == 2) {
                        $query->where('status', 'disetujui_lead');
                    } else {
                        $query->where('status', 'pending');
                    }
                })
                ->get();
        } else {
            $totalCuti = Cuti::where('user_id', $userId)->count();
            $cutiDisetujui = Cuti::where('user_id', $userId)->where('status', 'disetujui')->count();
            $cutiDitolak = Cuti::where('user_id', $userId)->where('status', 'ditolak')->count();
            $cutiPending = Cuti::where('user_id', $userId)->where('status', 'pending')->count();

            $chartData = Cuti::where('user_id', $userId)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->whereMonth('created_at', now()->month)
                ->get();

            $cutiPendingList = Cuti::where('user_id', $userId)
                ->latest()
                ->get();
        }

        $cutiPendingList = $cutiPendingList->transform(function ($cuti) {
            switch ($cuti->status) {
                case 'disetujui_lead':
                    $cuti->status_label = 'Diproses ke Building Manager';
                    $cuti->status_badge = 'bg-danger-soft';
                    break;
                case 'disetujui_bm':
                case 'disetujui':
                    $cuti->status_label = 'Cuti Disetujui';
                    $cuti->status_badge = 'bg-success-soft';
                    break;
                case 'ditolak':
                    $cuti->status_label = 'Cuti Tidak Disetujui';
                    $cuti->status_badge = 'bg-danger-soft';
                    break;
                case 'pending':
                    $cuti->status_label = 'Menunggu Konfirmasi';
                    $cuti->status_badge = 'bg-warning-soft';
                    break;
                default:
                    $cuti->status_label = 'Status Tidak Dikenal';
                    $cuti->status_badge = 'bg-danger-soft';
                    break;
            }

            return $cuti;
        });

        return view('cuti.dashboard', compact(
            'totalCuti', 'cutiDisetujui', 'cutiDitolak', 'cutiPending', 'chartData', 'cutiPendingList', 'role'
        ));
    }


    public function list(Request $request)
    {
        if (!session('user_id')) return redirect('/login');
        $role = session('role');
        $userId = session('user_id'); 
    
        $query = Cuti::with('user');
    
        if ($role == 1 || $role == 2) {
            $query->whereIn('status', ['pending', 'disetujui_lead', 'disetujui_bm', 'ditolak']);
        } else {
            $query->where('user_id', $userId);
        }
    
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('alasan', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('user', function ($q2) use ($searchTerm) {
                    $q2->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }
    
        // Filter berdasarkan status cuti
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
    
        // Filter berdasarkan tanggal mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }
    
        // Filter berdasarkan tanggal selesai
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }
    
        // Ambil data cuti yang sudah disaring sesuai kondisi
        $cutis = $query->orderBy('created_at', 'desc')->get();
    
        $cutis->transform(function ($cuti) {
            switch ($cuti->status) {
                case 'disetujui_lead':
                    $cuti->status_label = 'Diproses ke Building Manager';
                    break;
                case 'disetujui_bm':
                    $cuti->status_label = 'Disetujui';
                    break;
                case 'ditolak':
                    $cuti->status_label = 'Ditolak';
                    break;
                case 'pending':
                    $cuti->status_label = 'Menunggu Konfirmasi';
                    break;
                default:
                    $cuti->status_label = 'Status Tidak Dikenal';
                    break;
            }
    
            return $cuti;
        });
    
        return view('cuti.index', compact('cutis'));
    }
    
    public function uploadList(Request $request)
    {
        if (!session('user_id')) return redirect('/login');
        $role = session('role'); 

        $query = Cuti::with('user', 'uploadedDocuments'); 

        if ($role != 1) {
            $query->where('user_id', session('user_id'));
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('alasan', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('user', function ($q2) use ($searchTerm) {
                    $q2->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $cutis = $query->orderBy('created_at', 'desc')->get();

        return view('cuti.upload_list', compact('cutis'));
    }

    // public function uploadDocument(Request $request, $cutiId)
    // {
    //     if (!session('user_id')) return redirect('/login');

    //     // Validasi file upload
    //     $request->validate([
    //         'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //     ]);

    //     $cuti = Cuti::findOrFail($cutiId);
    //     $userId = session('user_id');

    //     // Pastikan yang mengupload adalah admin atau user yang sama dengan cuti
    //     if (session('role') != 1 && $cuti->user_id != $userId) {
    //         return redirect()->back()->with('error', 'Akses ditolak.');
    //     }

    //     $file = $request->file('file');
    //     $path = $file->storeAs('uploads/documents', $file->getClientOriginalName(), 'public');

    //     UploadedDocument::create([
    //         'cuti_id' => $cutiId,
    //         'filename' => $file->getClientOriginalName(),
    //         'filepath' => $path,
    //     ]);

    //     return redirect()->route('cuti.uploadList')->with('success', 'Dokumen berhasil diupload.');
    // }

    public function create()
    {
        if (!session('user_id')) return redirect('/login');
        return view('cuti.create');
    }

    public function store(Request $request)
    {
        if (!session('user_id')) return redirect('/login');

        $userId = session('user_id');

        $request->validate([
            'alasan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $hariIni = Carbon::now();

        // Validasi cuti harus diajukan minimal 14 hari sebelum tanggal mulai
        if ($mulai->lt($hariIni->copy()->addDays(14))) {
            return redirect('/cuti')->with('error', 'Pengajuan cuti harus dilakukan minimal 14 hari sebelum tanggal mulai cuti.');
        }

        // Validasi tanggal mulai tidak lebih besar dari tanggal selesai
        if ($mulai->gt($selesai)) {
            return redirect('/cuti')->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.');
        }

        // Validasi jika masih ada cuti pending (belum selesai)
        $masihPending = DB::table('cuti')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();

        if ($masihPending) {
            return redirect('/cuti')->with('error', 'Masih ada pengajuan cuti yang pending. Harap tunggu sampai diproses.');
        }

        // Validasi bentrok tanggal cuti (overlap)
        $bentrok = DB::table('cuti')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'disetujui']) 
            ->where(function ($query) use ($mulai, $selesai) {
                $query->whereBetween('tanggal_mulai', [$mulai, $selesai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                    ->orWhere(function($q) use ($mulai, $selesai) {
                        $q->where('tanggal_mulai', '<=', $mulai)
                        ->where('tanggal_selesai', '>=', $selesai);
                    });
            })
            ->exists();

        if ($bentrok) {
            return redirect('/cuti')->with('error', 'Masih ada pengajuan cuti yang aktif di tanggal tersebut.');
        }

        // Simpan pengajuan cuti
        Cuti::create([
            'user_id' => $userId,
            'alasan' => $request->alasan,
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'status' => 'pending'
        ]);

        return redirect('/cuti')->with('success', 'Pengajuan cuti berhasil.');
    } 

    public function show($id)
    {
        if (!session('user_id')) return redirect('/login');

        $cuti = Cuti::with('user')->findOrFail($id);

        $pdf = Pdf::loadView('cuti.pdf', compact('cuti'));
        return $pdf->download('bukti_pengajuan_cuti_'.$cuti->id.'.pdf');
    }

    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);
        $currentUserRole = session('role'); 

        if ($currentUserRole == 1) { 
            if ($cuti->status == 'pending') {
                $cuti->status = 'disetujui_lead'; 
                $cuti->save();
                return redirect()->back()->with('success', 'Cuti berhasil disetujui sementara oleh Lead.');
            }
        }
        elseif ($currentUserRole == 2) {
            if ($cuti->status == 'disetujui_lead') {
                $cuti->status = 'disetujui_bm'; 
                $cuti->save();
                return redirect()->back()->with('success', 'Cuti berhasil disetujui oleh Building Manager.');
            }
        }

        return redirect()->back()->with('error', 'Tidak dapat memproses persetujuan ini.');
    }

    public function reject($id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->status = 'ditolak';
        $cuti->save();

        return redirect()->back()->with('success', 'Cuti berhasil ditolak.');
    }

    public function export(Request $request)
    {
        if (!session('user_id')) {
            return redirect('/login');
        }
    
        $query = Cuti::query();
    
        if (session('role') != 1) {
            $query->where('user_id', session('user_id'));
        }
    
        if ($request->filled('search')) {
            $query->where('alasan', 'like', '%' . $request->search . '%');
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }
    
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }
    
        $cutis = $query->orderBy('created_at', 'desc')->get();
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set header kolom
        $headers = ['No', 'Nama User', 'Alasan Cuti', 'Tanggal Mulai', 'Tanggal Selesai', 'Status'];
        $sheet->fromArray($headers, null, 'A1');
    
        $row = 2;
        foreach ($cutis as $index => $cuti) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $cuti->user->name ?? '-');
            $sheet->setCellValue('C' . $row, $cuti->alasan);
            $sheet->setCellValue('D' . $row, $cuti->tanggal_mulai);
            $sheet->setCellValue('E' . $row, $cuti->tanggal_selesai);
            $sheet->setCellValue('F' . $row, $cuti->status);
            $row++;
        }
    
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        $filename = 'cuti_export_' . now()->format('Ymd_His') . '.xlsx';
    
        $writer = new Xlsx($spreadsheet);
        $response = response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename);
    
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        return $response;
    }

    public function import(Request $request)
    {
        if (!session('user_id')) {
            return redirect('/login');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $user = User::find(session('user_id'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.',
            ]);
        }

        $cutiBerjalan = Cuti::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected']) 
            ->whereDate('tanggal_selesai', '>=', Carbon::today())
            ->exists();

        if ($cutiBerjalan) {
            return response()->json([
                'success' => false,
                'message' => 'Opsss, Masih ada cuti yang berjalan dan belum disetujui.',
            ]);
        }

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File Excel tidak valid atau terjadi kesalahan saat membaca file.',
            ]);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        if (empty($data) || count($data) <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'File Excel kosong atau tidak valid.',
            ]);
        }

        $header = $data[1];
        $header = array_map('strtolower', $header);
        unset($data[1]);

        foreach ($data as $row) {
            $rowData = array_combine($header, $row);

            if (!isset($rowData['alasan cuti']) || !isset($rowData['tanggal mulai']) || !isset($rowData['tanggal selesai'])) {
                continue; 
            }

            try {
                $mulai = Carbon::parse($rowData['tanggal mulai']);
                $selesai = Carbon::parse($rowData['tanggal selesai']);
            } catch (\Exception $e) {
                continue; 
            }

            if ($mulai->gt($selesai)) {
                continue; 
            }

            try {
                Cuti::create([
                    'user_id' => $user->id,
                    'alasan' => $rowData['alasan cuti'],
                    'tanggal_mulai' => $mulai->format('Y-m-d'),
                    'tanggal_selesai' => $selesai->format('Y-m-d'),
                    'status' => 'pending',
                ]);
            } catch (\Exception $e) {
                continue; 
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data cuti berhasil diimport.',
        ]);
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Alasan Cuti', 'Tanggal Mulai', 'Tanggal Selesai'];
        $sheet->fromArray($headers, null, 'A1');

        $sheet->setCellValue('A2', 'Alasan contoh cuti');
        $sheet->setCellValue('B2', '2025-05-01');
        $sheet->setCellValue('C2', '2025-05-03');

        $writer = new Xlsx($spreadsheet);

        $fileName = 'Template_Import_Cuti.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
