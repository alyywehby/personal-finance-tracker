<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index()
    {
        $categories = $this->categoryService->getForUser(auth()->id());
        return view('categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->create(auth()->user(), $request->validated());
        return back()->with('success', __('app.success'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->categoryService->authorize($category, auth()->id());
        $this->categoryService->update($category, $request->validated());
        return back()->with('success', __('app.success'));
    }

    public function destroy(Category $category)
    {
        $this->categoryService->authorize($category, auth()->id());

        if (!$this->categoryService->delete($category)) {
            return back()->with('error', __('app.category_has_transactions'));
        }

        return back()->with('success', __('app.success'));
    }
}
