<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductMarginTest extends TestCase
{
    public function test_sale_price_with_zero_margin_equals_cost(): void
    {
        $product = new Product;

        $this->assertSame(10_000, $product->salePriceFromCostAndMargin(10_000, 0));
    }

    public function test_sale_price_with_thirty_percent_margin(): void
    {
        $product = new Product;

        $this->assertSame(13_000, $product->salePriceFromCostAndMargin(10_000, 30));
    }

    public function test_sale_price_with_full_margin_doubles_cost(): void
    {
        $product = new Product;

        $this->assertSame(20_000, $product->salePriceFromCostAndMargin(10_000, 100));
    }

    public function test_margin_is_clamped_above_one_hundred(): void
    {
        $product = new Product;

        $this->assertSame(20_000, $product->salePriceFromCostAndMargin(10_000, 150));
    }

    public function test_margin_is_clamped_below_zero(): void
    {
        $product = new Product;

        $this->assertSame(10_000, $product->salePriceFromCostAndMargin(10_000, -5));
    }

    public function test_fractional_margin_rounds_to_nearest_minor_unit(): void
    {
        $product = new Product;

        $this->assertSame(13_333, $product->salePriceFromCostAndMargin(10_000, 33.33));
    }
}
