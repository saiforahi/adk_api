<?php

namespace App\Http\Controllers\v1\attribute;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $attributes = Attribute::query()->latest()->get();
        return $this->success($attributes);
    }

    /**
     * @param AttributeRequest $request
     * @return JsonResponse
     */
    public function store(AttributeRequest $request): JsonResponse
    {
        try {
            $attribute = Attribute::query()->create($request->validated());
            return $this->success($attribute);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function show(Attribute $attribute): JsonResponse
    {
        return $this->success($attribute);
    }

    /**
     * @param Attribute $attribute
     * @param AttributeRequest $request
     * @return JsonResponse
     */
    public function update(Attribute $attribute, AttributeRequest $request): JsonResponse
    {
        try {
            $attribute->update($request->validated());
            return $this->success($attribute);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function destroy(Attribute $attribute): JsonResponse
    {
        $attribute->delete();
        return $this->success($attribute, 'Attribute Deleted Successfully');
    }

}
