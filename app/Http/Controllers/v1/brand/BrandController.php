<?php

namespace App\Http\Controllers\v1\brand;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        $brands = Brand::query()->latest()->get();
        $brands = $brands->transform(function ($item, $key) {
            $this->getImages($item);
            return $item;
        });
        return $this->success($brands);
    }

    /**
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function store(BrandRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), 'logo');
            $data['slug'] = Str::slug($data['name']);
            $brand = Brand::query()->create($data);
            $this->uploadImage($request, $brand);
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
        $this->getImages($brand);
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
            $data = Arr::except($request->validated(), 'logo');
            $data['slug'] = Str::slug($data['name']);
            $brand->update($data);
            $this->uploadImage($request, $brand);
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
        try {
            $logos = $brand->getMedia('logo');
            $logos?->each(function ($item) {
                $item->delete();
            });
            $brand->delete();
            return $this->success($brand, 'Brand Deleted Successfully');
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage());
        }
    }

    protected function uploadImage(Request $request, $brand): void
    {
        $logos = $brand->getMedia('logo');
        $logos?->each(function ($item) {
            $item->delete();
        });
        $logos = [];
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logos[] = $request->file('logo');
            event(new UploadImageEvent($brand, $logos, 'logo'));
        }
    }

    protected function getImages($brand): mixed
    {
        $brand['logo_url'] = null;

        $logos = $brand->getMedia('logo');
        $logos?->each(function ($item) use ($brand) {
            $brand['logo_url'] = $item->getFullUrl();
        });
        $brand->makeHidden('media');
        return $brand;
    }
}
