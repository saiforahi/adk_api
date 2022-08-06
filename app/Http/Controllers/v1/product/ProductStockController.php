<?php

namespace App\Http\Controllers\v1\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockRequest;
use App\Models\Dealer;
use App\Models\ProductStock;
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
        $stocks = ProductStock::leftJoin('products', 'product_stocks.product_id', 'products.id')
        ->select('product_stocks.*', 'products.name as product_name')
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
            $stock = ProductStock::query()->create($request->validated());
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
        return $this->success($productStock, 'Product Stock Deleted Successfully');
    }

    public function product_stock_orders(): JsonResponse
    {
        try{
            $orders = ProductStockOrder::with(['product','order_from' => function (MorphTo $morphTo) {
                $morphTo->constrain([
                    Dealer::class => function (Builder $query) {
                        $query->where('id', '=', Auth::user()->id);
                    },
                ]);
            }])->whereHasMorph('order_from',Dealer::class,function(Builder $query){
                $query->where('id', '=', Auth::user()->id);
            })->get();
            return $this->success($orders, 'Product Stock orders');
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
    public function product_stock_order_status_update(): JsonResponse
    {
        try{
            $orders = ProductStockOrder::with(['product','order_from' => function (MorphTo $morphTo) {
                $morphTo->constrain([
                    Dealer::class => function (Builder $query) {
                        $query->where('id', '=', Auth::user()->id);
                    },
                ]);
            }])->whereHasMorph('order_from',Dealer::class,function(Builder $query){
                $query->where('id', '=', Auth::user()->id);
            })->get();
            return $this->success($orders, 'Product Stock orders');
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
