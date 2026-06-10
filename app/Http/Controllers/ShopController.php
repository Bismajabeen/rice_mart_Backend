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

        $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('shops', 'public');
        }

        $shop = Shop::create([
            'seller_id'   => $user->id,
            'name'        => $request->name,
            'description' => $request->description,
            'address'     => $request->address,
            'logo'        => $logoPath,
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Shop created! Admin approve karega.',
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

        $shop->update($request->only(['name', 'description', 'address']));

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

    // ── ADMIN: APPROVE / REJECT ───────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json(['message' => 'Shop not found.'], 404);
        }

        $shop->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Shop status updated.',
            'shop'    => $shop,
        ], 200);
    }
}