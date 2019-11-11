<?php

namespace App\Http\Controllers;

use App\Entities\Boutique;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;


class CartController extends Controller
{

    // When you click on "
    public function stripeSCA()
    {
        \Stripe\Stripe::setApiKey('your-key');
        $currentCart = Cart::content();
        $intent = \Stripe\PaymentIntent::create([
            'payment_method_types' => ['card'],
            'amount' => Cart::total() * 100,
            'currency' => 'EUR',
        ]);

        return view('shop.checkout', compact('intent', 'currentCart'));
    }

    // After payment
    public function check(Request $request)
    {
        foreach (Cart::content() as $item) {
            $decreaseDB = Boutique::where('id', '=', $item->id)->first();
            if (isset($decreaseDB)) {
                $decreaseDB->quantite = $decreaseDB->quantite - $item->qty;
                $decreaseDB->save();
            }
        }

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
