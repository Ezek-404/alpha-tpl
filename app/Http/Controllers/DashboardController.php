<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * I-display ang Dashboard kasama ang mga COC Statistics.
     */
    public function index(): View
    {
        // Kunin ang bilang ng Available at Used kada COC Type
        $cocStats = DB::table('coc_table')
            ->select('coc_type', 'coc_status', DB::raw('count(*) as total'))
            ->groupBy('coc_type', 'coc_status')
            ->get();

        // I-initialize ang array para sa mga uri (MC, PC, TC, CV)
        $types = ['MC', 'PC', 'TC', 'CV'];
        $data = [];

        foreach ($types as $type) {
            $data[$type] = [
                'available' => $cocStats->where('coc_type', $type)->where('coc_status', 'Available')->first()->total ?? 0,
                'used'      => $cocStats->where('coc_type', $type)->where('coc_status', 'Used')->first()->total ?? 0,
            ];
        }

        // Ibalik ang view kasama ang malinis na data array
        return view('dashboard', compact('data'));
    }
}