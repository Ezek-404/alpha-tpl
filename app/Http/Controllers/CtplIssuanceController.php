<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CtplIssuanceController extends Controller
{
    public function create()
    {
        return view('ctpl.issuance');
    }

    public function index(Request $request)
    {
        $query = Transaction::query(); // O kung anong model ang gamit mo

        // Dito nangyayari ang filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('assured', 'like', '%' . $search . '%')
                ->orWhere('coc_no', 'like', '%' . $search . '%')
                ->orWhere('plate_no', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->latest()->paginate(10)->withQueryString();

        return view('pangalan-ng-view-mo', compact('logs'));
    }

    public function validateCoc(Request $request)
    {
        try {
            $cocNo = $request->query('coc_no');
            $classification = $request->query('classification');

            if (!$cocNo || !$classification) {
                return response()->json([
                    'valid' => false, 
                    'message' => 'Missing required validation parameters.'
                ]);
            }

            // Ginamit ang tamang column names base sa phpMyAdmin mo
            $coc = DB::table('coc_table')
                ->where('coc_no', $cocNo) // Inayos mula 'coc_number' -> 'coc_no'
                ->first();

            if (!$coc) {
                return response()->json([
                    'valid' => false, 
                    'message' => 'COC Number does not exist.'
                ]);
            }

            // Inayos mula 'type' -> 'coc_type'
            if (strtoupper($coc->coc_type) !== strtoupper($classification)) { 
                return response()->json([
                    'valid' => false, 
                    'message' => "This COC is registered for {$coc->coc_type}, not for {$classification}."
                ]);
            }

            // Inayos mula 'status' -> 'coc_status'
            // Kung ang nakalagay sa screenshot mo ay 'Used', ibig sabihin hindi na sya pwedeng gamitin.
            if (strtolower($coc->coc_status) === 'used') { 
                return response()->json([
                    'valid' => false, 
                    'message' => 'This COC Number is already used/issued.'
                ]);
            }

            // Kung hindi 'used' (halimbawa, 'Available' o blanko), pwede itong gamitin!
            return response()->json(['valid' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false, 
                'message' => 'Database Error: ' . $e->getMessage()
            ]);
        }
    }

    public function validatePolicy(Request $request)
    {
        try {
            $policyNo = $request->query('policy_no');

            if (!$policyNo) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Policy number is required.'
                ]);
            }

            // 🟢 Inayos ang table at column base sa iyong phpMyAdmin
            $policyExists = DB::table('ctpl_issuances') 
                ->where('policy_no', $policyNo)
                ->exists();

            if ($policyExists) {
                return response()->json([
                    'valid' => false,
                    'message' => 'This Policy Number already exists in the system.'
                ]);
            }

            return response()->json(['valid' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Database Error: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        // Dito natin ilalagay ang logic ng pag-save mamaya
    }

    public function logs(Request $request)
    {
        $search = trim($request->query('search'));
        
        // Default sa ngayong araw ang from at to kung walang pinili para mabilis mag-load
        $from = $request->query('from', Carbon::today()->format('Y-m-d'));
        $to = $request->query('to', Carbon::today()->format('Y-m-d'));

        $query = DB::table('ctpl_issuances')
            ->join('vehicles', 'ctpl_issuances.vehicle_id', '=', 'vehicles.vehicle_id')
            ->join('coc_table', 'ctpl_issuances.coc_id', '=', 'coc_table.coc_id')
            ->select(
                'ctpl_issuances.*', 
                'vehicles.assured', 
                'vehicles.plate_no', 
                'vehicles.file_no as mv_file', 
                'coc_table.coc_no'
            );

        // Filter ayon sa Date Range (From at To)
        if (!empty($from) && !empty($to)) {
            $query->whereBetween('ctpl_issuances.created_at', [
                $from . ' 00:00:00', 
                $to . ' 23:59:59'
            ]);
        } elseif (!empty($from)) {
            $query->whereDate('ctpl_issuances.created_at', '>=', $from);
        } elseif (!empty($to)) {
            $query->whereDate('ctpl_issuances.created_at', '<=', $to);
        }

        // Filter ayon sa search query
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('vehicles.assured', 'LIKE', '%' . $search . '%')
                ->orWhere('ctpl_issuances.agent', 'LIKE', '%' . $search . '%')
                ->orWhere('coc_table.coc_no', 'LIKE', '%' . $search . '%')
                ->orWhere('vehicles.plate_no', 'LIKE', '%' . $search . '%')
                ->orWhere('vehicles.file_no', 'LIKE', '%' . $search . '%');
            });
        }

        $logs = $query->orderBy('ctpl_issuances.created_at', 'desc')->paginate(10)->withQueryString();

        return view('ctpl.logs', compact('logs', 'from', 'to'));
    }
}
