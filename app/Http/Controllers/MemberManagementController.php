<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class MemberManagementController extends Controller
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
     * Display a listing of the members.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::orderBy('id', 'desc');

        // 姓名搜尋（模糊比對）
        if($request->has('name') && $request->name != '') {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Email 搜尋（模糊比對）
        if($request->has('email') && $request->email != '') {
            $query->where('email', 'LIKE', '%' . $request->email . '%');
        }

        $members = $query->paginate(20);

        return view('admin.members.index', compact('members'));
    }
}
