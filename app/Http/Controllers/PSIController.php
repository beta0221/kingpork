<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\InventoryLog;
use App\Retailer;
use Illuminate\Http\Request;

class PSIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**index頁面 */
    public function index(){
        $inventories = Inventory::all();
        $inventoryLogs = InventoryLog::orderBy('id','desc')->paginate(10);
        $retailers = Retailer::all();

        return view('psi.index',[
            'retailers'=>$retailers,
            'inventories'=>$inventories,
            'inventoryLogs'=>$inventoryLogs,
            'inventoryDict'=>Inventory::nameDict(),
            'retailerDict'=>Retailer::nameDict()
        ]);
    }

    /**新增 */
    public function store(Request $request){
        
        $this->validate($request,[
            'inventory'=>'required',
            'event'=>'required',
            'date'=>'required',
            'action'=>'required',
            'retailer_id'=>'required_if:action,sale'
        ]);


        $inventoryLog = InventoryLog::insert_row($request);
        
        $sync = [];
        if($request->has('inventory')){
            foreach ($request->inventory as $inventory_id => $quantity) {
                if(!$quantity){ continue; }
                $sync[$inventory_id] = ['quantity'=>$quantity];
            }
        }
        $inventoryLog->inventories()->sync($sync);

        $action = null;
        switch ($request->action) {
            case 'sale':
                $action = Inventory::DECREASE; 
                break;
            case 'purchase':
                $action = Inventory::INCREASE;
                break;
            default:
                return response('error',500);
                break;
        }

        foreach ($request->inventory as $id => $quantity) {
            Inventory::updateAmount($id,$quantity,$action);
        }

        return response()->json(['m'=>'success']);

    }

    /**內容 */
    public function show(Request $request,$id){
        $log = InventoryLog::findOrFail($id);
        $inventories = $log->inventories()->get();
        $data = [];
        foreach ($inventories as $inventory) {
            $data[$inventory->pivot->inventory_id] = $inventory->pivot->quantity;
        }
        return response()->json($data);
    }

    /**回朔紀錄 */
    public function reverseInventoryLog(Request $request,$id){
        $log = InventoryLog::findOrFail($id);
        $inventories = $log->inventories()->get();

        $action = null;
        switch ($log->action) {
            case 'sale':
                $action = Inventory::INCREASE;
                break;
            case 'purchase':
                $action = Inventory::DECREASE; 
                break;
            default:
                return response('error',500);
                break;
        }

        foreach ($inventories as $inventory) {
            //更新 inventories
            Inventory::updateAmount($inventory->pivot->inventory_id,$inventory->pivot->quantity,$action);
        }

        //刪除 inventory_logs_inventory
        $log->inventories()->detach();

        //刪除 inventoryLog
        $log->delete();

        return response()->json(['m'=>'success']);

    }

    /**產出報表 */
    public function report(Request $request){

        $dateArray = InventoryLog::where('action','sale')
            ->select('date')
            ->groupBy('date')
            ->whereBetween('date',[$request->from_date,$request->to_date])
            ->pluck('date');

        $logs = InventoryLog::where('action','sale')
            ->whereIn('date',$dateArray)
            ->get();


        $data = [];
        foreach ($logs as $log) {
            
            if(!isset($data[$log->date][$log->retailer_id])){
                $data[$log->date][$log->retailer_id] = [];
            }

            $data[$log->date][$log->retailer_id][] = $log;
        }

        $retailers = Retailer::all();

        return view('psi.report',[
            'from_date'=>$request->from_date,
            'to_date'=>$request->to_date,
            'data'=>$data,
            'dateArray'=>$dateArray,
            'logs'=>$logs,
            'retailers'=>$retailers,
        ]);
    }


}
