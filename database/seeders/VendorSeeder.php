<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['name' => 'Toko Langgeng',
             'contact_name' => 'Bpk. Andika',
             'email' => '',
             'phone' => '+62 878-5234-4177'],
             
            ['name' => 'Tip Top',
             'contact_name' => 'Budi Santoso',
             'email' => 'budi.santoso@example.com',
             'phone' => '+62-21-5550456'],

            ['name' => 'Sentra Ikan Laut',
             'contact_name' => 'Ibu Ega',
             'email' => 'citra.dewi@example.com',
             'phone' => '+62 822-9831-7532'],

            ['name' => 'Pasar',
             'contact_name' => 'Bpk. Supri',
             'email' => 'dedi.prasetyo@example.com',
             'phone' => '+62 878-8880-3271'],

            ['name' => 'Paskomnas',
             'contact_name' => 'Ibu Yusni',
             'email' => 'eka.putri@example.com',
             'phone' => '+62 859-3910-4555'],

            ['name' => 'Sari Roti',
             'contact_name' => 'Bpk. Bandi',
             'email' => 'ferry.nugroho@example.com',
             'phone' => '+62 831-1767-5233'],

            ['name' => 'Toko Ronny',
             'contact_name' => 'Bpk. Ronny',
             'email' => '',
             'phone' => '+62 877-2200-0080'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(['name' => $vendor['name']], $vendor);
        }
    }
}
