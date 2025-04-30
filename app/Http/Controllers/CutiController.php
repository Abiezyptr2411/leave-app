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

        if ($role == 1) {
            $totalCuti = Cuti::count();
            $cutiDisetujui = Cuti::where('status', 'disetujui')->count();
            $cutiDitolak = Cuti::where('status', 'ditolak')->count();
            $cutiPending = Cuti::where('status', 'pending')->count();

            $chartData = Cuti::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->whereMonth('created_at', now()->month)
                ->get();

            $cutiPendingList = Cuti::where('status', 'pending')
                ->latest()
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

        return view('cuti.dashboard', compact(
            'totalCuti', 'cutiDisetujui', 'cutiDitolak', 'cutiPending', 'chartData', 'cutiPendingList', 'role'
        ));
    }

    public function list(Request $request)
    {
       if (!session('user_id')) return redirect('/login');
       $role = session('role'); 

       $query = Cuti::with('user');

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

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }

        $cutis = $query->orderBy('created_at', 'desc')->get();

        return view('cuti.index', compact('cutis'));
    }

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

        if ($mulai->gt($selesai)) {
            return redirect('/cuti')->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.');
        }

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
        $cuti->status = 'disetujui';
        $cuti->save();

        return redirect()->back()->with('success', 'Cuti berhasil disetujui.');
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
