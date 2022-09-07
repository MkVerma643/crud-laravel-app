<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Product::select('id', 'title', 'description', 'image')->get();
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
            $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('Product/image', $request->image, $imageName);
            Product::create($request->post() + ['image' => $imageName]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product has been created successfully.' // for status 200
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
     * @param  \App\Models\Product  $Product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $Product)
    {
        //
        return response()->json([
            'products' => $Product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $Product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $Product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $Product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $Product)
    {
        //
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'image' => 'nullable'
        ]);

        try {
            $Product->fill($request->post())->update();

            if ($request->hasFile('image')) {

                // remove old image
                if ($Product->file('image')) {
                    $exists = Storage::disk('public')->exists("Product/image/{$Product->image}");
                    if ($exists) {
                        Storage::disk('public')->delete("Product/image/{$Product->image}");
                    }
                }

                $imageName = Str::random() . '' . $request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs("Product/image/", $request->image, $imageName);
                $Product->image = $imageName;
                $Product->save();
            }

            return response()->json([
                'message' => 'Product updated successfully.' // for status 200
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => 'Something goes wrong while updating a Product!!',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $Product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $Product)
    {
        //
        try {
            if ($Product->image) {
                $exists = Storage::disk('public')->exists("Product/image/{$Product->image}");
                if ($exists) {
                    Storage::disk('public')->delete("Product/image/{$Product->image}");
                }
            }

            $Product->delete();

            return response()->json([
                'message' => 'Product Deleted Successfully!!'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while deleting a Product!!'
            ]);
        }
    }
}