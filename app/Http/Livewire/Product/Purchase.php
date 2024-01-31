<?php

namespace App\Http\Livewire\Product;

use App\Models\Product;
use Livewire\Component;


class Purchase extends Component
{
    public $title;
    public $units = 100;
    public $totalPrice = 10.00;
    public $products = [];

    public function __construct()
    {
        $this->products = Product::where('name', '!=', 'unit')->levelOrdered()->get();
    }


    public function rules()
    {
        return [
            'units' => 'integer|min:100|max:10000'
        ];
    }

    public function render()
    {
        return view('livewire.purchase')->layout('layouts.app', ['title' => 'Purchase units']);
    }

    public function updatedUnits($value)
    {
        $this->totalPrice = $value * 0.10;
    }

    public function processPurchase()
    {
        $this->validate();
        $unitProduct = Product::where('name', 'unit')->firstOrFail();

        return auth()->user()->checkout(
            ['price_1OdeFqEjLWGu0g9vVJeUQOso' => $this->units],
            [
                'success_url' => route('checkout-success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchase'),
                'invoice_creation' => [
                    'enabled' => true
                ],
                'metadata' => [
                    'product_id' => $unitProduct->id,
                    'quantity' => $this->units
                ]
            ]
        );
    }

    public function selectProduct(string $productId)
    {
        if (auth()->user()->sparkPlan()) {
            return redirect()->to('/billing');
        }

        $product = $this->products->find($productId);
        return auth()->user()
            ->newSubscription('default', $product->meta['price_id'])
            ->checkout([
                'success_url' => route('checkout-success'),
                'cancel_url' => route('purchase'),
            ]);
    }
}
