<?php

use App\Models\Product;
use App\Models\User;
use App\Repositories\CheckoutRepository;
use Illuminate\Support\Facades\Auth;

describe(
    'CheckoutRepository component',
    function () {
        it('process a unit purchase and redirects to the checkout page', function () {
            $product = Product::factory()->create(['name' => 'unit', 'meta' => ['price_id' => 'price_123']]);
            $userMock = $this->createMock(User::class);
            $userMock->method('withCoupon')->willReturnSelf();
            $userMock->expects($this->once())->method('checkout')->with(
                $this->anything(),
                $this->callback(function ($options) use ($product) {
                    $successUrl = env('APP_URL') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}';
                    $cancelUrl = env('APP_URL') . '/purchase';
                    return $options['success_url'] === $successUrl &&
                        $options['cancel_url'] === $cancelUrl &&
                        $options['metadata']['product_id'] === $product->id &&
                        $options['metadata']['quantity'] === 47 &&
                        $options['invoice_creation']['enabled'] === true;
                })
            )->willReturn('checkout_response');

            Auth::shouldReceive('user')->once()->andReturn($userMock);

            $repository = new CheckoutRepository();
            $result = $repository->processUnitPurchase(47, 0);

            $this->assertEquals('checkout_response', $result);
        });
    }
)->group('product');
