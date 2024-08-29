<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    function getOrder()
    {
        $order = Order::where('user_id', auth()->user()->id)->get();
        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $order);
    }

    function storeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
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
            'order_date' => now(),
        ];

        $order = Order::create($insertData);
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $order);
    }

    function updateOrder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'exists:products,id',
            'quantity' => 'numeric|min:1',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            return ResponseHelper::jsonResponse('error', $errorMessages, null, 400);
        }

        $order = Order::find($id);
        if (!$order) {
            return ResponseHelper::jsonResponse('error', "Invalid Order ID", null, 400);
        }
        $productId = $request->product_id ?: $order->product_id;
        $productPrice = Product::where('id', $productId)->pluck("price")->first();
        $quantity = $request->quantity ?: $order->quantity;

        $updateData = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'total_price' => $productPrice * $quantity,
            'order_date' => now(),
        ];

        $order->update($updateData);
        return ResponseHelper::jsonResponse('success', 'Record created successfully', $order);
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
        $order = Order::select("orders.id", "p.name as product_name", "c.name as category_name", "quantity", "total_price", "u.name as customer_name", "address as customer_address", "order_date")
            ->leftJoin('users as u', 'u.id', 'orders.user_id')
            ->leftJoin('products as p', 'p.id', 'orders.product_id')
            ->leftJoin('categories as c', 'p.category_id', 'c.id')
            ->get()
            ->toArray();
        $totalRevenue = array_sum(array_column($order, 'total_price'));
        $data = [
            'total_orders' => count($order),
            'total_revenue' => $totalRevenue,
            'orders' => $order,
        ];

        return ResponseHelper::jsonResponse('success', 'Record retrieved successfully', $data);
    }
}
