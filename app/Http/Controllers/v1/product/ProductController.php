<?php

namespace App\Http\Controllers\v1\product;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['stock', 'category', 'brand'])->select('*','products.unit_price as price')->latest()->get();
        $products = $products->transform(function ($item) {
            return $this->getImages($item);
        });
        return $this->success($products);
    }

    /**
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), 'image');
            $data['slug'] = Str::slug($data['name']);
            $product = Product::query()->create($data);
            if ($request->hasFile('image')) {
                $this->uploadImage($request, $product);
            }
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
        $this->getImages($product);
        return $this->success($product->load('brand', 'category', 'sub_category', 'sub_sub_category'));
    }

    /**
     * @param Product $product
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function update(Product $product, ProductRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), 'image');
            $data['slug'] = Str::slug($data['name']);
            $product->update($data);
            $product->getMedia();
            if ($request->hasFile('image')) {
                $this->uploadImage($request, $product);
            }
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
        $product->getMedia();
        if ($product->media) {
            $product->media->each(function ($item) {
                $item->delete();
            });
        }
        $product->delete();
        return $this->success($product, 'Product Deleted Successfully');
    }

    protected function uploadImage(Request $request, $product)
    {
        if ($product->media) {
            $product->media->each(function ($item) {
                $item->delete();
            });
        }
        $images = [];
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $images[] = $request->file('image');
        }
        event(new UploadImageEvent($product, $images, 'image'));
    }

    protected function getImages($product)
    {
        $product['image'] = null;
        $product->getMedia();
        if ($product->media) {
            $product->media->each(function ($item) use ($product) {
                $product['image'] = $item->getFullUrl();
            });
        }
        $product->makeHidden('media');
        return $product;
    }

    public function all_stockable_products(){

    }
}