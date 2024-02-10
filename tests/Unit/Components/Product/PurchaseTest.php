<?php

use App\Helpers\SupportHelper;
use App\Livewire\Product\Purchase;
use App\Models\Product;
use App\Models\User;
use App\Repositories\CheckoutRepository;
use Livewire\Livewire;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->products = Product::factory(3)->create();
    $this->component = actingAs($this->authUser)->livewire(Purchase::class);
    $this->mockedCheckoutRepository = Mockery::mock(CheckoutRepository::class);
    $this->app->instance(CheckoutRepository::class, $this->mockedCheckoutRepository);
});

describe(
    'Purchase component',
    function () {
        it('renders the purchase view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.purchase')
                ->assertViewHas('products', function ($products) {
                    return $products->count() === 3;
                })
                ->assertSet('units', 100)
                ->assertSet('totalPrice', 10.00)
                ->assertSet('discountTier', '')
                ->assertSet('displayCalculator', false);
        });

        it('calculates discount based on unit selection changes when buying 200 units', function () {
            $this->component->set('units', 200)
                ->assertSet('discount', 0)
                ->assertSet('discountTier', '');
        });

        it(
            'calculates discount based on unit selection changes when buying more than 500 and less than 1000 units',
            function ($amount) {
                $this->mockedCheckoutRepository->shouldReceive('processUnitPurchase')
                    ->once()
                    ->andReturn(null);

                $this->component->set('units', $amount)
                    ->assertSet('discount', 3)
                    ->assertSet('totalPrice', number_format(SupportHelper::subPercent($amount, 3) * 0.10, 2))
                    ->assertSet('discountTier', __('checkout.%_discount', ['percentage' => 3]))
                    ->call('processPurchase')
                    ->assertHasNoErrors();
            }
        )->with([501, 600, 753, 891, 999]);

        it('calculates discount based on unit selection changes when buying more than 100 units', function ($amount) {
            $this->mockedCheckoutRepository->shouldReceive('processUnitPurchase')
                ->once()
                ->andReturn(null);

            $this->component->set('units', $amount)
                ->assertSet('discount', 7)
                ->assertSet('totalPrice', number_format(SupportHelper::subPercent($amount, 7) * 0.10, 2))
                ->assertSet('discountTier', __('checkout.%_discount', ['percentage' => 7]))
                ->call('processPurchase')
                ->assertHasNoErrors();
        })->with([1000, 1500, 4500, 9999]);

        it('fails validation when more than 10000 units', function ($amount) {
            $this->component->set('units', $amount)
                ->call('processPurchase')
                ->assertHasErrors('units');
        })->with([10001, 15000, 45000, 99999, 100000]);

        it('set display calculator flag to false after event', function () {
            $this->component->set('displayCalculator', true)
                ->assertSet('displayCalculator', true)
                ->dispatch('closeUnitCalculator')
                ->assertSet('displayCalculator', false);
        });

        it('redirects when selecting a product and the user has a spark plan', function () {
            $user = User::factory()->withSubscription($this->products[0]->meta['pr cice_id'])->create();
            $this->actingAs($user)->livewire(Purchase::class)->call('selectProduct', $this->products[0]->id);
        });
    }
)->group('product');
