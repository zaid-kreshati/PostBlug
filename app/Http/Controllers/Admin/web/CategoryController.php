<?php

namespace App\Http\Controllers\Admin\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use JsonResponseTrait;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index2()
    {
        $categories = $this->categoryService->getParentNullCategories();
        $id=null;
        return view('DashBoard.categoryIndex', compact('categories', 'id'));
    }

    public function paginate(Request $request)
    {
        if($request['parent_id']==0)
        $categories = $this->categoryService->getParentNullCategories();
        else
        $categories = $this->categoryService->getCategoriesByParent($request->all());
        $html = view('DashBoard.partials.categoryIndex', compact('categories'))->render();
        $pagination=$categories->links('pagination::bootstrap-5')->render();
        $response=[
            'html'=>$html,
            'pagination'=>$pagination
        ];
        return $this->successResponse($response, __('Categories fetched successfully'));
    }

    public function store(Request $request)
    {
        Log::info($request->all());
        $category=$this->categoryService->createCategory($request->all());
        if($request['id']==0){
        $categories = $this->categoryService->getParentNullCategories();
        }
        else{
        $categories = $this->categoryService->getCategoriesByParent($request->id);
        }
    Log::info($categories);
        $html = view('DashBoard.partials.categoryIndex', compact('categories'))->render();
        $pagination=$categories->links('pagination::bootstrap-5')->render();
        $response=[
            'html'=>$html,
            'pagination'=>$pagination
        ];
        return $this->successResponse($response, __('Category created successfully'));
    }

    public function update(Request $request, $id)
    {
        $this->categoryService->updateCategory($request, $id);
        return $this->successResponse(null, __('Category updated successfully'));
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);
        $categories = $this->categoryService->getParentNullCategories();
        $pagination=$categories->links('pagination::bootstrap-5')->render();
        $response=[
            'pagination'=>$pagination
        ];
        return $this->successResponse($response, __('Category deleted successfully'));
    }
    public function search(Request $request)
    {
        $categories = $this->categoryService->searchCategories($request->search);
        $html = view('DashBoard.partials.categoryIndex', compact('categories'))->render();
        return $this->successResponse($html, __('Categories fetched successfully'));
    }
    public function index()
    {
        Log::info('index');
        // $categories = Category::whereNull('parent_id')
        //     ->with('children')
        //     ->get()
        //     ->map(function($category) {
        //         return [
        //             'id' => $category->id,
        //             'name' => $category->name,
        //             'has_children' => $category->children->count() > 0 ? true : false
        //         ];
        //     });
        $categories = $this->categoryService->getParentNullCategories();
        $id=0;

        return view('DashBoard.categoryIndex', compact('categories','id')); // Return the collection directly, not wrapped in 'data'
    }

    public function getNestedCategories($parentId)
    {

        $categories=$this->categoryService->getChildren($parentId);
        $id=$parentId;

        return view('DashBoard.categoryIndex', compact('categories','id')); // Return the collection directly, not wrapped in 'data'

    }


}


