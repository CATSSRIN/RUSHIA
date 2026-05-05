<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Admin',
                'company_name' => 'Ship Order Admin',
                'email' => 'admin@email.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
        
        // Create warehouse user
        User::firstOrCreate([
            'name'         => 'Warehouse User',
            'email'        => 'warehouse@email.com',
            'password'     => Hash::make('12345678'),
            'is_warehouse' => true,
        ]);

        // Create regular user
        User::firstOrCreate([
            'name'         => 'Regular User',
            'email'        => 'user@email.com',
            'password'     => Hash::make('12345678'),
            'is_admin' => false,
        ]);

        // Sample vendors and products (Indonesian)
        $vendorData = [
            [
                'name' => 'Sumber Pangan Segar',
                'contact_name' => 'Budi Santoso',
                'email' => 'budi@sumberpangan.com',
                'phone' => '+62-21-5550100',
                'products' => [
                    ['name' => 'Beras Putih (25kg)', 'category' => 'Sembako', 'harga_jual' => 520000, 'unit' => 'karung'],
                    ['name' => 'Mie Kering (500g)', 'category' => 'Sembako', 'harga_jual' => 52000, 'unit' => 'bungkus'],
                    ['name' => 'Ikan Tuna Kaleng', 'category' => 'Makanan Kaleng', 'harga_jual' => 45000, 'unit' => 'kaleng'],
                    ['name' => 'Minyak Goreng (5L)', 'category' => 'Minyak & Bumbu', 'harga_jual' => 190000, 'unit' => 'botol'],
                    ['name' => 'Gula Pasir (5kg)', 'category' => 'Sembako', 'harga_jual' => 135000, 'unit' => 'karung'],
                ],
            ],
            [
                'name' => 'Minuman Bahari Nusantara',
                'contact_name' => 'Sari Dewi',
                'email' => 'sari@minumanbahari.com',
                'phone' => '+62-21-5550200',
                'products' => [
                    ['name' => 'Air Mineral (1.5L)', 'category' => 'Minuman', 'harga_jual' => 24000, 'unit' => 'botol'],
                    ['name' => 'Jus Jeruk (1L)', 'category' => 'Minuman', 'harga_jual' => 60000, 'unit' => 'karton'],
                    ['name' => 'Kopi Bubuk (500g)', 'category' => 'Minuman Panas', 'harga_jual' => 225000, 'unit' => 'bungkus'],
                    ['name' => 'Teh Hitam (100 kantong)', 'category' => 'Minuman Panas', 'harga_jual' => 105000, 'unit' => 'kotak'],
                ],
            ],
            [
                'name' => 'Rantai Dingin Susu Nusantara',
                'contact_name' => 'Agus Wijaya',
                'email' => 'agus@rantaidingin.com',
                'phone' => '+62-21-5550300',
                'products' => [
                    ['name' => 'Susu UHT (1L)', 'category' => 'Susu & Olahan', 'harga_jual' => 35000, 'unit' => 'karton'],
                    ['name' => 'Mentega (500g)', 'category' => 'Susu & Olahan', 'harga_jual' => 88000, 'unit' => 'bungkus'],
                    ['name' => 'Keju Slice', 'category' => 'Susu & Olahan', 'harga_jual' => 77000, 'unit' => 'bungkus'],
                    ['name' => 'Telur Ayam (30 butir)', 'category' => 'Susu & Olahan', 'harga_jual' => 144000, 'unit' => 'tray'],
                ],
            ],
        ];

        foreach ($vendorData as $vd) {
            $products = $vd['products'];
            unset($vd['products']);
            $vendor = Vendor::firstOrCreate(['name' => $vd['name']], $vd);
            foreach ($products as $pd) {
                Product::firstOrCreate(
                    ['vendor_id' => $vendor->id, 'name' => $pd['name']],
                    array_merge($pd, ['vendor_id' => $vendor->id, 'is_active' => true])
                );
            }
        }
    }
}
