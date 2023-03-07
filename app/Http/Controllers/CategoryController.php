<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Traits\ValidateLibrary;

class CategoryController extends Controller
{
    use HttpResponses, ValidateLibrary;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id, Category $category)
    {
        $result = $this->validateLibrary($category);
        if (!$result) return $this->error('', "You are not authorized to make this request", Response::HTTP_UNAUTHORIZED);

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id, Category $category)
    {
        //
        $result = $this->validateLibrary($category);
        if (!$result) return $this->error('', "You are not authorized to make this request", Response::HTTP_UNAUTHORIZED);

        $request->validated($request->all());

        $category->update($request->all());

        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Category $category)
    {
        //
        $result = $this->validateLibrary($category);
        if (!$result) return $this->error('', "You are not authorized to make this request", Response::HTTP_UNAUTHORIZED);

        $category->delete();

        return $this->success([], "Category successfully deleted", Response::HTTP_NO_CONTENT);
    }
}
