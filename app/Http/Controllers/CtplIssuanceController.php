<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        
        $query = DB::table('ctpl_issuances');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('assured', 'LIKE', '%' . $search . '%') // Dito ito mahahanap
                ->orWhere('agent', 'LIKE', '%' . $search . '%')
                ->orWhere('coc_no', 'LIKE', '%' . $search . '%')
                ->orWhere('plate_no', 'LIKE', '%' . $search . '%')
                ->orWhere('mv_file', 'LIKE', '%' . $search . '%');
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('ctpl.logs', compact('logs'));
    }
}
