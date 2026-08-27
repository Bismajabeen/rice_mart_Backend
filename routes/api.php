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
// For testing image upload
use App\Http\Controllers\TestImageController;
use App\Http\Controllers\ShopReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AiRecommendationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CourierChargeController;
//admin settings controller
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SellerRemovalController;

//
// =========================================
// AUTH ROUTES
// =========================================
//
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

//
// =========================================
// AUTHENTICATED USER
// =========================================
//

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/delete-account/request', [ProfileController::class, 'requestDeletion']);
    Route::post('/delete-account/confirm', [ProfileController::class, 'confirmDeletion']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/update-profile', [ProfileController::class, 'update']);
});

//
// =========================================
// PUBLIC RICE CATEGORIES
// =========================================
//

Route::get('/rice-categories', [
    RiceCategoryController::class,
    'index'
]);

Route::get('/all-rice-categories', [
    RiceCategoryController::class,
    'allCategories'
]);

//
// =========================================
// PUBLIC PRODUCTS
// =========================================
//

Route::get('/all-products', [
    ProductController::class,
    'allProducts'
]);

Route::get('/shop-products/{shopId}', [
    ProductController::class,
    'shopProducts'
]);

//
// =========================================
// PUBLIC: CITIES + DELIVERY CHARGES (checkout dropdown)
// =========================================
//

Route::get('/cities-with-charges', [
    CityController::class,
    'citiesWithCharges'
]);

// routes/api.php — inside your authenticated (non-admin) group
Route::get('/delivery-charges', [CourierChargeController::class, 'deliverableCities']);

//
// =========================================
// AUTHENTICATED ROUTES
// =========================================
//

Route::middleware('auth:sanctum')->group(function () {

    //
    // =========================================
    // CUSTOMER FEATURES
    // =========================================
    //

    Route::post('/shops', [
        ShopController::class,
        'store'
    ])->middleware('permission:create shop');

    //
    // =========================================
    // ORDERS
    // =========================================
    //

    Route::post('/checkout', [
        OrderController::class,
        'checkout'
    ])->middleware('permission:checkout orders');

    Route::get('/my-orders', [
        OrderController::class,
        'myOrders'
    ])->middleware('permission:view own orders');

    Route::get('/active-orders', [
        OrderController::class,
        'activeOrders'
    ])->middleware('permission:view own orders');

    Route::get('/order-history', [
        OrderController::class,
        'orderHistory'
    ])->middleware('permission:view own orders');

    Route::put('/order-item/{id}/confirm-received', [
      OrderController::class,
      'confirmReceived'
    ])->middleware('permission:view own orders');

    //
    // =========================================
    // SHOPS
    // =========================================
    //
    Route::middleware('auth:sanctum')->get('/my-shop', [ShopController::class, 'myShop']);

    Route::put('/shops/{id}', [
        ShopController::class,
        'update'
    ])->middleware('permission:update own shop');

    Route::post('/shops/{id}/delete/request', [
        ShopController::class,
        'requestShopDeletion'
    ])->middleware('permission:delete own shop');

    Route::post('/shops/{id}/delete/confirm', [
        ShopController::class,
        'confirmShopDeletion'
    ])->middleware('permission:delete own shop');

    //
    // =========================================
    // PRODUCTS
    // =========================================
    //

    Route::post('/products', [
        ProductController::class,
        'store'
    ])->middleware('permission:create products');

    Route::put('/products/{id}', [
        ProductController::class,
        'update'
    ])->middleware('permission:update own products');

    Route::delete('/products/{id}', [
        ProductController::class,
        'delete'
    ])->middleware('permission:delete own products');

    //
    // =========================================
    // SELLER ORDERS
    // =========================================
    //

    Route::get('/seller-orders', [
        SellerOrderController::class,
        'sellerOrders'
    ])->middleware('permission:view shop orders');

    Route::put('/seller/order-item/{id}/status', [
        SellerOrderController::class,
        'updateStatus'
    ])->middleware('permission:update order status');

    //========================= 
    // Seller Payout Details 
    //=========================
    
    Route::put('/my-shop/payout-details', [
       ShopController::class,
       'updatePayoutDetails'
    ])->middleware('permission:update own shop');

    // =========================
    // Seller See their payouts
    // =========================

    Route::get('/seller/payouts', [
     PayoutController::class,
     'sellerPayouts'
    ])->middleware('permission:view shop orders');

    //
    // =========================================
    // ADMIN CREATE SELLER
    // =========================================
    //

    Route::post('/admin/create-seller', [
        ShopController::class,
        'adminCreateSeller'
    ])->middleware('permission:create sellers');

    //
    // =========================================
    // USERS MANAGEMENT
    // =========================================
    //

    Route::get('/users', [
        UserController::class,
        'index'
    ])->middleware('permission:view users');

    Route::get('/roles', [
        UserController::class,
        'roles'
    ])->middleware('permission:view users');

    Route::post('/users', [
        UserController::class,
        'store'
    ])->middleware('permission:create users');

    Route::put('/users/{id}', [
        UserController::class,
        'update'
    ])->middleware('permission:update users');

    Route::delete('/users/{id}', [
        UserController::class,
        'destroy'
    ])->middleware('permission:delete users');

    //
    // =========================================
    // ROLE MANAGEMENT
    // =========================================
    //

    Route::get('/roles-management', [
        RoleController::class,
        'index'
    ])->middleware('permission:create roles');

    Route::post('/roles-management', [
        RoleController::class,
        'store'
    ])->middleware('permission:create roles');

    Route::put('/roles-management/{id}', [
        RoleController::class,
        'update'
    ])->middleware('permission:update roles');

    Route::delete('/roles-management/{id}', [
        RoleController::class,
        'destroy'
    ])->middleware('permission:delete roles');

    //
    // =========================================
    // PERMISSION MANAGEMENT
    // =========================================
    //

    Route::get('/permission-roles', [
        RoleController::class,
        'getRoles'
    ])->middleware('permission:assign permissions');

    Route::get('/permissions', [
        PermissionController::class,
        'getPermissions'
    ])->middleware('permission:assign permissions');

    Route::get('/roles-management/{id}/permissions', [
        PermissionController::class,
        'getRolePermissions'
    ])->middleware('permission:assign permissions');

    Route::post('/assign-permissions', [
        PermissionController::class,
        'assignPermissions'
    ])->middleware('permission:assign permissions');

    //
    // =========================================
    // CATEGORY MANAGEMENT
    // =========================================
    //

    Route::put('/rice-categories/{id}/status', [
        RiceCategoryController::class,
        'updateStatus'
    ])->middleware('permission:update categories');

    //
    // =========================================
    // SHOP APPROVAL MANAGEMENT
    // =========================================
    //

    Route::get('/pending-shops', [
        ShopController::class,
        'pendingShops'
    ])->middleware('permission:view all shops');

    Route::get('/approved-shops', [
        ShopController::class,
        'approvedShops'
    ]);
    // ->middleware('permission:view all shops');

    Route::post('/shops/{id}/approve', [
        ShopController::class,
        'approve'
    ])->middleware('permission:approve shops');

    Route::delete('/shops/{id}', [
        ShopController::class,
        'reject'
    ])->middleware('permission:reject shops');

    Route::get('/rejected-shops', [
       ShopController::class,
       'rejectedShops'
    ])->middleware('permission:view all shops');


    // request correction route for shops
    Route::post('/shops/{id}/request-correction', [
        ShopController::class,
        'requestCorrection'
    ]);

    //
    // =========================================
    // ADMIN ORDERS
    // =========================================
    //

    Route::get('/admin/orders', [
        OrderController::class,
        'adminOrders'
    ])->middleware('permission:view all orders');

    Route::get('/admin/order-history',[
        OrderController::class,
        'adminOrderHistory'
    ])->middleware('permission:view all orders');

    Route::put('/admin/order-item/{id}/status', [
        OrderController::class,
        'adminUpdateOrderItemStatus'
    ])->middleware('permission:update any order status');

    Route::put('/orders/{id}/status', [
        OrderController::class,
        'updateStatus'
    ])->middleware('permission:update any order status');

    // =========================================
    // ADMIN SELLER REMOVAL
    // =========================================

    Route::post('/admin/shops/{id}/remove-seller', [
        SellerRemovalController::class,
        'remove'
    ])->middleware('permission:remove sellers');

    Route::get('/removed-shops', [
        SellerRemovalController::class,
        'removedShops'
    ])->middleware('permission:view all shops');
});

//
// =========================================
// DASHBOARDS
// =========================================
//

Route::middleware([
    'auth:sanctum',
    'permission:view customer dashboard'
])->get('/customer/dashboard', [
    DashboardController::class,
    'customerDashboard'
]);

Route::middleware([
    'auth:sanctum',
    'permission:view seller dashboard'
])->get('/seller/dashboard', [
    DashboardController::class,
    'sellerDashboard'
]);

Route::middleware([
    'auth:sanctum',
    'permission:view admin dashboard'
])->get('/admin/dashboard', [
    DashboardController::class,
    'adminDashboard'
]);

//
// =========================================
// NOTIFICATIONS
// =========================================
//

   Route::middleware([
       'auth:sanctum',
       'permission:view own notifications'
    ])->group(function () {

    Route::get('/notifications', [
        NotificationController::class,
        'myNotifications'
    ]);

    Route::put('/notifications/{id}/read', [
        NotificationController::class,
        'markAsRead'
    ]);
});
    // =========================================
    // admin settings routes
    // =========================================
    Route::middleware('auth:sanctum')->group(function () {
       Route::get('/payment-settings', [
           PaymentSettingController::class,
           'paymentSettings'
        ]);
       Route::post('/admin/payment-settings', [
           PaymentSettingController::class,
          'adminUpdatePaymentSettings'
        ]);
    });


    // For testing image upload

   Route::post('/test-image', [TestImageController::class, 'upload']);

   Route::middleware(['auth:sanctum'])->group(function () {

    // =========================
    // ADMIN PAYMENT MANAGEMENT
    // =========================
    Route::get('/admin/payments', [
        PaymentController::class,
        'adminPayments'
    ]);
    Route::put('/admin/payments/{id}/status', [
        PaymentController::class,
        'updatePaymentStatus'
    ]);

    // =========================
    // ADMIN — LIST ALL PAYOUTS
    // =========================

    Route::get('/admin/payouts', [
        PayoutController::class,
        'index'
    ])->middleware('permission:update any order status');

    Route::post('/admin/payouts/{id}/pay', [
       PayoutController::class,
       'pay'
    ])->middleware('permission:update any order status');

    // shop review route

    Route::post('/shop-review',[
        ShopReviewController::class,
        'store'
    ]);

    //
   // =========================================
   // CITY MANAGEMENT
   // =========================================
   //

   Route::get('/admin/cities', [
       CityController::class,
       'index'
    ]);

   Route::post('/admin/cities', [
       CityController::class,
       'store'
    ]);

   Route::put('/admin/cities/{id}', [
      CityController::class,
      'update'
    ]);

   Route::delete('/admin/cities/{id}', [
      CityController::class,
      'destroy'
    ]);

   //
   // =========================================
   // COURIER CHARGES MANAGEMENT
   // =========================================
   //

   Route::get('/admin/courier-charges', [
      CourierChargeController::class,
      'index'
    ]);

   Route::post('/admin/courier-charges', [
      CourierChargeController::class,
      'store'
    ]);

   Route::put('/admin/courier-charges/{id}', [
      CourierChargeController::class,
      'update'
    ]);

   Route::delete('/admin/courier-charges/{id}', [
       CourierChargeController::class,
      'destroy'
    ]);

    // cities
    Route::get(
      '/admin/available-cities',
      [CityController::class, 'availableCities']
    );

});

// =========================================
// CHAT ROUTES
// =========================================

Route::middleware('auth:sanctum')->group(function () {

    // Chat routes
    Route::get('/conversations',                  [ChatController::class, 'index']);
    Route::post('/conversations/start',           [ChatController::class, 'start']);
    Route::get('/conversations/{id}/messages',    [ChatController::class, 'messages']);
    Route::post('/conversations/{id}/messages',   [ChatController::class, 'send']);
});

// AI Recommendation (no auth required — public endpoint)
Route::post('/ai-recommendation', [AiRecommendationController::class, 'recommend']);

//===============
// COMPLAINTS
//===============   
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/settings/emergency-contact', [SettingController::class, 'emergencyContact']);

    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/complaints/my', [ComplaintController::class, 'myComplaints']);
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);
    Route::post('/complaints/{complaint}/messages', [ComplaintController::class, 'addMessage']);
    Route::patch('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus']);
});