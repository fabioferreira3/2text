<?php

namespace App\Livewire\Product;

use App\Helpers\SupportHelper;
use App\Models\Product;
use App\Repositories\CheckoutRepository;
use Illuminate\Support\Facades\App;
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
            'units.min' => __('validation.purchase_min', ['amount' => 100]),
            'units.max' => __('validation.purchase_max', ['amount' => 10000])
        ];
    }

    public function render()
    {
        return view('livewire.purchase')->title(__('checkout.purchase_units'));
    }

    public function updatedUnits($unitsAmount)
    {
        $this->discountTier = '';
        $this->discount = 0;
        if ($unitsAmount >= 500 && $unitsAmount < 1000) {
            $this->discount = 3;
        } elseif ($unitsAmount >= 1000 && $unitsAmount < 10001) {
            $this->discount = 7;
        }
        $this->discountTier = $this->discount ? __('checkout.%_discount', ['percentage' => $this->discount]) : '';

        $discountedUnits = SupportHelper::subPercent($unitsAmount, $this->discount);
        $this->totalPrice = number_format($discountedUnits * 0.10, 2);
    }

    public function processPurchase()
    {
        $this->validate();
        $checkoutRepo = App::make(CheckoutRepository::class);

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
