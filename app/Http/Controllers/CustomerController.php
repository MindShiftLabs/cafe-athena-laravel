<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CustomerController extends Controller
{
    // --- VIEWS ---

    public function dashboard()
    {
        $user = Auth::user();
        $initials = strtoupper(substr($user->user_firstname, 0, 1) . substr($user->user_lastname, 0, 1));

        return view('customer.dashboard', [
            'user' => $user,
            'initials' => $initials
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        $initials = strtoupper(substr($user->user_firstname, 0, 1) . substr($user->user_lastname, 0, 1));

        return view('customer.history', [
            'user' => $user,
            'initials' => $initials
        ]);
    }

    public function settings()
    {
        $user = Auth::user();
        $initials = strtoupper(substr($user->user_firstname, 0, 1) . substr($user->user_lastname, 0, 1));

        return view('customer.settings', [
            'user' => $user,
            'initials' => $initials
        ]);
    }

    // --- API / ACTIONS ---

    public function getProducts()
    {
        $products = Product::select(
                'product_id as id',
                'product_name as name',
                'product_price as price',
                'product_category as category',
                'product_image as imageUrl',
                'product_status'
            )
            ->orderBy('product_category')
            ->orderBy('product_name')
            ->get()
            ->map(function ($product) {
                // Convert numeric types and adjust image path if necessary
                $product->price = (float) $product->price;
                $product->hasStock = ($product->product_status === 'available');
                unset($product->product_status); 
                return $product;
            });

        return response()->json($products);
    }

    public function getOrderHistory()
    {
        $user = Auth::user();
        
        $orders = Order::where('user_id', $user->user_id)
            ->select('order_id', 'order_status', 'order_type', 'order_total', 'order_createdat')
            ->orderBy('order_createdat', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function processOrder(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'checkout' => 'required|array',
            'totals' => 'required|array',
        ]);

        $cart = $request->input('cart');
        $checkout = $request->input('checkout');
        $totals = $request->input('totals');
        $user_id = Auth::id();

        DB::beginTransaction();

        try {
            $order_type = strtolower($checkout['method']);
            $order_total = $totals['finalTotal'];
            $order_payment_method = strtolower($checkout['payment']);
            $order_delivery_address = $checkout['address'] ?? null;
            
            $order_payment_status = 'unpaid';
            if ($order_payment_method === 'card') {
                $order_payment_status = 'paid';
            }

            // Create Order
            $order = Order::create([
                'user_id' => $user_id,
                'order_type' => $order_type,
                'order_total' => $order_total,
                'order_payment_method' => $order_payment_method,
                'order_payment_status' => $order_payment_status,
                'order_delivery_address' => $order_delivery_address,
                'order_status' => 'pending',
                'order_notes' => null, // Assuming no notes for now
            ]);

            // Create Order Items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['id'],
                    'orderitem_quantity' => $item['quantity'],
                    'orderitem_price' => $item['price'],
                    'orderitem_subtotal' => $item['quantity'] * $item['price'],
                    'orderitem_notes' => null, // Assuming no notes for now
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Order placed successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()], 500);
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
        ]);

        $orderId = $request->input('order_id');
        $userId = Auth::id();

        $updated = Order::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->where('order_status', 'ready')
            ->update([
                'order_status' => 'completed',
                'order_completedat' => now(),
            ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Order marked as completed!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Order could not be updated. It may not be ready or does not belong to you.'], 400);
        }
    }

    // --- PROFILE SETTINGS ---

    public function updateProfile(Request $request)
    {
        $request->validate([
            'user_firstname' => 'required|string|max:100',
            'user_lastname' => 'required|string|max:100',
            'user_birthday' => 'nullable|date',
            'user_phone' => 'nullable|string|max:20',
            'user_address' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'user_firstname' => $request->user_firstname,
            'user_lastname' => $request->user_lastname,
            'user_birthday' => $request->user_birthday,
            'user_phone' => $request->user_phone,
            'user_address' => $request->user_address,
        ]);

        return redirect()->route('customer.settings')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->user_password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password.']);
        }

        $user->update([
            'user_password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('customer.settings')->with('success', 'Password changed successfully.');
    }
}