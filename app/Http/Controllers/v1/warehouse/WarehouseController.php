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
        $warehouses = $warehouses->transform(function ($item, $key) {
            return $this->getImages($item);
        });
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
            if ($request->hasFile('image')) {
                $this->uploadImage($request, $warehouse);
            }
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
        $this->getImages($warehouse);
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
            $warehouse->getMedia();
            if ($request->hasFile('image')) {
                $this->uploadImage($request, $warehouse);
            }
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
        $warehouse->getMedia();
        if ($warehouse->media) {
            $warehouse->media->each(function ($item) {
                $item->delete();
            });
        }
        $warehouse->delete();
        return $this->success($warehouse, 'Warehouse Deleted Successfully');
    }

    protected function uploadImage(Request $request, $warehouse)
    {
        if ($warehouse->media) {
            $warehouse->media->each(function ($item) {
                $item->delete();
            });
        }
        $images = [];
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $images[] = $request->file('image');
        }
        event(new UploadImageEvent($warehouse, $images, 'image'));
    }

    protected function getImages($warehouse)
    {
        $warehouse['image'] = null;
        $warehouse->getMedia();
        if ($warehouse->media) {
            $warehouse->media->each(function ($item) use ($warehouse) {
                $warehouse['image'] = $item->getFullUrl();
            });
        }
        $warehouse->makeHidden('media');
        return $warehouse;
    }
}
