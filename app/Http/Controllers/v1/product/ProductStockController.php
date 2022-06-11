<?php

namespace App\Http\Controllers\v1\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockRequest;
use App\Models\ProductStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $stocks = ProductStock::query()->latest()->get();
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
}
