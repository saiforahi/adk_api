<?php

namespace App\Http\Controllers\v1\purchaseOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\AdminStock;
use App\Models\AdminWallet;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $purchaseOrders = PurchaseOrder::with('details.product:id,name','details:id,purchase_order_id,product_id,req_quantity,cost,total_amount')->leftJoin('suppliers', 'purchase_orders.supplier_id', 'suppliers.id')
        ->leftJoin('warehouses', 'purchase_orders.warehouse_id', 'warehouses.id')
        ->select('purchase_orders.*', DB::raw('CONCAT(suppliers.first_name, " ", suppliers.last_name) AS supplier_name'), 'warehouses.name as warehouse_name')->get();
        return $this->success($purchaseOrders);
    }
    /**
     * @param PurchaseOrderRequest $request
     * @return JsonResponse
     */
    public function store(PurchaseOrderRequest $request)
    {

        DB::beginTransaction();

        
        $details = collect($request->details);
        $total_amount = $details->sum('total_amount');

        if(Auth::user()->wallet && Auth::user()->wallet->product_balance < (float)$total_amount){
            return $this->failed(null,'Insuficient product balance');
        }
        
        try {
            $data = $this->purchaseOrderData($request);
            $purchaseOrder = PurchaseOrder::create($data);
            $purchaseOrder->details()->createMany($request->details);
            // update or create stock
            foreach ($request->details as $item) {
                $oldStock = AdminStock::where('product_id', $item['product_id'])->first();
                if ($oldStock) {
                    $oldStock->update([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['req_quantity'] + $oldStock->quantity
                        ]);
                } else {
                    AdminStock::create([
                        'product_id' => $item['product_id'],  
                        'quantity' => $item['req_quantity'],
                        ]);
                }
            }
            AdminWallet::updateOrInsert(
                ['admin_id' => auth()->user()->id],
                [
                 'product_balance' => DB::raw('product_balance-'. $total_amount),
                 'stock_balance' => DB::raw('stock_balance+'. $total_amount)
                ],
            );
            DB::commit();

            return $this->success($purchaseOrder);
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param PurchaseOrder $purchaseOrder
     * @return JsonResponse
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        return $this->success($purchaseOrder->load('sub_category'));
    }

    /**
     * @param PurchaseOrder $purchaseOrder
     * @param PurchaseOrderRequest $request
     * @return JsonResponse
     */
    public function update(PurchaseOrder $purchaseOrder, PurchaseOrderRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $this->purchaseOrderData($request, $purchaseOrder);
            $purchaseOrder->update($data);
            PurchaseOrderDetail::where('purchase_order_id', $purchaseOrder->id)->delete();
            $purchaseOrder->details()->createMany($request->details);
            // update or create stock
            foreach ($request->details as $item) {
                $oldStock = AdminStock::where('product_id', $item['product_id'])->first();
                if ($oldStock) {
                    $oldStock->update([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['req_quantity'] + $oldStock->quantity
                        ]);
                } else {
                    AdminStock::create([
                        'product_id' => $item['product_id'],  
                        'quantity' => $item['req_quantity'],
                        ]);
                }
            }
            DB::commit();

            return $this->success($purchaseOrder);
        } catch (\Exception $exception) {

            DB::rollback();
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param PurchaseOrder $purchaseOrder
     * @return JsonResponse
     */
    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->delete();
        return $this->success($purchaseOrder, 'Purchase Order Deleted Successfully');
    }

    /**
     * @param PurchaseOrderRequest $request
     * @param PurchaseOrder|null $purchaseOrder
     * @return mixed
     */
    private function purchaseOrderData(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder = null): mixed
    {
        $data = $request->validated();
        return $data;
    }
}
