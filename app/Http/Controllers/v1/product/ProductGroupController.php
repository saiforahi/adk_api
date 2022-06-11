<?php

namespace App\Http\Controllers\v1\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductGroupRequest;
use App\Models\ProductGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $productGroups = ProductGroup::query()->orderByDesc('id')->get();
        return $this->success($productGroups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductGroupRequest $request
     * @return JsonResponse
     */
    public function store(ProductGroupRequest $request): JsonResponse
    {
        try {

            $productGroup = ProductGroup::query()->create($request->validated());
            return $this->success($productGroup);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ProductGroup $productGroup
     * @return JsonResponse
     */
    public function show(ProductGroup $productGroup): JsonResponse
    {
        return $this->success($productGroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductGroupRequest $request
     * @param ProductGroup $productGroup
     * @return JsonResponse
     */
    public function update(ProductGroupRequest $request, ProductGroup $productGroup): JsonResponse
    {
        try {
            $productGroup->update($request->validated());
            return $this->success($productGroup);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductGroup $productGroup
     * @return JsonResponse
     */
    public function destroy(ProductGroup $productGroup): JsonResponse
    {
        $productGroup->delete();
        return $this->success($productGroup, 'Product Group Deleted Successfully');
    }
}
