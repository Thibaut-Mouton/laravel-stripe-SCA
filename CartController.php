<?php

namespace App\Http\Controllers;

use App\Entities\Boutique;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;


class CartController extends Controller
{

    // When the customer wants to pay his cart, this function is triggered
    public function stripeSCA()
    {
        \Stripe\Stripe::setApiKey('your-key');
        $currentCart = Cart::content();
        $intent = \Stripe\PaymentIntent::create([
            'payment_method_types' => ['card'],
            'amount' => Cart::total() * 100,
            'currency' => 'EUR',
        ]);
        // You will need intent to pass PaymentIntent ID in payment form
        return view('shop.checkout', compact('intent', 'currentCart'));
    }

    // After payment, you can trigger email automatically
    public function check(Request $request)
    {
        \Stripe\PaymentIntent::update(
            $request->intent_id,
            ['receipt_email' => $request->mail]
        );

        $customer = [
            'mail' => $request->mail,
            'city' => $request->city,
            'postalcode' => $request->postalcode,
            'address' => $request->address,
        ];
        Cart::destroy();
        return view('shop.success', compact('customer'));
    }
}
