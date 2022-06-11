<?php

namespace App\Http\Controllers\v1\brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $brands = Brand::query()->get();
        return $this->success($brands);
    }

    /**
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function store(BrandRequest $request): JsonResponse
    {
        try {
            $data = $this->brandData(null, $request);
            $brand = Brand::query()->create($data);
            return $this->success($brand);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Brand $brand
     * @return JsonResponse
     */
    public function show(Brand $brand): JsonResponse
    {
        return $this->success($brand);
    }

    /**
     * @param Brand $brand
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function update(Brand $brand, BrandRequest $request): JsonResponse
    {
        try {
            $data = $this->brandData($brand, $request);
            $brand->update($data);
            return $this->success($brand);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand): JsonResponse
    {
        if ($image = Storage::disk('public')) {
            $image->delete($brand->logo);
        }
        $brand->delete();
        return $this->success($brand, 'Brand Deleted Successfully');
    }

    /**
     * @param Brand|null $brand
     * @param BrandRequest $request
     * @return mixed
     */
    private function brandData(Brand $brand = null, BrandRequest $request): mixed
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('logo')) {
            if ($brand && $image = Storage::disk('public')) {
                $image->delete($brand->logo);
            }
            $filename = $request->file('logo')->store('brand', 'public');
            Storage::disk('public')->path($filename);
            $data['logo'] = $filename;
        }
        return $data;
    }
}
