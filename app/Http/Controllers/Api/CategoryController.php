<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class CategoryController extends Controller
{

    use AuthorizesRequests;

    public function index()
    {
        $categories = CategoryResource::collection(Category::all());
        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $category = Category::create($request->all());
        return response()->json([
            'category' => CategoryResource::make($category),
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'category' => CategoryResource::make($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);

        $category->update($request->all());
        return response()->json([
            'category' => CategoryResource::make($category),
        ], 200);
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        if ($category->expenses()->exists()) {
            return response()->json([
                'message' => 'Category has expenses',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }

    public function trashed()
    {
        $this->authorize('viewAllTrashed', Category::class);

        $categories = CategoryResource::collection(Category::onlyTrashed()->get());
        return response()->json([
            'categories' => $categories,
        ], 200);
    }

    public function showTrashed($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        $this->authorize('viewTrashed', $category);

        return response()->json([
            'category' => CategoryResource::make($category),
        ], 200);
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $category);

        $category->restore();

        return response()->json([
            'message' => 'Category restored successfully',
        ], 200);
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $category);

        $category->forceDelete();

        return response()->json([
            'message' => 'Category deleted permanently successfully',
        ], 200);
    }
}
