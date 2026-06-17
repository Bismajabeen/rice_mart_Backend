<?php
namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // ── GET ALL APPROVED SHOPS ────────────────────────────────
    public function index()
    {
        $shops = Shop::with('seller:id,name')
            ->where('status', 'approved')
            ->latest()
            ->get();

        return response()->json(['shops' => $shops], 200);
    }

    // ── GET SINGLE SHOP WITH PRODUCTS ─────────────────────────
    public function show($id)
    {
        $shop = Shop::with(['seller:id,name', 'products' => function ($q) {
            $q->where('status', 'approved');
        }])->find($id);

        if (!$shop) {
            return response()->json(['message' => 'Shop not found.'], 404);
        }

        return response()->json(['shop' => $shop], 200);
    }

    // ── SELLER: CREATE SHOP ───────────────────────────────────
    public function store(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        // Check agar shop pehle se exist karti hai
        $existingShop = Shop::where('seller_id', $user->id)->first();
        if ($existingShop) {
            return response()->json([
                'message' => 'Aap ki shop pehle se exist karti hai.',
                'shop'    => $existingShop,
            ], 400);
        }

        $request->validate([
            'name'        => 'required|string|max:200',
            'owner_name'  => 'required|string|max:200',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string',
            'description' => 'nullable|string',
            'cnic_number' => 'required|string|max:20',
        ]);

        $shop = Shop::create([
            'seller_id'   => $user->id,
            'name'        => $request->name,
            'owner_name'  => $request->owner_name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'description' => $request->description,
            'cnic_number' => $request->cnic_number,
            'status'      => 'pending',
        ]);

        // User ka role seller kar do
        $user->role = 'seller';
        $user->save();

        return response()->json([
            'message' => 'Shop request submit ho gayi! Admin approve karega.',
            'shop'    => $shop,
        ], 201);
    }

    // ── SELLER: MY SHOP ───────────────────────────────────────
    public function myShop(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $shop = Shop::where('seller_id', $user->id)->first();

        if (!$shop) {
            return response()->json(['message' => 'Shop nahi mili.'], 404);
        }

        return response()->json(['shop' => $shop], 200);
    }

    // ── SELLER: UPDATE SHOP ───────────────────────────────────
    public function update(Request $request, $id)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $shop = Shop::where('id', $id)->where('seller_id', $user->id)->first();
        if (!$shop) {
            return response()->json(['message' => 'Shop not found.'], 404);
        }

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('shops', 'public');
            $shop->logo = $logoPath;
            $shop->save();
        }

        $shop->update($request->only([
            'name', 'description', 'address',
            'owner_name', 'phone', 'cnic_number'
        ]));

        return response()->json([
            'message' => 'Shop updated!',
            'shop'    => $shop,
        ], 200);
    }

    // ── ADMIN: ALL SHOPS ──────────────────────────────────────
    public function allShops()
    {
        $shops = Shop::with('seller:id,name')->latest()->get();
        return response()->json(['shops' => $shops], 200);
    }

    // ── ADMIN: PENDING SHOPS ──────────────────────────────
public function pendingShops()
{
    $shops = Shop::with('seller:id,name')
        ->where('status', 'pending')
        ->latest()
        ->get();
    return response()->json(['shops' => $shops], 200);
}

// ── ADMIN: APPROVED SHOPS ─────────────────────────────
public function approvedShops()
{
    $shops = Shop::with('seller:id,name')
        ->where('status', 'approved')
        ->latest()
        ->get();
    return response()->json(['shops' => $shops], 200);
}
}