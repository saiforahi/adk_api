<?php

namespace App\Http\Controllers\v1\TycoonPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockRequest;
use App\Models\{ DealerProductStock, TycoonBonusConfig, AdminStock, TycoonWallet, Tycoon, ProductStockOrder, Admin };
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\v1\CommissionDistributionEvent;

use DB;

class OrderController extends Controller
{

    public function _store_order(Request $req)
    {
        DB::beginTransaction();

        try{
            $req->validate([
                'products'=> 'required'
            ]);
            if(Auth::user()->wallet && Auth::user()->wallet->product_balance > (float)$req->totalAmount){
                foreach($req->products as $product){
                    $new_order = ProductStockOrder::create([
                        'order_id'=> $this->orderId(),
                        'product_id'=>$product['product_id'],
                        'qty'=> $product['quantity'],
                        'status'=> 'APPROVED',
                        'order_notes'=> 'null'
                    ]);
                    $new_order->order_from()->associate(Auth::user());
                    $new_order->order_to()->associate(Admin::first());
                    $new_order->save();
                }
                $dat = TycoonWallet::where('tycoon_id',Auth::user()->id)->update([
                    'product_balance'=> Auth::user()->wallet->product_balance-(float)$req->totalAmount
                ]);
                $tycoon_bonus = TycoonBonusConfig::where('bonus_type', 'instant_sale')->first();
                $bonus = [
                    'wallet_type' => 'sales_commission',
                    'bonus_type' => 'instant_sale',
                    'amount' => ($req->totalAmount * $tycoon_bonus->bonus_percentage) / 100,
                    'tycoon_id' => auth()->user()->reference_id ? auth()->user()->reference_id : 1
                ];
                event(new CommissionDistributionEvent($bonus));
                DB:: commit();
                return $this->success($req->all(), 'Order successfully completed.');
            }
            else{
                return $this->failed(null,'Insuficient product balance');
            }
            
        }
        catch(Exception $e){
            DB::rollback();

            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    private function orderId () {
        $orderId = 1000;
        $order = ProductStockOrder::orderBy('id', 'desc')->first();
        if ($order) {
            $orderId = $order->order_id + 1;
        }
        return $orderId;
    }
    /**
     * @param ProductStock $productStock
     * @return JsonResponse
     */
    public function show(ProductStock $productStock): JsonResponse
    {
        return $this->success($productStock);
    }

    // order list or requests for dealers 
    public function product_orders(): JsonResponse
    {
        try{
            $orders=array();
            $orders = ProductStockOrder::with(['product','order_to','order_from'])->whereHasMorph('order_from', Tycoon::class,function(Builder $query){
                $query->where('id', '=', Auth::user()->id);
            })->get();
            return $this->success($orders, 'Orders', 200);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function order_status_update(Request $req): JsonResponse
    {
        try{
            $req->validate([
                'order_id'=>'required',
                'status'=> 'required'
            ]);
            $order=ProductStockOrder::findOrFail($req->order_id);
            $order->status=$req->status;
            $order->save();
            
            switch($req->status){
                case 'PROCESSED':
                    $dealer_stock = DealerProductStock::create([
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

    
}
