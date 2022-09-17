<?php

namespace App\Http\Controllers\v1\TycoonPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockRequest;
use App\Models\{ DealerProductStock, TycoonBonusConfig,TycoonGroupBonusConfig, AdminStock, TycoonWallet, Tycoon, ProductStockOrder, Admin, AdminWallet, Dealer, DealerWallet};
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\v1\CommissionDistributionEvent;
use App\Events\v1\DealerCommissionDistributionEvent;
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
                        'price'=> $product['price'],
                        'status'=> 'APPROVED',
                        'order_notes'=> 'null'
                    ]);
                    $total_amount = $product['quantity'] * $product['price'];

                    if($product['from'] == "adk"){
                        $admin_stock= AdminStock::where('product_id',$product['product_id'])->first();
                        if ($admin_stock->quantity < $product['quantity']) {
                            return $this->failed(null,'Not engounh quantity!');
                        }
                        $admin_stock->quantity-=floatval($product['quantity']);
                        $admin_stock->save();

                        $new_order->order_from()->associate(Auth::user());
                        $new_order->order_to()->associate(Admin::first());
                        $new_order->save();

                        AdminWallet::where('admin_id', 1)->update([
                            'stock_balance' => DB::raw('stock_balance-'. $total_amount),
                            'total_sale' => DB::raw('total_sale+'. $total_amount)
                        ]);

                    } else {
                        $dealer_stock= DealerProductStock::where(['product_id'=>$product['product_id'],'dealer_id'=>$product['dealer_id']])->first();
                        if ($dealer_stock->qty < $product['quantity']) {
                            return $this->failed(null,'Not engounh quantity!');
                        }
                        $dealer_stock->qty-= floatval($product['quantity']);
                        $dealer_stock->save();

                        $new_order->order_from()->associate(Auth::user());
                        $new_order->order_to()->associate(Dealer::find($product['dealer_id']));
                        $new_order->save();
                        
                        DealerWallet::where('dealer_id', $product['dealer_id'])->update([
                            'stock_balance' => DB::raw('stock_balance-'. $total_amount),
                            'sales_balance' => DB::raw('sales_balance+'. $total_amount)
                        ]);

                        //bonus distribution
                        $dealerbonus = [
                            'product_id' => $product['product_id'],
                            'amount' => $product['quantity'] * $product['price'],
                            'tycoon_id' => auth()->user()->id,
                            'from_dealer_id' => null,
                            'to_dealer_id' => $product['dealer_id']
                        ];
                        event(new DealerCommissionDistributionEvent($dealerbonus));
                    }

                    // bonus distribution
                    $bonus = [
                        'product_id' => $product['product_id'],
                        'bonus_type' => 'instant_sale',
                        'amount' => $product['quantity'] * $product['price'],
                        'from_tycoon_id' => auth()->user()->id,
                        'to_tycoon_id' => auth()->user()->reference_id ? auth()->user()->reference_id : 1,
                        'placement_id' => auth()->user()->placement_id ? auth()->user()->placement_id : 1
                    ];
                    event(new CommissionDistributionEvent($bonus));

                    // bonus distribution
                    $bonus = [
                        'product_id' => $product['product_id'],
                        'bonus_type' => 'group_bonus',
                        'amount' => $product['quantity'] * $product['price'],
                        'to_tycoon_id' => auth()->user()->reference_id ? auth()->user()->reference_id : 1,
                        'from_tycoon_id' => auth()->user()->id,
                        'placement_id' => auth()->user()->placement_id ? auth()->user()->placement_id : 1
                    ];
                    event(new CommissionDistributionEvent($bonus));
                }
                TycoonWallet::where('tycoon_id',Auth::user()->id)->update([
                    'product_balance'=> Auth::user()->wallet->product_balance-(float)$req->totalAmount
                ]);

                DB::commit();
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
