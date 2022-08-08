<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ProductStockOrder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
}
