<?php

use App\Models\Product;

it('orders products by level in ascending order', function () {
    $product1 = Product::factory()->create(['level' => 2]);
    $product2 = Product::factory()->create(['level' => 1]);
    $product3 = Product::factory()->create(['level' => 3]);

    $orderedProducts = Product::levelOrdered()->get();

    expect($orderedProducts->first()->id)->toBe($product2->id);
    expect($orderedProducts[1]->id)->toBe($product1->id);
    expect($orderedProducts->last()->id)->toBe($product3->id);
});

it('can filter products by external_id', function () {
    $targetExternalId = 'target-id';
    $otherExternalId = 'other-id';

    Product::factory()->create(['external_id' => $targetExternalId]);
    Product::factory()->create(['external_id' => $otherExternalId]);

    $filteredProducts = Product::ofExternalId($targetExternalId)->get();

    expect($filteredProducts)->toHaveCount(1);
    expect($filteredProducts->first()->external_id)->toBe($targetExternalId);
});
