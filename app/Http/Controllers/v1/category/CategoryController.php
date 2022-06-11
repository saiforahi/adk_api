<?php

namespace App\Http\Controllers\v1\category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $categories = Category::with(['sub_category', 'sub_category.sub_sub_category'])->latest()->get();
        return $this->success($categories);
    }

    /**
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->categoryData($request);
            $category = Category::query()->create($data);
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
        return $this->success($category->load('sub_category'));
    }

    /**
     * @param Category $category
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function update(Category $category, CategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->categoryData($request, $category);
            $category->update($data);
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
        if (Storage::disk('public')->exists($category->icon)) {
            Storage::disk('public')->delete($category->icon);
        }
        if (Storage::disk('public')->exists($category->banner)) {
            Storage::disk('public')->delete($category->banner);
        }
        $category->delete();
        return $this->success($category, 'Category Deleted Successfully');
    }

    /**
     * @param CategoryRequest $request
     * @param Category|null $category
     * @return mixed
     */
    private function categoryData(CategoryRequest $request, Category $category = null): mixed
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('icon')) {
            if ($category && $image = Storage::disk('public')) {
                $image->delete($category->icon);
            }
            $icon = $request->file('icon')->store('category', 'public');
            $data['icon'] = $icon;
        }
        if ($request->hasFile('banner')) {
            if ($category && $image = Storage::disk('public')) {
                $image->delete($category->banner);
            }
            $banner = $request->file('banner')->store('category', 'public');
            $data['banner'] = $banner;
        }
        return $data;
    }
}
