<?php

namespace App\Http\Controllers\v1\category;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Requests\SubSubCategoryRequest;
use App\Models\SubSubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubSubCategoryController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $subSubCategory = SubSubCategory::with('sub_category')->get();
        return $this->success($subSubCategory);
    }

    /**
     * @param SubSubCategoryRequest $request
     * @return JsonResponse
     */
    public function store(SubSubCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->categoryData($request);
            $subSubCategory = SubSubCategory::query()->create($data);
            return $this->success($subSubCategory);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param SubSubCategory $subSubCategory
     * @return JsonResponse
     */
    public function show(SubSubCategory $subSubCategory): JsonResponse
    {
        return $this->success($subSubCategory->load('sub_category'));
    }

    /**
     * @param SubSubCategory $subSubCategory
     * @param SubSubCategoryRequest $request
     * @return JsonResponse
     */
    public function update(SubSubCategory $subSubCategory, SubSubCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->categoryData($request, $subSubCategory);
            dd($data);
            $subSubCategory->update($data);
            return $this->success($subSubCategory);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param SubSubCategory $subSubCategory
     * @return JsonResponse
     */
    public function destroy(SubSubCategory $subSubCategory): JsonResponse
    {
        if (Storage::disk('public')->exists($subSubCategory->icon)) {
            Storage::disk('public')->delete($subSubCategory->icon);
        }
        if (Storage::disk('public')->exists($subSubCategory->banner)) {
            Storage::disk('public')->delete($subSubCategory->banner);
        }
        $subSubCategory->delete();
        return $this->success($subSubCategory, 'Sub Sub-Category Deleted Successfully');
    }

    /**
     * @param SubSubCategoryRequest $request
     * @param SubSubCategory|null $subSubCategory
     * @return mixed
     */
    private function categoryData(SubSubCategoryRequest $request, SubSubCategory $subSubCategory = null): mixed
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('icon')) {
            if ($subSubCategory && $image = Storage::disk('public')) {
                $image->delete($subSubCategory->icon);
            }
            $icon = $request->file('icon')->store('category/sub_category/sub_sub_category', 'public');
            $data['icon'] = $icon;
        }
        if ($request->hasFile('banner')) {
            if ($subSubCategory && $image = Storage::disk('public')) {
                $image->delete($subSubCategory->banner);
            }
            $banner = $request->file('banner')->store('category/sub_category/sub_sub_category', 'public');
            $data['banner'] = $banner;
        }
        return $data;
    }
}
