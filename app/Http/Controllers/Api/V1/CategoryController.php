<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index()
    {
        $categories = $this->categoryService->getForUser(auth()->id());
        return $this->apiResponse(CategoryResource::collection($categories));
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->create(auth()->user(), $request->validated());
        return $this->apiResponse(new CategoryResource($category), 'Category created successfully', 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorizeCategory($category);
        $this->categoryService->update($category, $request->validated());

        return $this->apiResponse(new CategoryResource($category), 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);

        if (!$this->categoryService->delete($category)) {
            return $this->apiResponse(message: 'Cannot delete category with existing transactions.', status: 422);
        }

        return $this->apiResponse(message: 'Category deleted successfully');
    }

    private function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
