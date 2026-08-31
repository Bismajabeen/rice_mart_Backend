<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RiceCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TestImageController;
use App\Http\Controllers\ShopReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AiRecommendationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CourierChargeController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SellerRemovalController;
use App\Http\Controllers\StripeController;

/*
|--------------------------------------------------------------------------
| Route map legend
|--------------------------------------------------------------------------
| PUBLIC        -> no auth required
| ANY LOGGED IN -> auth:sanctum only, no specific role/permission
| CUSTOMER      -> permission normally only granted to the customer role
| SELLER        -> permission normally only granted to the seller role
| ADMIN         -> permission granted to admin (and super_admin, who has everything)
| SUPER ADMIN   -> permission granted ONLY to super_admin
|--------------------------------------------------------------------------
*/


// =========================================================================
// PUBLIC — AUTH / ONBOARDING
// =========================================================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


// =========================================================================
// PUBLIC — BROWSING (no auth, used by app before login / guest browsing)
// =========================================================================

Route::get('/rice-categories', [RiceCategoryController::class, 'index']);
Route::get('/all-rice-categories', [RiceCategoryController::class, 'allCategories']);

Route::get('/all-products', [ProductController::class, 'allProducts']);
Route::get('/shop-products/{shopId}', [ProductController::class, 'shopProducts']);

Route::get('/cities-with-charges', [CityController::class, 'citiesWithCharges']);
Route::get('/delivery-charges', [CourierChargeController::class, 'deliverableCities']);

Route::get('/approved-shops', [ShopController::class, 'approvedShops']);

// Public product recommendation endpoint — intentionally no auth
Route::post('/ai-recommendation', [AiRecommendationController::class, 'recommend']);

// Stripe calls this directly — must stay outside auth:sanctum
Route::post('/stripe/webhook', [StripeController::class, 'webhook']);

// Test/dev image upload helper — kept exactly as in the original file
// (no auth middleware on this one, same as before)
Route::post('/test-image', [TestImageController::class, 'upload']);


// =========================================================================
// ANY LOGGED-IN USER (auth:sanctum only — no specific permission)
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/delete-account/request', [ProfileController::class, 'requestDeletion']);
    Route::post('/delete-account/confirm', [ProfileController::class, 'confirmDeletion']);
    Route::get('/user', fn (Request $request) => $request->user());
    Route::put('/update-profile', [ProfileController::class, 'update']);

    // Notifications — always scoped to the logged-in user in the controller
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    // Needed by customers at checkout and by sellers filling in payout
    // details, so it stays open to any authenticated user rather than
    // gated to one role.
    Route::get('/payment-settings', [PaymentSettingController::class, 'paymentSettings']);

    // =========================
    // SHOP REVIEWS
    // Any customer/seller can submit a review (controller enforces the
    // "must be the buyer on a delivered order" rule). Reviews are then
    // visible to any authenticated user — customer, seller, admin, or
    // super_admin — since prospective buyers need to see them too.
    // =========================
    Route::post('/shop-review', [ShopReviewController::class, 'store']);
    Route::get('/shops/{shopId}/reviews', [ShopReviewController::class, 'shopReviews']);

    // Complaints — filing + viewing your own thread is open to any user.
    // Controllers must still scope /complaints/my and /complaints/{id} to
    // records the current user owns (see SUPER ADMIN section note below).
    Route::middleware('permission:file complaints')->group(function () {
        Route::post('/complaints', [ComplaintController::class, 'store']);
    });
    Route::get('/complaints/my', [ComplaintController::class, 'myComplaints']);
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);
    Route::post('/complaints/{complaint}/messages', [ComplaintController::class, 'addMessage']);

    Route::get('/settings/emergency-contact', [SettingController::class, 'emergencyContact']);

    // Chat — controller scopes conversations to the logged-in user
    Route::get('/conversations', [ChatController::class, 'index']);
    Route::post('/conversations/start', [ChatController::class, 'start']);
    Route::get('/conversations/{id}/messages', [ChatController::class, 'messages']);
    Route::post('/conversations/{id}/messages', [ChatController::class, 'send']);

    // Stripe — customer starting a card payment
    Route::post('/stripe/create-intent', [StripeController::class, 'createPaymentIntent']);
});


// =========================================================================
// CUSTOMER ROUTES
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {

    // Applying to become a seller (creates a shop pending approval)
    Route::post('/shops', [ShopController::class, 'store'])
        ->middleware('permission:create shop');

    // Orders
    Route::post('/checkout', [OrderController::class, 'checkout'])
        ->middleware('permission:checkout orders');

    Route::get('/my-orders', [OrderController::class, 'myOrders'])
        ->middleware('permission:view own orders');

    Route::get('/active-orders', [OrderController::class, 'activeOrders'])
        ->middleware('permission:view own orders');

    Route::get('/order-history', [OrderController::class, 'orderHistory'])
        ->middleware('permission:view own orders');

    Route::put('/order-item/{id}/confirm-received', [OrderController::class, 'confirmReceived'])
        ->middleware('permission:view own orders');
});


// =========================================================================
// SELLER ROUTES
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {

    // Shop management (own shop only)
    Route::get('/my-shop', [ShopController::class, 'myShop']);

    Route::put('/shops/{id}', [ShopController::class, 'update'])
        ->middleware('permission:update own shop');

    Route::post('/shops/{id}/delete/request', [ShopController::class, 'requestShopDeletion'])
        ->middleware('permission:delete own shop');

    Route::post('/shops/{id}/delete/confirm', [ShopController::class, 'confirmShopDeletion'])
        ->middleware('permission:delete own shop');

    Route::put('/my-shop/payout-details', [ShopController::class, 'updatePayoutDetails'])
        ->middleware('permission:update own shop');

    // Products (own products only)
    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('permission:create products');

    Route::put('/products/{id}', [ProductController::class, 'update'])
        ->middleware('permission:update own products');

    Route::delete('/products/{id}', [ProductController::class, 'delete'])
        ->middleware('permission:delete own products');

    // Seller order management
    Route::get('/seller-orders', [SellerOrderController::class, 'sellerOrders'])
        ->middleware('permission:view shop orders');

    Route::put('/seller/order-item/{id}/status', [SellerOrderController::class, 'updateStatus'])
        ->middleware('permission:update order status');

    // Seller payouts
    Route::get('/seller/payouts', [PayoutController::class, 'sellerPayouts'])
        ->middleware('permission:view own payouts');
});


// =========================================================================
// ADMIN ROUTES  (admin + super_admin, since super_admin has every permission)
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/admin/create-seller', [ShopController::class, 'adminCreateSeller'])
        ->middleware('permission:create sellers');

    // --- Users ---
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:view users');

    Route::get('/roles', [UserController::class, 'roles'])
        ->middleware('permission:view users');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:create users');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->middleware('permission:update users');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:delete users');

    // --- Categories ---
    Route::put('/rice-categories/{id}/status', [RiceCategoryController::class, 'updateStatus'])
        ->middleware('permission:update categories');

    // --- Shop approval / lifecycle ---
    Route::get('/pending-shops', [ShopController::class, 'pendingShops'])
        ->middleware('permission:view all shops');

    Route::post('/shops/{id}/approve', [ShopController::class, 'approve'])
        ->middleware('permission:approve shops');

    Route::delete('/shops/{id}', [ShopController::class, 'reject'])
        ->middleware('permission:reject shops');

    Route::get('/rejected-shops', [ShopController::class, 'rejectedShops'])
        ->middleware('permission:view all shops');

    Route::post('/shops/{id}/request-correction', [ShopController::class, 'requestCorrection'])
        ->middleware('permission:update any shop');

    Route::post('/admin/shops/{id}/remove-seller', [SellerRemovalController::class, 'remove'])
        ->middleware('permission:remove sellers');

    Route::get('/removed-shops', [SellerRemovalController::class, 'removedShops'])
        ->middleware('permission:view all shops');

    // --- Orders ---
    Route::get('/admin/orders', [OrderController::class, 'adminOrders'])
        ->middleware('permission:view all orders');

    Route::get('/admin/order-history', [OrderController::class, 'adminOrderHistory'])
        ->middleware('permission:view all orders');

    Route::put('/admin/order-item/{id}/status', [OrderController::class, 'adminUpdateOrderItemStatus'])
        ->middleware('permission:update any order status');

    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])
        ->middleware('permission:update any order status');

    // --- Payments & payouts ---
    Route::get('/admin/payments', [PaymentController::class, 'adminPayments'])
        ->middleware('permission:view all payments');

    Route::put('/admin/payments/{id}/status', [PaymentController::class, 'updatePaymentStatus'])
        ->middleware('permission:manage payments');

    Route::get('/admin/payouts', [PayoutController::class, 'index'])
        ->middleware('permission:view all payments');

    Route::post('/admin/payouts/{id}/pay', [PayoutController::class, 'pay'])
        ->middleware('permission:manage payments');

    // --- Payment settings ---
    Route::post('/admin/payment-settings', [PaymentSettingController::class, 'adminUpdatePaymentSettings'])
        ->middleware('permission:manage settings');

    // --- Cities & courier charges ---
    Route::get('/admin/cities', [CityController::class, 'index'])
        ->middleware('permission:manage cities');

    Route::post('/admin/cities', [CityController::class, 'store'])
        ->middleware('permission:manage cities');

    Route::put('/admin/cities/{id}', [CityController::class, 'update'])
        ->middleware('permission:manage cities');

    Route::delete('/admin/cities/{id}', [CityController::class, 'destroy'])
        ->middleware('permission:manage cities');

    Route::get('/admin/available-cities', [CityController::class, 'availableCities'])
        ->middleware('permission:manage cities');

    Route::get('/admin/courier-charges', [CourierChargeController::class, 'index'])
        ->middleware('permission:manage cities');

    Route::post('/admin/courier-charges', [CourierChargeController::class, 'store'])
        ->middleware('permission:manage cities');

    Route::put('/admin/courier-charges/{id}', [CourierChargeController::class, 'update'])
        ->middleware('permission:manage cities');

    Route::delete('/admin/courier-charges/{id}', [CourierChargeController::class, 'destroy'])
        ->middleware('permission:manage cities');
});


// =========================================================================
// SUPER ADMIN ONLY
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {

    // --- Role management ---
    Route::get('/roles-management', [RoleController::class, 'index'])
        ->middleware('permission:view roles');

    Route::post('/roles-management', [RoleController::class, 'store'])
        ->middleware('permission:create roles');

    Route::put('/roles-management/{id}', [RoleController::class, 'update'])
        ->middleware('permission:update roles');

    Route::delete('/roles-management/{id}', [RoleController::class, 'destroy'])
        ->middleware('permission:delete roles');

    // --- Permission management ---
    Route::get('/permission-roles', [RoleController::class, 'getRoles'])
        ->middleware('permission:assign permissions');

    Route::get('/permissions', [PermissionController::class, 'getPermissions'])
        ->middleware('permission:assign permissions');

    Route::get('/roles-management/{id}/permissions', [PermissionController::class, 'getRolePermissions'])
        ->middleware('permission:assign permissions');

    Route::post('/assign-permissions', [PermissionController::class, 'assignPermissions'])
        ->middleware('permission:assign permissions');

    // --- Complaints admin view / resolution ---
    // Per the business rule that only Super Admin responds to complaints,
    // these two are gated to super_admin only. The controller must still
    // separately allow a complainant to see/reply on THEIR OWN complaint
    // via the /complaints/{complaint} and /complaints/{complaint}/messages
    // routes above (auth:sanctum only) — a plain permission check can't
    // express "owner OR super admin", so that check belongs in
    // ComplaintController (e.g. abort(403) unless
    // $complaint->user_id === auth()->id() OR auth()->user()->can('manage complaints')).
    Route::get('/complaints', [ComplaintController::class, 'index'])
        ->middleware('permission:view complaints');

    Route::patch('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])
        ->middleware('permission:manage complaints');
});


// =========================================================================
// DASHBOARDS
// =========================================================================

Route::middleware(['auth:sanctum', 'permission:view customer dashboard'])
    ->get('/customer/dashboard', [DashboardController::class, 'customerDashboard']);

Route::middleware(['auth:sanctum', 'permission:view seller dashboard'])
    ->get('/seller/dashboard', [DashboardController::class, 'sellerDashboard']);

Route::middleware(['auth:sanctum', 'permission:view admin dashboard'])
    ->get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);
