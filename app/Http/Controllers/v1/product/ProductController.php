<?php

namespace App\Http\Controllers\v1\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()->latest()->get();
        return $this->success($products);
    }

    /**
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['name']);
            $product = Product::query()->create($data);
            return $this->success($product);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return $this->success($product);
    }

    /**
     * @param Product $product
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function update(Product $product, ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['name']);
            $product->update($data);
            return $this->success($product);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return $this->success($product, 'Product Deleted Successfully');
    }
}
