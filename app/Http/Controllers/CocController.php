<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CocController extends Controller
{
    public function index(Request $request)
    {
        // Kukunin ang search keyword mula sa URL string
        $search = $request->input('search', '');

        // Query papuntang database na may pagination (10 items per page)
        $cocs = DB::table('coc_table')
            ->where(function($query) use ($search) {
                if (!empty($search)) {
                    $query->where('coc_no', 'like', '%' . $search . '%')
                          ->orWhere('coc_type', 'like', '%' . $search . '%')
                          ->orWhere('coc_status', 'like', '%' . $search . '%');
                }
            })
            ->orderBy('coc_id', 'desc')
            ->paginate(10)
            ->withQueryString(); // Pinapanatili ang URL parameters sa tuwing lilipat ng page

        return view('coc.index', compact('cocs', 'search'));
    }

    public function checkAvailability(Request $request)
    {
        try {
            $start = $request->query('start');
            $end = $request->query('end');

            if (!$start || !$end) {
                return response()->json([
                    'available' => false, 
                    'message' => 'Kailangan ang start at end series.'
                ], 400);
            }

            // PINALITAN: Mula 'cocs' ginawang 'coc_table'
            $exists = DB::table('coc_table')
                ->whereBetween('coc_no', [$start, $end])
                ->exists();

            if ($exists) {
                return response()->json([
                    'available' => false, 
                    'message' => 'May umiiral na (existing) COC number sa range na ito.'
                ]);
            }

            return response()->json([
                'available' => true,
                'message' => 'Available ang series range na ito!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => 'Backend Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para sa pagsusumite ng "Save Series" form
     */
    public function store(Request $request)
    {
        $request->validate([
            'coc_type' => 'required|in:MC,PC,TC,CV',
            'series_start' => 'required|integer',
            'series_end' => 'required|integer|gte:series_start',
        ]);

        $start = $request->series_start;
        $end = $request->series_end;
        $type = $request->coc_type;

        // PINALITAN: Mula 'cocs' ginawang 'coc_table'
        $exists = DB::table('coc_table')
            ->whereBetween('coc_no', [$start, $end])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'series_start' => 'Hindi natuloy ang pag-save dahil may umiiral na COC sa range na pinili mo.'
            ])->withInput();
        }

        DB::transaction(function () use ($start, $end, $type) {
            $data = [];
            for ($i = $start; $i <= $end; $i++) {
                $data[] = [
                    'coc_no' => $i,
                    'coc_type' => $type,
                    'coc_status' => 'Available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($data) === 500) {
                    // PINALITAN: Mula 'cocs' ginawang 'coc_table'
                    DB::table('coc_table')->insert($data);
                    $data = [];
                }
            }

            if (count($data) > 0) {
                // PINALITAN: Mula 'cocs' ginawang 'coc_table'
                DB::table('coc_table')->insert($data);
            }
        });

        return redirect()->back()->with('success', 'Matagumpay na nagawa ang COC Series!');
    }

    public function deleteSeries(Request $request)
    {
        // 1. Validation ng inputs
        $request->validate([
            'delete_start' => 'required|integer',
            'delete_end' => 'required|integer|gte:delete_start',
        ]);

        $start = $request->delete_start;
        $end = $request->delete_end;

        // 2. CRITICAL CHECK: May gumamit na ba sa sakop ng range na ito?
        // Chine-check kung may kahit isang row na may status na 'Used' sa pagitan ng start at end
        $hasUsedRecords = DB::table('coc_table')
            ->whereBetween('coc_no', [$start, $end])
            ->where('coc_status', 'Used')
            ->exists();

        // Kung may nahanap na 'Used', hihinto dito at babalik sa view kasama ang error message
        if ($hasUsedRecords) {
            return redirect()->back()->withErrors([
                'delete_error' => 'Hindi pwedeng burahin ang seryeng ito dahil may mga COC na nagamit (Used) na sa loob ng range.'
            ])->withInput();
        }

        // 3. TRANSACTION: Kung malinis (puro Available), tuluyan nang buburahin
        $deletedCount = DB::transaction(function () use ($start, $end) {
            return DB::table('coc_table')
                ->whereBetween('coc_no', [$start, $end])
                ->where('coc_status', 'Available') // Double protection
                ->delete();
        });

        // Kung walang nabura (halimbawa, maling serye o hindi umiiral sa DB)
        if ($deletedCount === 0) {
            return redirect()->back()->withErrors([
                'delete_error' => 'Walang nahanap na umiiral na COC numbers sa range na iyong inilagay.'
            ])->withInput();
        }

        // 4. SUCCESS: Kapag natapos nang walang error
        return redirect()->back()->with('success', "Matagumpay na nabura ang {$deletedCount} na mga magkakasunod na COC series sa database!");
    }
}