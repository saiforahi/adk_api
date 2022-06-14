<?php

namespace App\Http\Controllers\v1\warehouse;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WarehouseController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $warehouses = Warehouse::query()->get();
        return $this->success($warehouses);
    }

    /**
     * @param WarehouseRequest $request
     * @return JsonResponse
     */
    public function store(WarehouseRequest $request): JsonResponse
    {
        try {
            $warehouse = Warehouse::query()->create(Arr::except($request->validated(), 'image'));
            $this->uploadImage($request, $warehouse);
            return $this->success($warehouse);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Warehouse $warehouse
     * @return JsonResponse
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->success($warehouse);
    }

    /**
     * @param Warehouse $warehouse
     * @param WarehouseRequest $request
     * @return JsonResponse
     */
    public function update(Warehouse $warehouse, WarehouseRequest $request): JsonResponse
    {
        try {
            $warehouse->update(Arr::except($request->validated(), 'image'));
            return $this->success($warehouse);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Warehouse $warehouse
     * @return JsonResponse
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();
        return $this->success($warehouse, 'Warehouse Deleted Successfully');
    }

    public function uploadImage(Request $request, $warehouse)
    {
        $images = [];
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $images[] = $request->file('image');
        }
        event(new UploadImageEvent($warehouse, $images, 'image'));
    }
}
