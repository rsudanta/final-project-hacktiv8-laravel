<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProduct()
    {
        $products = Product::all();
        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $products);
    }

    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'numeric|required|min:1',
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $product = Product::create($request->all());
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $product);
    }

    public function updateProduct(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'price' => 'numeric|min:1',
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

        if ($product && $product->orders()->exists()) {
            return ResponseHelper::jsonResponse('error', "This product cannot be deleted because it is still referenced by orders.", null, 400);
        }

        if (!$product) {
            return ResponseHelper::jsonResponse('error', "Invalid Product ID", null, 400);
        }
        $product->delete();
        return ResponseHelper::jsonResponse('success', 'Record deleted successfully');
    }
}
