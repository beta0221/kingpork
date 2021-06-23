<?php

namespace App\Http\Controllers;

use App\InvoiceLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoiceLogs = InvoiceLog::orderBy('id','desc')->paginate(15);
        return view('admin.index',[
            'invoiceLogs'=>$invoiceLogs
        ]);
    }
}
