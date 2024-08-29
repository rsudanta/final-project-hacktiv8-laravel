<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProduct(Request $request)
    {
        $product = [];
        $category_id = $request->query('category_id');

        if ($category_id) {
            $product = Product::where('category_id', $category_id)
                ->get();
        } else {
            $product = Product::all();
        }

        if (count($product) == 0) {
            return ResponseHelper::jsonResponse('error', "Invalid category ID", null, 400);
        }

        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $product);
    }

    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'numeric|required',
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $product = Product::create($request->all());
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $product);
    }

    public function getProductById($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ResponseHelper::jsonResponse('error', "Invalid product ID", null, 400);
        }

        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $product);
    }


    public function updateProduct(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'price' => 'numeric',
            'category_id' => 'exists:categories,id'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $product = Product::find($id);
        if (!$product) {
            return ResponseHelper::jsonResponse('error', "Invalid Product ID", null, 400);
        }
        $product->update($request->all());
        return ResponseHelper::jsonResponse('success', 'Record updated successfully', $product);
    }

    public function destroyProduct(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ResponseHelper::jsonResponse('error', "Invalid Product ID", null, 400);
        }
        $product->delete();
        return ResponseHelper::jsonResponse('success', 'Record deleted successfully');
    }
}
