<?php

namespace App\Http\Controllers\v1\admin;

use App\Events\v1\DealerCommissionDistributionEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\product\ProductStockController;
use App\Models\Admin;
use App\Models\AdminStock;
use App\Models\AdminWallet;
use App\Models\Dealer;
use App\Models\DealerProductStock;
use App\Models\DealerType;
use App\Models\DealerWallet;
use App\Models\ProductStockOrder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
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
            foreach($orders as $history){
                switch($history->order_from_type){
                    case 'App\Models\Dealer':
                        $history['order_from']['from_type']='Dealer';
                        $history['order_from']['dealer_type']=DealerType::where('id',$history->order_from['dealer_type_id'])->first()->name;
                        break;
                    case 'App\Models\Admin':
                        $history['order_from']['from_type']='Admin';break;
                    case 'App\Models\Tycoon':
                        $history['order_from']['from_type']='Tycoon';break;
                    case 'App\Models\MasterTycoon':
                        $history['order_from']['from_type']='Master Tycoon';break;
                }
                switch($history->order_to_type){
                    case 'App\Models\Dealer':
                        $history['order_to']['to_type']='Dealer';
                        $history['order_to']['dealer_type']=DealerType::where('id',$history->order_to['dealer_type_id'])->first()->name;
                        break;
                    case 'App\Models\Admin':
                        $history['order_to']['to_type']='Admin';break;
                    case 'App\Models\Tycoon':
                        $history['order_to']['to_type']='Tycoon';break;
                    case 'App\Models\MasterTycoon':
                        $history['order_to']['to_type']='Master Tycoon';break;
                }

            }
            return $this->success($orders, 'Product Stock order requests',200);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function product_stock_order_status_update(Request $req): JsonResponse
    {
        DB::beginTransaction();
        try{;
            $req->validate([
                'order_id'=>'required',
                'status'=> 'required'
            ]);
            $order=ProductStockOrder::findOrFail($req->order_id);
            $orderDealer=ProductStockOrder::whereHasMorph('order_from', Dealer::class)->first();
            if ($orderDealer) {
                $total_sale_amount = ($order->price * $order->qty);
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
                            ['stock_balance' => DB::raw('stock_balance+'. ($order->price * $order->qty))]
                        );
                    break;
                }
            }
            $order->status = $req->status;
            $order->save();
            // admin order check
            $adminOrder=ProductStockOrder::whereHasMorph('order_to', Admin::class)->first();
            if ($adminOrder) {
                $total_sale_amount = ($order->price * $order->qty);
                AdminWallet::where('admin_id', auth()->user()->id)->update(
                    [
                        'stock_balance' => DB::raw('stock_balance-'.$total_sale_amount),
                        'total_sale' => DB::raw('total_sale+'.$total_sale_amount)
                    ]
                );
            }
            DB:: commit();
            return $this->success($order, 'Product Stock order status updated',200);
        }
        catch(Exception $e){
            DB::rollback();
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
