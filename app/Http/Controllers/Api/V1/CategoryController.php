<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = auth()->user()->categories()->withCount('transactions')->get();
        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'message' => '',
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = auth()->user()->categories()->create($request->validated());
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'message' => 'Category created successfully',
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorizeCategory($category);
        $category->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'message' => 'Category updated successfully',
        ]);
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);

        if ($category->transactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing transactions.',
                'errors' => [],
            ], 422);
        }

        $category->delete();
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Category deleted successfully',
        ]);
    }

    private function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== auth()->id()) {
            abort(response()->json(['success' => false, 'message' => 'Forbidden', 'errors' => []], 403));
        }
    }
}
