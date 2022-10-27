<?php

namespace App\Http\Controllers\v1\admin;

use App\Events\v1\DealerCommissionDistributionEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\product\ProductStockController;
use App\Models\Admin;
use App\Models\AdminStock;
use App\Models\AdminWallet;
use App\Models\Dealer;
use App\Models\DealerBonusConfig;
use App\Models\DealerProductStock;
use App\Models\DealerWallet;
use App\Models\ProductStockOrder;
use App\Models\TycoonProduct;
use App\Models\TycoonWallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;

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
        FacadesDB::beginTransaction();
        try{;
            $req->validate([
                'order_id'=>'required',
                'status'=> 'required'
            ]);
            $order=ProductStockOrder::findOrFail($req->order_id);
            $total_sale_amount = ($order->price * $order->qty);
            // $orderDealer=ProductStockOrder::whereHasMorph('order_from', Dealer::class)->first();
            if ($order) {
                if ($order->order_from_type=="App\Models\Dealer"){
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
                            
                            switch(Dealer::firstWhere('id',$order->order_from->id)->dealer_type_id){
                                case 1:
                                    break;
                                case 2:
                                    $percentage = DealerBonusConfig::where('id',1)->sum('commission');
                                    $total_bonus = ($total_sale_amount * $percentage) / 100;
                                    AdminWallet::where('admin_id', auth()->user()->id)->update(
                                        [
                                            'sales_commission' => FacadesDB::raw('sales_commission+'.$total_bonus),
                                        ]
                                    );
                                    break;
                                case 3:
                                    $percentage = DealerBonusConfig::whereIn('id',[1,2])->sum('commission');
                                    $total_bonus = ($total_sale_amount * $percentage) / 100;
                                    AdminWallet::where('admin_id', auth()->user()->id)->update(
                                        [
                                            'sales_commission' => FacadesDB::raw('sales_commission+'.$total_bonus),
                                        ]
                                    );
                                    break;
                            }
                        break;
                    }
                }
                elseif($order->order_from_type=="App\Models\Tycoon"){
                    switch($req->status){
                        case 'PROCESSED':
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
                            // saving admin profit
                            $percentage = DealerBonusConfig::all()->sum('commission');
                            $total_bonus = ($total_sale_amount * $percentage) / 100;
                            AdminWallet::where('admin_id', auth()->user()->id)->update(
                                [
                                    'sales_commission' => FacadesDB::raw('sales_commission+'.$total_bonus),
                                ]
                            );
                        break;
                    }
                }
                
                $order->status = $req->status;
                $order->save();
            }
            
            FacadesDB:: commit();
            return $this->success($order, 'Product Stock order status updated',200);
        }
        catch(Exception $e){
            FacadesDB::rollback();
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
