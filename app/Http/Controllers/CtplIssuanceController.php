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

    public function searchVehicle(Request $request)
    {
        $type = $request->query('type'); // plate_no, file_no, chassis_no, engine_no, assured, address
        $value = $request->query('value');

        if (!$value) {
            return response()->json(['success' => false, 'message' => 'Invalid parameters.']);
        }

        try {
            // Simulan sa 'vehicles' table para lumabas ang lahat ng sasakyan kahit wala pang ctpl_issuances record
            $query = DB::table('vehicles')
                        ->leftJoin('ctpl_issuances', 'vehicles.vehicle_id', '=', 'ctpl_issuances.vehicle_id');

            // Piliin ang mga kolum (gamitin ang latest o kung ano ang meron)
            $query->select('vehicles.*', 'ctpl_issuances.assured', 'ctpl_issuances.address');

            // I-filter depende sa search type
            if ($type === 'assured') {
                $query->where('ctpl_issuances.assured', 'LIKE', '%' . $value . '%');
            } elseif ($type === 'address') {
                $query->where('ctpl_issuances.address', 'LIKE', '%' . $value . '%');
            } elseif (in_array($type, ['plate_no', 'file_no', 'engine_no', 'chassis_no'])) {
                $query->where('vehicles.' . $type, 'LIKE', '%' . $value . '%');
            } else {
                $query->where('vehicles.plate_no', 'LIKE', '%' . $value . '%')
                    ->orWhere('vehicles.file_no', 'LIKE', '%' . $value . '%');
            }

            $results = $query->limit(10)->get();

            if ($results->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => $results
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No record found.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
        // 1. Validate ang mga input galing sa form
        $validated = $request->validate([
            'assured'      => 'required|string|max:100',
            'address'      => 'required|string|max:255',
            'denomination' => 'required|string|max:50',
            'year_model'   => 'required|integer|min:1900|max:2100',
            'make'         => 'required|string|max:50',
            'series'       => 'required|string|max:50',
            'color'        => 'required|string|max:50',
            'mv_file'      => 'required|string|max:20',
            'plate_no'     => 'required|string|max:20',
            'chassis_no'   => 'required|string|max:50',
            'engine_no'    => 'required|string|max:50',
            'coc_no'       => 'required|digits:8',
            'policy_no'    => 'required|string|max:20|unique:ctpl_issuances,policy_no',
            'agent'        => 'required|string|max:50',
            'amount'       => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 2. Check kung existing na ang vehicle gamit ang plate_no at file_no
            $vehicle = DB::table('vehicles')
                ->where('plate_no', strtoupper($validated['plate_no']))
                ->where('file_no', strtoupper($validated['mv_file']))
                ->first();

            // Mga column na kasama lang sa vehicles table mo
            $vehicleData = [
                'year_model'   => $validated['year_model'],
                'make'         => strtoupper($validated['make']),
                'series'       => strtoupper($validated['series']),
                'denomination' => $validated['denomination'], // Dito natin ilalagay ang denomination
                'color'        => strtoupper($validated['color']),
                'chassis_no'   => strtoupper($validated['chassis_no']),
                'engine_no'    => strtoupper($validated['engine_no']),
                'updated_at'   => now(),
            ];

            if ($vehicle) {
                // Kung meron na, i-update ang details nito
                DB::table('vehicles')
                    ->where('vehicle_id', $vehicle->vehicle_id)
                    ->update($vehicleData);
                
                $vehicleId = $vehicle->vehicle_id;
            } else {
                // Kung wala pa, mag-i-insert ng bago
                $vehicleData['file_no'] = strtoupper($validated['mv_file']);
                $vehicleData['plate_no'] = strtoupper($validated['plate_no']);
                $vehicleData['created_at'] = now();
                
                $vehicleId = DB::table('vehicles')->insertGetId($vehicleData);
            }

            // 3. Hanapin ang COC sa coc_table gamit ang coc_no
            $coc = DB::table('coc_table')->where('coc_no', $validated['coc_no'])->first();

            if (!$coc) {
                throw new \Exception('The selected COC Number ('.$validated['coc_no'].') does not exist in the database.');
            }

            // 4. I-save sa ctpl_issuances table
            DB::table('ctpl_issuances')->insert([
                'assured'      => strtoupper($validated['assured']),
                'address'      => strtoupper($validated['address']),
                'policy_no'    => $validated['policy_no'],
                'agent'        => strtoupper($validated['agent']),
                'amount'       => $validated['amount'],
                'coc_id'       => $coc->coc_id,
                'vehicle_id'   => $vehicleId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // 5. Baguhin ang status ng COC patungong Used
            DB::table('coc_table')
                ->where('coc_id', $coc->coc_id)
                ->update([
                    'coc_status' => 'Used',
                    'updated_at' => now()
                ]);

            DB::commit();

            return redirect()->back()->with('success', 'CTPL Policy successfully issued!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Database Error: ' . $e->getMessage());
        }
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
                'ctpl_issuances.transaction_id as id', // <--- Pinalitan ng 'issuance_id as id'. (Kung sakaling 'id' talaga ang column, tanggalin ito dahil kasama na sa ctpl_issuances.*)
                'ctpl_issuances.*', 
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
                $q->where('ctpl_issuances.assured', 'LIKE', '%' . $search . '%')
                ->orWhere('ctpl_issuances.agent', 'LIKE', '%' . $search . '%')
                ->orWhere('coc_table.coc_no', 'LIKE', '%' . $search . '%')
                ->orWhere('vehicles.plate_no', 'LIKE', '%' . $search . '%')
                ->orWhere('vehicles.file_no', 'LIKE', '%' . $search . '%');
            });
        }

        $logs = $query->orderBy('ctpl_issuances.created_at', 'desc')->paginate(10)->withQueryString();

        return view('ctpl.logs', compact('logs', 'from', 'to'));
    }

    public function showResult($id)
    {
        // Kunin ang policy at ang mga detalye ng sasakyan gamit ang transaction_id
        $policy = DB::table('ctpl_issuances')
            ->join('vehicles', 'ctpl_issuances.vehicle_id', '=', 'vehicles.vehicle_id')
            ->join('coc_table', 'ctpl_issuances.coc_id', '=', 'coc_table.coc_id')
            ->select(
                'ctpl_issuances.*', 
                'vehicles.plate_no',
                'vehicles.year_model', 
                'vehicles.make', 
                'vehicles.color', 
                'vehicles.chassis_no', 
                'vehicles.engine_no', 
                'vehicles.denomination', // <--- Dito natin binabasa ang uri/klase ng sasakyan
                'vehicles.file_no as mv_file', 
                'coc_table.coc_no'
            )
            ->where('ctpl_issuances.transaction_id', $id)
            ->first();

        if (!$policy) {
            abort(404, 'Policy not found.');
        }
        
        // Kunin at gawing uppercase ang denomination para madaling i-match
        $deniv = strtoupper(trim($policy->denomination ?? ''));
        $type = 'result'; // default fallback

        // Logic para sa pagtukoy ng folder batay sa ibinigay mong mapping
        if (str_contains($deniv, 'MC') || str_contains($deniv, 'MTC')) {
            $type = 'mc';
        } elseif (
            str_contains($deniv, 'CAR') || 
            str_contains($deniv, 'SEDAN') || 
            str_contains($deniv, 'PASSENGER') || 
            str_contains($deniv, 'HATCHBACK') || 
            str_contains($deniv, 'UTILITY') || 
            str_contains($deniv, 'SUV') || 
            str_contains($deniv, 'COUPE')
        ) {
            $type = 'pc';
        } elseif (str_contains($deniv, 'TRICYCLE')) {
            $type = 'tc';
        } elseif (str_contains($deniv, 'TRUCK') || str_contains($deniv, 'TRAILER')) {
            $type = 'cv';
        }

        // I-check kung umiiral ang view sa tamang folder (resources/views/ctpl/{type}/show.blade.php)
        $viewName = "ctpl.{$type}.show";
        
        if (view()->exists($viewName)) {
            return view($viewName, compact('policy'));
        }

        // Fallback kung sakaling wala o hindi pasok sa kategorya
        return view('ctpl.result', compact('policy'));
    }
}
