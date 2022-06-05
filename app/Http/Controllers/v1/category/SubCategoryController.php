<?php

namespace App\Http\Controllers\v1\category;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $subCategory = SubCategory::with(['category', 'sub_sub_category'])->get();
        return $this->success($subCategory);
    }

    /**
     * @param SubCategoryRequest $request
     * @return JsonResponse
     */
    public function store(SubCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->categoryData($request);
            $subCategory = SubCategory::query()->create($data);
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
            $data = $this->categoryData($request, $subCategory);
            $subCategory->update($data);
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
        if (Storage::disk('public')->exists($subCategory->icon)) {
            Storage::disk('public')->delete($subCategory->icon);
        }
        if (Storage::disk('public')->exists($subCategory->banner)) {
            Storage::disk('public')->delete($subCategory->banner);
        }
        $subCategory->delete();
        return $this->success($subCategory, 'Sub-Category Deleted Successfully');
    }

    /**
     * @param SubCategoryRequest $request
     * @param SubCategory|null $subCategory
     * @return mixed
     */
    private function categoryData(SubCategoryRequest $request, SubCategory $subCategory = null): mixed
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('icon')) {
            if ($subCategory && $image = Storage::disk('public')) {
                $image->delete($subCategory->icon);
            }
            $icon = $request->file('icon')->store('category/sub_category', 'public');
            $data['icon'] = $icon;
        }
        if ($request->hasFile('banner')) {
            if ($subCategory && $image = Storage::disk('public')) {
                $image->delete($subCategory->banner);
            }
            $banner = $request->file('banner')->store('category/sub_category', 'public');
            $data['banner'] = $banner;
        }
        return $data;
    }
}
