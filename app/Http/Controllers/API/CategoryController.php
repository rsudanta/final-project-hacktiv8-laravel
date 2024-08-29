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
        $category = Category::all();
        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $category);
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $category = Category::create($request->all());
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $category);
    }

    public function getCategoryById($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ResponseHelper::jsonResponse('error', "Invalid category ID", null, 400);
        }

        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $category);
    }

    public function updateCategory(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
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
        $category->delete();
        return ResponseHelper::jsonResponse('success', 'Record deleted successfully');
    }
}
