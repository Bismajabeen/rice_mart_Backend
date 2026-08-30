<?php

namespace App\Http\Controllers;

use App\Models\ShopReview;
use App\Models\OrderItem;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class ShopReviewController extends Controller
{
    public function store(Request $request)
    {

       $request->validate([

        'order_item_id'=>'required',
        'rating'=>'required|integer|min:1|max:5',
        'review'=>'nullable|string'
        ]);


       $item = OrderItem::with('shop.user')->findOrFail(
      $request->order_item_id
      );


     // security check
        if($item->order->user_id != auth()->id())
       {
         return response()->json([
          'message'=>'Unauthorized'
          ],403);
        }


      // only delivered items
      if($item->status != 'delivered')
       {
         return response()->json([
           'message'=>'You can review after delivery'
         ],400);
       }



      $review = ShopReview::create([

      'customer_id'=>auth()->id(),

      'order_item_id'=>$item->id,

     'shop_id'=>$item->shop_id,

     'rating'=>$request->rating,

     'review'=>$request->review

      ]);

      // =========================
      // NOTIFY SELLER — new review on their shop
      // =========================
      if ($item->shop && $item->shop->user) {
          NotificationService::send(
              $item->shop->user,
              'review',
              'New review',
              'You received a ' . $request->rating . '-star review on your shop.',
              ['shop_id' => $item->shop_id, 'review_id' => $review->id]
          );
      }

      return response()->json([
        'message'=>'Review submitted successfully',
        'review'=>$review
      ],201);


    }

    // =========================
    // GET REVIEWS FOR A SHOP
    // Any authenticated user (customer, seller, admin) can view a
    // shop's reviews — customers need this on the shop details page
    // before purchasing. No ownership check is required for reading.
    // =========================
    public function shopReviews(Request $request, $shopId)
    {
        $shop = Shop::findOrFail($shopId);

        $reviews = ShopReview::with('customer:id,name')
            ->where('shop_id', $shopId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'average_rating' => $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : 0,
            'total_reviews' => $reviews->count(),
        ]);
    }

}
