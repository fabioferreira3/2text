<?php

namespace App\Http\Livewire\Product;

use App\Models\Product;
use Livewire\Component;


class Purchase extends Component
{
    public $title;
    public $units = 100;
    public $totalPrice = 10.00;
    public $selectedProduct = null;
    public $products = [];

    public function __construct()
    {
        $this->products = Product::where('name', '!=', 'unit')->get();
    }


    public function rules()
    {
        return [
            'units' => 'integer|min:100|max:10000'
        ];
    }

    public function render()
    {
        return view('livewire.purchase')->layout('layouts.app', ['title' => 'eita']);
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

    public function selectProduct()
    {
        return auth()->user()
            ->newSubscription('default', 'price_1Obw79EjLWGu0g9vrEaHEpxm')
            ->checkout([
                'success_url' => route('checkout-success'),
                'cancel_url' => route('purchase'),
            ]);
    }
}
