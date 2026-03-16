<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = auth()->user()->categories()->withCount('transactions')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        auth()->user()->categories()->create($request->validated());
        return back()->with('success', __('app.success'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorizeCategory($category);
        $category->update($request->validated());
        return back()->with('success', __('app.success'));
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);

        if ($category->transactions()->exists()) {
            return back()->with('error', __('app.category_has_transactions'));
        }

        $category->delete();
        return back()->with('success', __('app.success'));
    }

    private function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
