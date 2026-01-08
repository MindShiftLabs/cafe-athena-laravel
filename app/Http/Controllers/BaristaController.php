<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;

class BaristaController extends Controller
{
    public function dashboard()
    {
        // 1. Fetch Statistics

        // Total Revenue
        $totalRevenue = Order::where('order_payment_status', 'paid')
            ->sum('order_total');

        // New Orders (today)
        $newOrdersCount = Order::whereDate('order_createdat', now()->toDateString())
            ->count();

        // Pending Orders
        $pendingOrdersCount = Order::where('order_status', 'pending')
            ->count();

        // Total Products (Products in Stock)
        $totalProducts = Product::count();

        // 2. Coffee of the Day (Consistent per Session)
        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        // 3. Recent Orders
        $recentOrders = Order::with('user')
            ->orderBy('order_createdat', 'desc')
            ->limit(5)
            ->get();

        // Transform for view if needed, but Eloquent models work directly in Blade
        // We'll pass the models directly.
        // Note: The view expects user properties on the order object if it was a join.
        // With Eloquent 'with', we access via $order->user->user_firstname.
        // Let's quickly check the view to see if we need to flatten it or update the view.
        // resources/views/barista/dashboard.blade.php uses $order->user_firstname.
        // We should map it to keep the view working without changes, or update the view.
        // Updating the controller to output flat structure is safer for now to avoid view changes.
        
        $recentOrdersFlat = $recentOrders->map(function($order) {
            $order->user_firstname = $order->user->user_firstname;
            $order->user_lastname = $order->user->user_lastname;
            return $order;
        });

        return view('barista.dashboard', [
            'totalRevenue' => $totalRevenue,
            'newOrdersCount' => $newOrdersCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'totalProducts' => $totalProducts,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'recentOrders' => $recentOrdersFlat,
            'username' => Auth::user()->user_firstname
        ]);
    }

    // --- PRODUCT MANAGEMENT ---

    public function products(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%");
        }

        $products = $query->orderBy('product_id', 'desc')->get();
        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        return view('barista.products', [
            'products' => $products,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'username' => Auth::user()->user_firstname
        ]);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|in:available,unavailable',
            'category' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = $this->handleImageUpload($request->file('image'), $request->category);

        Product::create([
            'product_name' => $request->name,
            'product_description' => $request->description,
            'product_price' => $request->price,
            'product_image' => $imagePath,
            'product_status' => $request->status,
            'product_category' => $request->category,
        ]);

        return redirect()->route('barista.products')->with('success', 'Product added successfully.');
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|in:available,unavailable',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $imagePath = $product->product_image;

        if ($request->hasFile('image')) {
            if (file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }
            $imagePath = $this->handleImageUpload($request->file('image'), $request->category);
        }

        $product->update([
            'product_name' => $request->name,
            'product_description' => $request->description,
            'product_price' => $request->price,
            'product_image' => $imagePath,
            'product_status' => $request->status,
            'product_category' => $request->category,
        ]);

        return redirect()->route('barista.products')->with('success', 'Product updated successfully.');
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);

        if ($product) {
            if (file_exists(public_path($product->product_image))) {
                @unlink(public_path($product->product_image));
            }
            $product->delete();
        }

        return redirect()->route('barista.products')->with('success', 'Product deleted successfully.');
    }

    public function toggleProductStock(Request $request)
    {
        $productId = $request->input('product_id');
        $hasStock = $request->input('has_stock');

        $status = $hasStock ? 'available' : 'unavailable';

        Product::where('product_id', $productId)->update([
            'product_status' => $status
        ]);

        return response()->json(['success' => true, 'message' => 'Stock status updated.']);
    }

    private function handleImageUpload($image, $category)
    {
        $categoryFolder = match ($category) {
            'Hot Brew' => 'hot-brew',
            'Iced & Cold' => 'iced-&-cold',
            'Pastry' => 'pastry',
            'Coffee Beans' => 'coffee-beans',
            default => 'uncategorized',
        };

        $targetDir = "assets/uploads/" . $categoryFolder;
        $imageName = preg_replace("/[^a-zA-Z0-9-_\.]/", "", str_replace(" ", "-", strtolower($image->getClientOriginalName())));

        $image->move(public_path($targetDir), $imageName);

        return $targetDir . "/" . $imageName;
    }

    // --- ORDER MANAGEMENT ---

    public function orders(Request $request)
    {
        $query = Order::with('user');

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('order_status', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('user_firstname', 'like', "%{$search}%")
                         ->orWhere('user_lastname', 'like', "%{$search}%");
                  });
            });
        }

        // Sort Filter
        if ($request->has('sort')) {
            $sort = $request->sort;
            if (in_array($sort, ['pending', 'preparing', 'ready', 'completed', 'cancelled'])) {
                $query->where('order_status', $sort);
                $query->orderBy('order_id', 'desc');
            } elseif (in_array($sort, ['order_total DESC', 'order_total ASC', 'order_id ASC', 'order_id DESC'])) {
                $sortParts = explode(' ', $sort);
                $query->orderBy($sortParts[0], $sortParts[1]);
            } else {
                $query->orderBy('order_id', 'desc');
            }
        } else {
            $query->orderBy('order_id', 'desc');
        }

        $orders = $query->get();

        // Flatten for view compatibility
        $orders->transform(function($order) {
            $order->user_firstname = $order->user->user_firstname;
            $order->user_lastname = $order->user->user_lastname;
            return $order;
        });

        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        return view('barista.orders', [
            'orders' => $orders,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'username' => Auth::user()->user_firstname,
            'search' => $request->search ?? '',
            'sort' => $request->sort ?? 'order_id DESC'
        ]);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'order_status' => 'required|in:pending,preparing,ready,completed,cancelled',
            'payment_status' => 'nullable|in:paid,unpaid',
            'amount_paid' => 'nullable|numeric',
        ]);

        $updateData = [
            'order_status' => $request->order_status,
        ];

        if ($request->payment_status === 'paid' && $request->amount_paid) {
            $updateData['order_payment_status'] = 'paid';
        }

        Order::where('order_id', $request->order_id)->update($updateData);

        return redirect()->route('barista.orders')->with('success', 'Order updated successfully.');
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['user', 'orderItems.product'])
            ->where('order_id', $id)
            ->first();

        if ($order) {
            // Transform to match JSON structure expected by frontend
            $response = [
                'order_id' => $order->order_id,
                'customer_name' => $order->user->user_firstname . ' ' . $order->user->user_lastname,
                'order_createdat' => $order->order_createdat,
                'order_payment_method' => $order->order_payment_method,
                'order_total' => $order->order_total,
                'items' => $order->orderItems->map(function($item) {
                    return [
                        'product_name' => $item->product->product_name,
                        'orderitem_quantity' => $item->orderitem_quantity,
                        'orderitem_price' => $item->orderitem_price,
                        'orderitem_subtotal' => $item->orderitem_subtotal,
                    ];
                })
            ];

            return response()->json($response);
        }

        return response()->json(['error' => 'Order not found'], 404);
    }

    // --- SETTINGS ---

    public function settings()
    {
        $user = Auth::user();
        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        return view('barista.settings', [
            'user' => $user,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'username' => $user->user_firstname
        ]);
    }

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

        return redirect()->route('barista.settings')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed', // 'confirmed' looks for new_password_confirmation
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->user_password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password.']);
        }

        $user->update([
            'user_password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('barista.settings')->with('success', 'Password changed successfully.');
    }

    // Helper to get consistent Coffee of the Day per session
    private function getCoffeeOfTheDay()
    {
        $productId = session('coffee_of_the_day_id');
        $product = null;

        if ($productId) {
            $product = Product::find($productId);
        }

        if (!$product) {
            $product = Product::inRandomOrder()->first();
            if ($product) {
                session(['coffee_of_the_day_id' => $product->product_id]);
            }
        }

        return $product;
    }
}