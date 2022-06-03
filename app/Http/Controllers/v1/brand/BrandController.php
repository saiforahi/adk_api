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
            $data = $request->validated();
            $data['slug'] = Str::slug($data['name']);
            if ($request->file('logo')) {
                $filename = $request->file('logo')->store('brand', 'public');
                Storage::disk('local')->path($filename);
                $data['logo'] = $filename;
            }
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

    public function update(Brand $brand, Request $request)
    {

    }

    public function destroy(Brand $brand)
    {

    }
}
