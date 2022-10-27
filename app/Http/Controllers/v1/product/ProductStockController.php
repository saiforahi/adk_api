<?php

namespace App\Http\Controllers\v1\product;

use App\Events\v1\DealerCommissionDistributionEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockRequest;
use App\Models\Dealer;
use App\Models\DealerProductStock;
use App\Models\AdminStock;
use App\Models\DealerWallet;
use App\Models\ProductStock;
use App\Models\ProductStockOrder;
use App\Models\TycoonProduct;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;

class ProductStockController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $stocks = AdminStock::leftJoin('products', 'admin_stocks.product_id', 'products.id')->where('admin_stocks.quantity','!=',0)
        ->select('admin_stocks.*', 'products.name as product_name')
        ->latest()->get();
        return $this->success($stocks);
    }
    // dealer stock
    public function dealerStock(Request $request): JsonResponse
    {
        $stocks = DealerProductStock::leftJoin('products', 'admin_stocks.product_id', 'products.id')
        ->select('admin_stocks.*', 'products.name as product_name')
        ->latest()->get();
        return $this->success($stocks);
    }

    /**
     * @param ProductStockRequest $request
     * @return JsonResponse
     */
    public function store(ProductStockRequest $request): JsonResponse
    {
        try {
            $stock = AdminStock::query()->create($request->validated());
            return $this->success($stock);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param ProductStock $productStock
     * @return JsonResponse
     */
    public function show(ProductStock $productStock): JsonResponse
    {
        return $this->success($productStock);
    }

    /**
     * @param ProductStock $productStock
     * @param ProductStockRequest $request
     * @return JsonResponse
     */
    public function update(ProductStock $productStock, ProductStockRequest $request): JsonResponse
    {
        try {
            $productStock->update($request->validated());
            return $this->success($productStock);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param ProductStock $productStock
     * @return JsonResponse
     */
    public function destroy(ProductStock $productStock): JsonResponse
    {
        $productStock->delete();
        return $this->success($productStock, 'Product Stock Deleted Successfully',200);
    }
    
    // order list or requests for dealers 
    public function product_stock_orders($type): JsonResponse
    {
        try{
            $orders=array();
            switch($type){
                case 'history':
                    $orders = ProductStockOrder::with(['product','order_to','order_from'])->whereHasMorph('order_from',Dealer::class,function(Builder $query){
                        $query->where('id', '=', Auth::user()->id);
                    })->get();
                    break;
                
                case 'requests':
                    $orders = ProductStockOrder::with(['product','order_to','order_from'])->whereHasMorph('order_to',Dealer::class,function(Builder $query){
                        $query->where('id', '=', Auth::user()->id);
                    })->get();
                    break;
            }
            
            return $this->success($orders, 'Product Stock orders',200);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function product_stock_order_status_update(Request $req): JsonResponse
    {
        FacadesDB::beginTransaction();
        try{
            $req->validate([
                'order_id'=>'required',
                'status'=> 'required'
            ]);
            $order=ProductStockOrder::findOrFail($req->order_id);
            $total_sale_amount = ($order->price * $order->qty);

            if ($order) {
                if($order->order_from_type == "App\Models\Dealer"){
                    switch($req->status){
                        case 'PROCESSED':
                            $stock=DealerProductStock::where(['product_id'=> $order->product_id,'dealer_id'=> $order->order_from->id])->first();
                            if($stock){
                                $stock->qty += (int)$order->qty;
                                $stock->save();
                            }
                            else{
                                DealerProductStock::create([
                                    'product_id'=> $order->product_id,
                                    'dealer_id'=> $order->order_from->id,
                                    'fk_order_id'=> $order->order_id,
                                    'qty'=> $order->qty
                                ]);
                            }
                            DealerWallet::updateOrInsert(
                                ['dealer_id' => $order->order_from->id],
                                ['stock_balance' => FacadesDB::raw('stock_balance+'. ($order->price * $order->qty))]
                            );
                            
                        break;
                    }
                }
                elseif($order->order_from_type == "App\Models\Tycoon"){
                    $stock=TycoonProduct::where(['product_id'=> $order->product_id,'tycoon_id'=> $order->order_from->id])->first();
                    if($stock){
                        $stock->qty += (int)$order->qty;
                        $stock->save();
                    }
                    else{
                        TycoonProduct::create([
                            'product_id'=> $order->product_id,
                            'tycoon_id'=> $order->order_from->id,
                            'fk_order_id'=> $order->order_id,
                            'qty'=> $order->qty
                        ]);
                    }
                }
            }

            $order->status = $req->status;
            $order->save();
            //bonus distribution
            $bonus = [
                'product_id' => $order->product_id,
                'amount' => $order->qty * $order->price,
                'from_dealer_id' => $order->order_from->id,
                'to_dealer_id' => $order->order_to->id,
                'tycoon_id' => null,
            ];
            event(new DealerCommissionDistributionEvent($bonus));
            // admin order check
            DealerWallet::where('dealer_id', auth()->user()->id)->update(
                [
                    'stock_balance' => FacadesDB::raw('stock_balance-'.$total_sale_amount),
                    'sales_balance' => FacadesDB::raw('sales_balance+'.$total_sale_amount)
                ]
            );
            FacadesDB:: commit();
            return $this->success($order, 'Product Stock order status updated',200);
        }
        catch(Exception $e){
            FacadesDB::rollback();
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    
}
