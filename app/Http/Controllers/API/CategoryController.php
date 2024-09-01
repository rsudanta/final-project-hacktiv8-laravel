<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function getCategory()
    {
        $categories = Category::all();
        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $categories);
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string|unique:categories",
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $category = Category::create($request->all());
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $category);
    }

    public function updateCategory(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string|unique:categories",
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $category = Category::find($id);
        if (!$category) {
            return ResponseHelper::jsonResponse('error', "Invalid category ID", null, 400);
        }
        $category->update($request->all());
        return ResponseHelper::jsonResponse('success', 'Record updated successfully', $category);
    }

    public function destroyCategory(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return ResponseHelper::jsonResponse('error', "Invalid category ID", null, 400);
        }

        if ($category && $category->products()->exists()) {
            return ResponseHelper::jsonResponse('error', "This category cannot be deleted because it is still referenced by products.", null, 400);
        }

        $category->delete();
        return ResponseHelper::jsonResponse('success', 'Record deleted successfully');
    }
}
