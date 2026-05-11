<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Ship;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class OrderTotalSheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_order_list_shows_hasil_button(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder();

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee(route('admin.orders.total-sheet', $order), false);
        $response->assertSee('Hasil');
    }

    public function test_admin_can_download_order_total_sheet(): void
    {
        Excel::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder();

        $response = $this->actingAs($admin)->get(route('admin.orders.total-sheet', $order));

        $response->assertOk();
        Excel::assertDownloaded('hasil-total-harga-order-00001.xlsx');
    }

    private function createOrder(): Order
    {
        $customer = User::factory()->create([
            'name' => 'PT Laut Jaya',
            'company_name' => 'PT Laut Jaya',
        ]);

        $ship = Ship::create([
            'user_id' => $customer->id,
            'name' => 'KM Nusantara',
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Segar',
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'name' => 'Beras Premium',
            'harga_jual' => 25000,
            'unit' => 'karung',
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'ship_id' => $ship->id,
            'total_price' => 50000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 25000,
            'subtotal' => 50000,
        ]);

        return $order;
    }
}
