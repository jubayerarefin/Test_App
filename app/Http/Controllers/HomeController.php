<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $data['active_verified_users'] = User::all()
            ->where('status', '=', 1)
            ->where('email_verified_at', '<>', null)
            ->count();
        $data['active_verified_user_with_active_products'] = DB::table('users')
            ->join('user_product', 'users.id', '=', 'user_product.user_id')
            ->join('products', 'user_product.product_id', '=', 'products.id')
            ->select('users.*')
            ->where('products.status', '=', 1)
            ->get()
            ->count();
        $data['active_products'] = Product::all()
            ->where('status', '=', 1)
            ->count();
        $data['active_products_without_users'] = DB::table('products')
            ->leftJoin('user_product', 'products.id', '=', 'user_product.product_id')
            ->select('products.*', 'user_product.*')
            ->where('products.status', '=', 1)
            ->where('user_product.user_id', '=', null)
            ->get()
            ->count();
        $data['active_attached_products_count'] = DB::table('products')
            ->join('user_product', 'products.id', '=', 'user_product.product_id')
            ->select('user_product.quantity')
            ->where('products.status', '=', 1)
            ->get()
            ->sum('quantity');
        $data['summarized_active_attached_products_price'] = DB::table('products')
            ->join('user_product', 'products.id', '=', 'user_product.product_id')
            ->select('user_product.price')
            ->where('products.status', '=', 1)
            ->get()
            ->sum('price');
        $data['summarized_active_attached_products_price_per_user'] = DB::table('products')
            ->join('user_product', 'products.id', '=', 'user_product.product_id')
            ->join('users', 'user_product.user_id', '=', 'users.id')
            ->select('users.name')->selectRaw('SUM(user_product.price) AS price')
            ->where('products.status', '=', 1)
            ->groupBy('users.name')
            ->get()->all();

        return view('report', $data);
    }
}
