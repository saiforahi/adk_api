<?php

namespace App\Http\Controllers\v1\category;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // $categories = Category::with(['sub_category', 'sub_category.sub_sub_category'])->latest()->get();
        $categories = Category::with(['sub_category'])->latest()->get();
        $categories = $categories->transform(function ($item, $key) {
            foreach ($item->getMedia('icon') as $media) {
                $item['icon_image_url'] = $media->getFullUrl();
            }
            foreach ($item->getMedia('banner') as $media) {
                $item['banner_image_url'] = $media->getFullUrl();
            }
            $item->makeHidden('media');
            return $item;
        });
        return $this->success($categories);
    }

    /**
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        try {

            $data = Arr::except($request->validated(), ['icon', 'banner']);
            $data['slug'] = Str::slug($data['name']);
            $category = Category::query()->create($data);
            $this->uploadImage($request, $category);
            return $this->success($category);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        $this->getImages($category);
        return $this->success($category->load('media', 'sub_category'));
    }

    /**
     * @param Category $category
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function update(Category $category, CategoryRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), ['icon', 'banner']);
            $data['slug'] = Str::slug($data['name']);
            $category->update($data);
            $this->uploadImage($request, $category);
            return $this->success($category);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $icons = $category->getMedia('icon');
            $banners = $category->getMedia('banner');
            $icons?->each(function ($item) {
                $item->delete();
            });
            $banners?->each(function ($item) {
                $item->delete();
            });
            $category->delete();
            return $this->success($category, 'Category Deleted Successfully');
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage());
        }
    }

    /**
     * @param CategoryRequest $request
     * @param $category
     * @return void
     */

    protected function uploadImage(Request $request, $category): void
    {
        $icons = $category->getMedia('icon');
        $banners = $category->getMedia('banner');
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
            event(new UploadImageEvent($category, $icons, 'icon'));
        }
        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            $banners[] = $request->file('banner');
            event(new UploadImageEvent($category, $banners, 'banner'));
        }
    }

    /**
     * @param $category
     * @return mixed
     */
    protected function getImages($category): mixed
    {
        $category['icon_image_url'] = null;
        $category['banner_image_url'] = null;
        $icons = $category->getMedia('icon');
        $banners = $category->getMedia('banner');
        if ($icons) {
            $icons->each(function ($item) use ($category) {
                $category['icon_image_url'] = $item->getFullUrl();
            });
        }
        if ($banners) {
            $banners->each(function ($item) use ($category) {
                $category['banner_image_url'] = $item->getFullUrl();
            });
        }
        $category->makeHidden('media');
        return $category;
    }
}
