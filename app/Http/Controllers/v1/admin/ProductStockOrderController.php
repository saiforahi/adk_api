<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\product\ProductStockController;
use App\Models\Admin;
use App\Models\AdminStock;
use App\Models\DealerProductStock;
use App\Models\ProductStockOrder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStockOrderController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except([]);
        
    }
    public function product_stock_orders(): JsonResponse
    {
        try{
            $orders = ProductStockOrder::with(['product','order_to','order_from'])->whereHasMorph('order_to',Admin::class,function(Builder $query){
                $query->where('id', '=', Auth::user()->id);
            })->get();
            return $this->success($orders, 'Product Stock order requests',200);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function product_stock_order_status_update(Request $req): JsonResponse
    {
        try{
            // return redirect()->action(
            //     [ProductStockController::class, 'product_stock_order_status_update'], [$req->all()]
            // );
            $req->validate([
                'order_id'=>'required',
                'status'=> 'required'
            ]);
            $order=ProductStockOrder::findOrFail($req->order_id);
            $order->status=$req->status;
            $order->save();
            
            switch($req->status){
                case 'PROCESSED':
                    $dealer_stock=DealerProductStock::create([
                        'product_id'=> $order->product_id,
                        'dealer_id'=> $order->order_from->id,
                        'fk_order_id'=> $order->order_id,
                        'qty'=> $order->qty
                    ]);
                    break;
            }
            
            return $this->success($order, 'Product Stock order status updated',200);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function admin_product_stocks(){
        try{
            $stocks=AdminStock::with('product')->get();
            return $this->success($stocks, 'Admin Product Stocks',200);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
