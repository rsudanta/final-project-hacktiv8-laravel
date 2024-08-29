<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    function getOrder()
    {
        $order = Order::all();
        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $order);
    }

    function storeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric',
            'customer_name' => 'required|string',
            'customer_address' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $productPrice = Product::where('id', $request->product_id)->pluck("price")->first();

        $insertData = [
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $productPrice * $request->quantity,
            'customer_name' => $request->customer_name,
            'customer_address' => $request->customer_address,
            'order_date' => now(),
        ];

        $order = Order::create($insertData);
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $order);
    }

    function updateOrder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'exists:products,id',
            'quantity' => 'numeric',
            'customer_name' => 'string',
            'customer_address' => 'string',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $order = Order::find($id);
        $productId = $request->product_id ?: $order->product_id;
        $productPrice = Product::where('id', $productId)->pluck("price")->first();
        $quantity = $request->quantity ?: $order->quantity;

        $updateData = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'total_price' => $productPrice * $quantity,
            'customer_name' => $request->customer_name ?: $order->customer_name,
            'customer_address' => $request->customer_address ?: $order->customer_address,
            'order_date' => now(),
        ];

        $order->update($updateData);
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $order);
    }

    public function getOrderById($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return ResponseHelper::jsonResponse('error', "Invalid order ID", null, 400);
        }

        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $order);
    }

    public function destroyOrder(string $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return ResponseHelper::jsonResponse('error', "Invalid order ID", null, 400);
        }
        $order->delete();
        return ResponseHelper::jsonResponse('success', 'Record deleted successfully');
    }

    public function getReport()
    {
        $order = Order::get()->toArray();
        $totalRevenue = array_sum(array_column($order, 'total_price'));
        $data = [
            'total_orders' => count($order),
            'total_revenue' => $totalRevenue,
            'orders' => $order,
        ];

        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $data);
    }
}
