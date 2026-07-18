<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CtplIssuanceController extends Controller
{
    public function create()
    {
        return view('ctpl.issuance');
    }

    /**
     * I-save ang CTPL Issuance Transaction (Ihahanda na natin para sa susunod)
     */
    public function store(Request $request)
    {
        // Dito natin ilalagay ang logic ng pag-save mamaya
    }
}
