<?php

namespace App\Http\Controllers\v1\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockRequest;
use App\Models\Dealer;
use App\Models\DealerProductStock;
use App\Models\AdminStock;
use App\Models\ProductStockOrder;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStockController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $stocks = AdminStock::leftJoin('products', 'admin_stocks.product_id', 'products.id')
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

    
}
