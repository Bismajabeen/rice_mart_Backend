<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function createSeller(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',

            'shop_name' => 'required',
            'owner_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'cnic' => 'nullable',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('seller');

        $shop = Shop::create([
            'user_id' => $user->id,

            'cnic' => $request->cnic,

            
            'cnic_image' => null,

            'shop_name' => $request->shop_name,
            'owner_name' => $request->owner_name,

            'phone' => $request->phone,
            'address' => $request->address,

            'description' => $request->description,

           
            'status' => 'approved',
            'is_approved' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Seller created successfully',
            'user' => $user,
            'shop' => $shop,
        ]);
    }
}