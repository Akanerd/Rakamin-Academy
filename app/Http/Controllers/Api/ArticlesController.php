<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ArticlesResource;
use App\Models\Articles;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ArticlesController extends Api
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Articles::paginate(5);
        return response([
            'Articles' => new
                ArticlesResource($articles),
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
            'title' => 'required|max:255',
            'content' => 'required|max:255',
            'image' => 'required|mimes:jpeg,jpg,png|max:2048',
            'user_id' => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors(), 'Validation Error']);
        }
        if ($request->file('image')->isValid()) {
            $imagefile = $request->file('image');
            $extention = $imagefile->getClientOriginalExtension();
            $fileName = "image/" . date('YmdHis') . "." . $extention;
            $uploadPath = env('UPLOAD_PATH') . "/image";
            $request->file('image')->move($uploadPath, $fileName);
            $data['avatar'] = $fileName;
        }
        $articles = Articles::create($data);
        return response([
            'Articles' => new
                ArticlesResource($articles),
            'message' => 'Success'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function show(Articles $articles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $articles = Articles::findOrFail($id);
        
        $validator = validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required|max:255',
            'image' => 'sometimes|nullable|mimes:jpeg,jpg,png|max:2048',
            'user_id' => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors(), 'Validation Error'=>$request->all()]);
        }
        $data = $request->all();
        if ($request->hasFile('image')) {
            if ($request->file('image')->isvalid) {
                Storage::disk('upload')->delete($articles->image);
                $imageFile = $request->file('image');
                $extention = $imageFile->getClientOriginalExtension();
                $fileName = "image/" . date('YmdHis') . "." . $extention;
                $uploadPath = env('UPLOAD_PATH') . "/image";
                $request->file('image')->move($uploadPath, $fileName);
                $data['image'] = $fileName;
            }
        }
        $articles->update($data);
        return response([
            'Articles' => new
                ArticlesResource($articles),
            'message' => 'Success'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $articles = Articles::find($id);
        if (is_null($articles)) {
            return response(['message' => 'categories not found'], 400);
        }
        $articles->delete();
        Storage::disk('upload')->delete($articles->image);
        return response(['message' => 'Articles deleted']);
    }
}
