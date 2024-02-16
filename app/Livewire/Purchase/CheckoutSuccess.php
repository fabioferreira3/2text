<?php

namespace App\Livewire\Purchase;

use App\Helpers\SupportHelper;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Livewire\Component;

/**
 * @codeCoverageIgnore
 */
class CheckoutSuccess extends Component
{
    public $productId;
    public $quantity;
    public $totalAmount;

    public function mount(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('home');
        }

        $sessionData = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
        $this->productId = $sessionData['metadata']['product_id'] ?? null;
        $this->quantity = $sessionData['metadata']['quantity'] ?? null;
        $this->totalAmount = SupportHelper::formatCentsToDollars($sessionData['amount_total']);
    }

    public function render()
    {
        return view('livewire.purchase.checkout-success');
    }
}
