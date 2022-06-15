<?php

namespace App\Http\Controllers\v1\category;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $subCategories = SubCategory::with(['category', 'sub_sub_category'])->get();
        $subCategories = $subCategories->transform(function ($item, $key) {
            foreach ($item->getMedia('icon') as $media) {
                $item['icon_image_url'] = $media->getFullUrl();
            }
            foreach ($item->getMedia('banner') as $media) {
                $item['banner_image_url'] = $media->getFullUrl();
            }
            $item->makeHidden('media');
            return $item;
        });
        return $this->success($subCategories);
    }

    /**
     * @param SubCategoryRequest $request
     * @return JsonResponse
     */
    public function store(SubCategoryRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), ['icon', 'banner']);
            $data['slug'] = Str::slug($data['name']);
            $subCategory = SubCategory::query()->create($data);
            $this->uploadImage($request, $subCategory);
            return $this->success($subCategory);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        $this->getImages($subCategory);
        return $this->success($subCategory->load('category'));
    }

    /**
     * @param SubCategory $subCategory
     * @param SubCategoryRequest $request
     * @return JsonResponse
     */
    public function update(SubCategory $subCategory, SubCategoryRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), ['icon', 'banner']);
            $data['slug'] = Str::slug($data['name']);
            $subCategory->update($data);
            $this->uploadImage($request, $subCategory);
            return $this->success($subCategory);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param SubCategory $subCategory
     * @return JsonResponse
     */
    public function destroy(SubCategory $subCategory): JsonResponse
    {
        try {
            $icons = $subCategory->getMedia('icon');
            $banners = $subCategory->getMedia('banner');
            $icons?->each(function ($item) {
                $item->delete();
            });
            $banners?->each(function ($item) {
                $item->delete();
            });
            $subCategory->delete();
            return $this->success($subCategory, 'Sub-Category Deleted Successfully');
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage());
        }
    }

    protected function uploadImage(Request $request, $subCategory): void
    {
        $icons = $subCategory->getMedia('icon');
        $banners = $subCategory->getMedia('banner');
        if ($icons) {
            $icons->each(function ($item) {
                $item->delete();
            });
        }
        if ($banners) {
            $banners->each(function ($item) {
                $item->delete();
            });
        }
        $icons = [];
        $banners = [];
        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $icons[] = $request->file('icon');
            event(new UploadImageEvent($subCategory, $icons, 'icon'));
        }
        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            $banners[] = $request->file('banner');
            event(new UploadImageEvent($subCategory, $banners, 'banner'));
        }
    }

    protected function getImages($subCategory): mixed
    {
        $category['icon_image_url'] = null;
        $category['banner_image_url'] = null;
        $icons = $subCategory->getMedia('icon');
        $banners = $subCategory->getMedia('banner');
        if ($icons) {
            $icons->each(function ($item) use ($subCategory) {
                $subCategory['icon_image_url'] = $item->getFullUrl();
            });
        }
        if ($banners) {
            $banners->each(function ($item) use ($subCategory) {
                $subCategory['banner_image_url'] = $item->getFullUrl();
            });
        }
        $subCategory->makeHidden('media');
        return $subCategory;
    }
}
