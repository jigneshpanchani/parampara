<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sell;
use App\Models\SellItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSellPurchaseTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndUser();
        $this->user = User::first();
    }

    private function seedRolesAndUser(): void
    {
        if (\DB::table('roles')->count() === 0) {
            \DB::table('roles')->insert([
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (User::count() === 0) {
            User::create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@parampara.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'status' => 1,
                'email_verified_at' => now(),
            ]);
        }
    }

    /** @test */
    public function product_index_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
    }

    /** @test */
    public function product_create_and_store_works(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.products.store'), [
            'product_name' => 'Test Product',
            'product_code' => 'TP001',
            'description' => 'Test description',
            'base_price_min' => 100,
            'base_price_max' => 150,
            'sell_price' => 120,
        ]);
        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'product_name' => 'Test Product',
            'product_code' => 'TP001',
            'sell_price' => 120,
        ]);
    }

    /** @test */
    public function product_with_sales_cannot_be_deleted(): void
    {
        $product = Product::create([
            'product_name' => 'Sellable Product',
            'product_code' => 'SP001',
            'base_price_min' => 50,
            'base_price_max' => 100,
            'sell_price' => 80,
            'stock_quantity' => 10,
        ]);

        $sell = Sell::create([
            'sell_date' => now(),
            'total_amount' => 80,
            'payment_mode' => 'cash',
            'payment_status' => 'paid',
            'amount_paid' => 80,
            'pending_amount' => 0,
        ]);

        SellItem::create([
            'sell_id' => $sell->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'selling_price' => 80,
            'total_price' => 80,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.products.destroy', $product));
        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /** @test */
    public function product_without_history_can_be_deleted(): void
    {
        $product = Product::create([
            'product_name' => 'Orphan Product',
            'product_code' => 'OP001',
            'base_price_min' => 10,
            'base_price_max' => 20,
            'sell_price' => 15,
            'stock_quantity' => 0,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.products.destroy', $product));
        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function sell_index_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.sells.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.sells.index');
    }

    /** @test */
    public function sell_create_and_store_works(): void
    {
        $product = Product::create([
            'product_name' => 'Sell Product',
            'product_code' => 'SELL001',
            'base_price_min' => 50,
            'base_price_max' => 100,
            'sell_price' => 75,
            'stock_quantity' => 20,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.sells.store'), [
            'sell_date' => now()->format('Y-m-d'),
            'product_id' => [$product->id],
            'quantity' => [2],
            'selling_price' => [75],
            'payment_mode' => 'cash',
            'amount_paid' => 150,
        ]);

        $response->assertRedirect(route('admin.sells.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sells', ['total_amount' => 150]);
        $product->refresh();
        $this->assertEquals(18, $product->stock_quantity);
    }

    /** @test */
    public function purchase_index_loads(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.purchases.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.purchases.index');
    }

    /** @test */
    public function purchase_create_and_store_works(): void
    {
        $product = Product::create([
            'product_name' => 'Purchase Product',
            'product_code' => 'PURCH001',
            'base_price_min' => 30,
            'base_price_max' => 50,
            'sell_price' => 45,
            'stock_quantity' => 0,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.purchases.store'), [
            'purchase_date' => now()->format('Y-m-d'),
            'supplier_name' => 'Test Supplier',
            'bill_type' => 'gst',
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'purchase_price' => 35,
                ],
            ],
            'transportation_cost' => 50,
        ]);

        $response->assertRedirect(route('admin.purchases.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('purchases', ['supplier_name' => 'Test Supplier']);
        $purchase = Purchase::where('supplier_name', 'Test Supplier')->first();
        $this->assertEquals(400, $purchase->total_amount); // 10*35 + 50

        $product->refresh();
        $this->assertEquals(10, $product->stock_quantity);
    }

    /** @test */
    public function sell_price_update_via_ajax_works(): void
    {
        $product = Product::create([
            'product_name' => 'Price Update Product',
            'product_code' => 'PU001',
            'base_price_min' => 100,
            'base_price_max' => 200,
            'sell_price' => 150,
            'stock_quantity' => 5,
        ]);

        $response = $this->actingAs($this->user)->patch(
            route('admin.products.update-sell-price', $product),
            ['sell_price' => 175],
            ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'sell_price' => 175]);
        $product->refresh();
        $this->assertEquals(175, $product->sell_price);
    }
}
