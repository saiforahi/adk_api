<?php

namespace App\Http\Controllers\v1\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            $data = $this->productData($request);
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
            $data = $this->productData($request, $product);
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
        if (Storage::disk('public')->exists($product->icon)) {
            Storage::disk('public')->delete($product->icon);
        }
        if (Storage::disk('public')->exists($product->banner)) {
            Storage::disk('public')->delete($product->banner);
        }
        $product->delete();
        return $this->success($product, 'Product Deleted Successfully');
    }

    /**
     * @param ProductRequest $request
     * @param Product|null $product
     * @return mixed
     */
    private function productData(ProductRequest $request, Product $product = null): mixed
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('icon')) {
            if ($product && $image = Storage::disk('public')) {
                $image->delete($product->icon);
            }
            $icon = $request->file('icon')->store('category', 'public');
            $data['icon'] = $icon;
        }
        return $data;
    }
}
