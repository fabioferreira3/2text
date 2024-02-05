<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Repositories\CheckoutRepository;
use Livewire\Component;


class Purchase extends Component
{
    public $title;
    public $units = 100;
    public $totalPrice = 10.00;
    public $products = [];
    public $discountTier = '';
    public $displayCalculator = false;
    public $discount = 0;

    protected $listeners = ['closeUnitCalculator'];

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

    public function messages()
    {
        return [
            'units.min' => 'You may purchase a minimum of 100 units'
        ];
    }

    public function render()
    {
        return view('livewire.purchase')->title('Purchase units');
    }

    public function updatedUnits($value)
    {
        $this->discountTier = '';
        $this->discount = 0;
        if ($value >= 500 && $value < 1000) {
            $value = $value - ($value * 0.03);
            $this->discountTier = '(3% discount)';
            $this->discount = 3;
        } elseif ($value >= 1000 && $value < 10001) {
            $value = $value - ($value * 0.07);
            $this->discountTier = '(7% discount)';
            $this->discount = 7;
        }
        $this->totalPrice = number_format($value * 0.10, 2);
    }

    public function processPurchase()
    {
        $this->validate();
        $checkoutRepo = new CheckoutRepository();

        return $checkoutRepo->processUnitPurchase($this->units, $this->discount);
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

    public function closeUnitCalculator()
    {
        $this->displayCalculator = false;
    }
}
