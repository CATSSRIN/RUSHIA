<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['name' => 'Toko Langgeng'],
            ['name' => 'Tip Top'],
            ['name' => 'Sentra Ikan Laut'],
            ['name' => 'Pasar'],
            ['name' => 'Paskomnas'],
            ['name' => 'Sari Roti'],
            ['name' => 'Toko Ronny'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(['name' => $vendor['name']], $vendor);
        }
    }
}
