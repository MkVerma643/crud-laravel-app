<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Category::select('id', 'title', 'description', 'image')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'image' => 'required|image',
        ]);

        try {
            $imageName = Str::random . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('category/image', $request->image, $imageName);
            Category::create($request->post() + ['image' => $imageName]);

            return response()->json([
                'status' => 'success',
                'message' => 'category has been created successfully.' // for status 200
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
        return response()->json([
            'categories' => $category
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'image' => 'nullable'
        ]);

        try {
            $category->fill($request->post())->update();

            if ($request->hasFile('image')) {

                // remove old image
                if ($category->file('image')) {
                    $exists = Storage::disk('public')->exists("category/image/{$category->image}");
                    if ($exists) {
                        Storage::disk('public')->delete("category/image/{$category->image}");
                    }
                }

                $imageName = Str::random() . '' . $request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs("category/image/", $request->image, $imageName);
                $category->image = $imageName;
                $category->save();
            }

            return response()->json([
                'message' => 'Category updated successfully.' // for status 200
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => 'Something goes wrong while updating a Category!!',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
        try {
            if ($category->image) {
                $exists = Storage::disk('public')->exists("category/image/{$category->image}");
                if ($exists) {
                    Storage::disk('public')->delete("category/image/{$category->image}");
                }
            }

            $category->delete();

            return response()->json([
                'message' => 'category Deleted Successfully!!'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while deleting a category!!'
            ]);
        }
    }
}
