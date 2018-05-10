<?php

namespace App\Http\Controllers;

use App\Runner;
use Illuminate\Http\Request;

class RunnerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin',['except'=>'getRunner']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $runners = Runner::all();
        return view('runner.index',['runners'=>$runners]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'running_text' => 'required',
        ]);

        $runner = new Runner;
        $runner->running_text = $request->running_text;
        $runner->save();

        return redirect()->route('runner.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Runner  $runner
     * @return \Illuminate\Http\Response
     */
    public function show(Runner $runner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Runner  $runner
     * @return \Illuminate\Http\Response
     */
    public function edit(Runner $runner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Runner  $runner
     * @return \Illuminate\Http\Response
     */
    public function getRunner()
    {

        $runner = Runner::where('use',1)->first();
        if ($runner) {
            return response()->json($runner->running_text);
        }else{
            return response()->json('歡迎光臨～');
        }
        
    }

    public function runnerUse(Request $request)
    {
        $runner = Runner::where('use',1)->first();
        if ($runner) {
            $runner->use = 0;
            $runner->save();
        }

        $runnerUse = Runner::find($request->use);
        $runnerUse->use = 1;
        $runnerUse->save();
        return response()->json($request->use);

    }

    public function update(Request $request, $id)
    {
        $runner = Runner::find($id);
        $runner->running_text = $request->running_text;
        $runner->save();

        return redirect()->route('runner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Runner  $runner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $runner = Runner::find($id);
        $runner->delete();
        return response()->json(['msg'=>'成功刪除']);
    }
}
