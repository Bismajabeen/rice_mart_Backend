<?php

namespace App\Http\Controllers;

use App\Models\ShopReview;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ShopReviewController extends Controller
{
    public function store(Request $request)
    {

       $request->validate([

        'order_item_id'=>'required',
        'rating'=>'required|integer|min:1|max:5',
        'review'=>'nullable|string'
        ]);


       $item = OrderItem::findOrFail(
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
    
      return response()->json([
        'message'=>'Review submitted successfully',
        'review'=>$review
      ],201);


    }

}