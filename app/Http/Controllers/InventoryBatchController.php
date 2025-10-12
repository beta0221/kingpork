<?php

namespace App\Http\Controllers;

use App\InventoryBatch;
use App\Inventory;
use Illuminate\Http\Request;

class InventoryBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = InventoryBatch::with('inventory');

        // 篩選特定庫存
        if ($request->has('inventory_id') && $request->inventory_id) {
            $query->where('inventory_id', $request->inventory_id);
        }

        $batches = $query->orderBy('manufactured_date', 'desc')->get();
        $inventories = Inventory::orderBy('category')->orderBy('name')->get();

        return view('inventoryBatch.index', compact('batches', 'inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inventories = Inventory::orderBy('category')->orderBy('name')->get();
        return view('inventoryBatch.create', compact('inventories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'inventory_id' => 'required|exists:inventories,id',
            'batch_number' => 'required|string|max:50|unique:inventory_batches,batch_number',
            'quantity' => 'required|integer|min:0',
            'manufactured_date' => 'nullable|date',
        ]);

        InventoryBatch::create($request->all());

        return redirect()->route('inventoryBatch.index')
            ->with('success', '批號新增成功');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InventoryBatch  $inventoryBatch
     * @return \Illuminate\Http\Response
     */
    public function show(InventoryBatch $inventoryBatch)
    {
        return view('inventoryBatch.show', compact('inventoryBatch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InventoryBatch  $inventoryBatch
     * @return \Illuminate\Http\Response
     */
    public function edit(InventoryBatch $inventoryBatch)
    {
        $inventories = Inventory::orderBy('category')->orderBy('name')->get();
        return view('inventoryBatch.edit', compact('inventoryBatch', 'inventories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InventoryBatch  $inventoryBatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventoryBatch $inventoryBatch)
    {
        $this->validate($request, [
            'inventory_id' => 'required|exists:inventories,id',
            'batch_number' => 'required|string|max:50|unique:inventory_batches,batch_number,'.$inventoryBatch->id,
            'quantity' => 'required|integer|min:0',
            'manufactured_date' => 'nullable|date',
        ]);

        $inventoryBatch->update($request->all());

        return redirect()->route('inventoryBatch.index')
            ->with('success', '批號更新成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InventoryBatch  $inventoryBatch
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryBatch $inventoryBatch)
    {
        $inventoryBatch->delete();

        return redirect()->route('inventoryBatch.index')
            ->with('success', '批號刪除成功');
    }
}
