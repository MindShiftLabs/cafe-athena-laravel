<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
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

        // Total Customers
        $totalCustomers = User::where('user_role', 'customer')->count();

        // Total Staff
        $totalStaff = User::whereIn('user_role', ['admin', 'barista'])->count();

        // 2. Coffee of the Day (Consistent per Session)
        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        // 3. Recent Registrations
        $recentRegistrations = User::where('user_role', 'customer')
            ->orderBy('user_id', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'totalRevenue' => $totalRevenue,
            'newOrdersCount' => $newOrdersCount,
            'totalCustomers' => $totalCustomers,
            'totalStaff' => $totalStaff,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'recentRegistrations' => $recentRegistrations,
            'username' => Auth::user()->user_firstname
        ]);
    }

    // --- USER MANAGEMENT ---

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('view') && $request->view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_firstname', 'like', "%{$search}%")
                  ->orWhere('user_lastname', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('user_createdat', 'desc')->get();

        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        return view('admin.users', [
            'users' => $users,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'username' => Auth::user()->user_firstname,
            'view' => $request->view ?? 'active'
        ]);
    }

    public function restoreUser($id)
    {
        User::withTrashed()->find($id)->restore();
        return redirect()->route('admin.users', ['view' => 'trash'])->with('success', 'User restored successfully.');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:user,user_email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,barista,customer',
        ]);

        User::create([
            'user_firstname' => $request->firstname,
            'user_lastname' => $request->lastname,
            'user_email' => $request->email,
            'user_password' => Hash::make($request->password),
            'user_role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'User added successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:user,user_email,' . $id . ',user_id',
            'role' => 'required|in:admin,barista,customer',
        ]);

        $user->update([
            'user_firstname' => $request->firstname,
            'user_lastname' => $request->lastname,
            'user_email' => $request->email,
            'user_role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        User::destroy($id);
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    // --- PRODUCT MANAGEMENT ---

    public function products(Request $request)
    {
        $query = Product::query();

        if ($request->has('view') && $request->view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('product_id', 'desc')->get();

        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        return view('admin.products', [
            'products' => $products,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'username' => Auth::user()->user_firstname,
            'view' => $request->view ?? 'active'
        ]);
    }

    public function restoreProduct($id)
    {
        Product::withTrashed()->find($id)->restore();
        return redirect()->route('admin.products', ['view' => 'trash'])->with('success', 'Product restored successfully.');
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

        return redirect()->route('admin.products')->with('success', 'Product added successfully.');
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
            // Delete old image if it exists
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

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);

        if ($product) {
            $product->delete();
        }

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
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

        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        return view('admin.orders', [
            'orders' => $orders,
            'coffeeOfTheDay' => $coffeeOfTheDay,
            'username' => Auth::user()->user_firstname
        ]);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'order_status' => 'required|in:pending,preparing,ready,completed,cancelled',
            'order_payment_status' => 'required|in:unpaid,paid',
        ]);

        Order::where('order_id', $request->order_id)->update([
            'order_status' => $request->order_status,
            'order_payment_status' => $request->order_payment_status,
        ]);

        return redirect()->route('admin.orders')->with('success', 'Order updated successfully.');
    }

    // --- SETTINGS ---

    public function settings()
    {
        $user = Auth::user();
        $coffeeOfTheDay = $this->getCoffeeOfTheDay();

        return view('admin.settings', [
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

        $user = Auth::user();
        // The user object from Auth::user() is already an Eloquent model instance
        // but let's make sure we update the fresh instance or current one.
        /** @var User $user */
        $user->update([
            'user_firstname' => $request->user_firstname,
            'user_lastname' => $request->user_lastname,
            'user_birthday' => $request->user_birthday,
            'user_phone' => $request->user_phone,
            'user_address' => $request->user_address,
        ]);

        return redirect()->route('admin.settings')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->user_password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password.']);
        }

        /** @var User $user */
        $user->update([
            'user_password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.settings')->with('success', 'Password changed successfully.');
    }
}
