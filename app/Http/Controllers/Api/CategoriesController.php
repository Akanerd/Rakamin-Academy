<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoriesResource;
use App\Models\Categories;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Api
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::all();
        return response([
            'Categories' =>
            CategoriesResource::collection($categories),
            'message' => 'Successful'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = validator::make($data, [
            'name' => 'required',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors(), 'Validation Error']);
        }
        $categories = Categories::create($data);
        return response([
            'Categories' => new
                CategoriesResource($categories),
            'message' => 'Success'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categories = Categories::find($id);
        if (is_null($categories)) {
            return response(['message' => 'categories not found'], 400);
        } else {
            return response(['Categories' => new
                CategoriesResource($categories), 'message' => 'Success'], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $categories = Categories::find($id);
        $data = $request->all();
        $validator = validator::make($data, [
            'name' => 'required',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors(), 'Validation Error']);
        }
        if (is_null($categories)) {
            return response(['message' => 'categories not found'], 400);
        }
        $categories->update($data);
        return response([
            'Categories' => new
                CategoriesResource($categories),
            'message' => 'Success'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categories = Categories::find($id);
        if (is_null($categories)) {
            return response(['message' => 'categories not found'], 400);
        }
        $categories->delete();
        return response(['message' => 'Categories deleted']);
    }
}
