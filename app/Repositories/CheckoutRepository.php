<?php

namespace App\Repositories;

use App\Models\Product;

class CheckoutRepository
{
    public function processUnitPurchase(int $unitsCount, int $discount = 0)
    {
        $user = auth()->user();
        $unitProduct = Product::where('name', 'unit')->firstOrFail();
        if ($discount === 3) {
            $user = $user->withCoupon('LRRrmSGu');
        } elseif ($discount === 7) {
            $user = $user->withCoupon('elDqv3of');
        }

        return $user->checkout(
            [$unitProduct->meta['price_id'] => $unitsCount],
            [
                'success_url' => route('checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase'),
                'invoice_creation' => [
                    'enabled' => true
                ],
                'metadata' => [
                    'product_id' => $unitProduct->id,
                    'quantity' => $unitsCount
                ]
            ]
        );
    }
}
