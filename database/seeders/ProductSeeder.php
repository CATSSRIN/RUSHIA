<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // A - BAHAN KERING
            ['kode' => 'BK010002', 'name' => 'Agar - Agar', 'description' => 'Nutrijel Plain', 'unit' => 'Dos @12pack', 'harga_supplier' => 10000, 'harga_jual' => 62400, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK020003', 'name' => 'Beras Ketan Putih', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 20400, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK020005', 'name' => 'Beras Premium', 'description' => 'Platinum', 'unit' => 'Karung @5kg', 'harga_supplier' => 10000, 'harga_jual' => 85000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK020011', 'name' => 'Bumbu Pecel', 'description' => 'Sinti', 'unit' => 'Pcs @200gr', 'harga_supplier' => 10000, 'harga_jual' => 10800, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK070003', 'name' => 'Gula Merah/Kelapa', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 19200, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK070002', 'name' => 'Gula', 'description' => 'Gulaku Kuning', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 21000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK110006', 'name' => 'Kecap Manis', 'description' => 'ABC', 'unit' => 'Bottle @600ml', 'harga_supplier' => 10000, 'harga_jual' => 36000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK110020', 'name' => 'Kopi 2', 'description' => 'Kapal Api Silver', 'unit' => 'Pack @350gr', 'harga_supplier' => 10000, 'harga_jual' => 40800, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK110014', 'name' => 'Krupuk Udang', 'description' => 'Kecil', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 45720, 'category' => 'Bahan Kering', 'vendor' => 'Paskomnas'],
            ['kode' => 'BK130007', 'name' => 'Mie Instant Goreng', 'description' => 'Indomie', 'unit' => 'Dos', 'harga_supplier' => 10000, 'harga_jual' => 130800, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK130009', 'name' => 'Mie Instant Kuah', 'description' => 'Indomie', 'unit' => 'Dos', 'harga_supplier' => 10000, 'harga_jual' => 130800, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK130011', 'name' => 'Mie Telur', 'description' => '3 Ayam', 'unit' => 'Pcs @200gr', 'harga_supplier' => 10000, 'harga_jual' => 5400, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK130013', 'name' => 'Minyak Goreng', 'description' => 'Rose Brand', 'unit' => 'Galon @5 Ltr', 'harga_supplier' => 10000, 'harga_jual' => 126000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK190001', 'name' => 'Santan Kelapa', 'description' => 'Kara Sun', 'unit' => 'Pcs @200ml', 'harga_supplier' => 10000, 'harga_jual' => 19020, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK190004', 'name' => 'Saos Sambal', 'description' => 'Delmonte', 'unit' => 'Bottle @335ml', 'harga_supplier' => 10000, 'harga_jual' => 20000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK190011', 'name' => 'Sirup', 'description' => 'Marjan', 'unit' => 'Bottle @460ml', 'harga_supplier' => 10000, 'harga_jual' => 27990, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK190025', 'name' => 'Susu Kental Manis Putih', 'description' => 'Frisian Flag', 'unit' => 'Can @370gr', 'harga_supplier' => 10000, 'harga_jual' => 14160, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK200006', 'name' => 'Telur Asin', 'description' => null, 'unit' => 'Biji/Pc', 'harga_supplier' => 10000, 'harga_jual' => 4800, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK200007', 'name' => 'Telur Ayam Horn', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 35000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK200018', 'name' => 'Tepung Terigu', 'description' => 'Segitiga Biru', 'unit' => 'Pcs @1kg', 'harga_supplier' => 10000, 'harga_jual' => 14940, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK190012', 'name' => 'Slai Coklat', 'description' => 'Chocolate Peanut', 'unit' => 'Can @300gr', 'harga_supplier' => 10000, 'harga_jual' => 56580, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK110013', 'name' => 'Krupuk Puli', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 35000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK190015', 'name' => 'Slai Srikaya', 'description' => 'Morin Kaya', 'unit' => 'Can @330gr', 'harga_supplier' => 10000, 'harga_jual' => 41310, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BK190029', 'name' => 'Susu UHT Putih', 'description' => 'Frisian Flag', 'unit' => 'Pcs @950ml', 'harga_supplier' => 10000, 'harga_jual' => 22000, 'category' => 'Bahan Kering', 'vendor' => 'Toko Langgeng'],

            // B - BUMBU DAPUR
            ['kode' => 'BD020001', 'name' => 'Bawang Bombay', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 43200, 'category' => 'Bumbu', 'vendor' => 'Paskomnas'],
            ['kode' => 'BD020002', 'name' => 'Bawang Merah', 'description' => 'Super', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 60000, 'category' => 'Bumbu', 'vendor' => 'Pasar'],
            ['kode' => 'BD020004', 'name' => 'Bawang Putih', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 49680, 'category' => 'Bumbu', 'vendor' => 'Paskomnas'],
            ['kode' => 'BD040003', 'name' => 'Daun Sereh', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 10200, 'category' => 'Bumbu', 'vendor' => 'Paskomnas'],
            ['kode' => 'BD070001', 'name' => 'Garam', 'description' => 'Refina', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 17760, 'category' => 'Bumbu', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'BD100001', 'name' => 'Jahe Utuh', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 29040, 'category' => 'Bumbu', 'vendor' => 'Paskomnas'],
            ['kode' => 'BD100002', 'name' => 'Jeruk Nipis / Jeruk Pecel', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 25000, 'category' => 'Bumbu', 'vendor' => 'Pasar'],
            ['kode' => 'BD110001', 'name' => 'Kayu Manis', 'description' => null, 'unit' => '@100gr (Ons)', 'harga_supplier' => 10000, 'harga_jual' => 16872, 'category' => 'Bumbu', 'vendor' => 'Pasar'],

            // C - SAYUR & LAUK
            ['kode' => 'SY020002', 'name' => 'Bawang Pre', 'description' => 'Daun Bawang Besar', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 18000, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY020003', 'name' => 'Bayam/Spinach', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 15600, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY020008', 'name' => 'Buncis', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 15600, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY030003', 'name' => 'Cabe Merah Besar', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 81120, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY030004', 'name' => 'Cabe Merah Kecil', 'description' => 'Rawit Merah', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 54000, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY070001', 'name' => 'Gambas', 'description' => 'Oyong', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 19200, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY100001', 'name' => 'Jagung Kulit Utuh', 'description' => 'Jagung Manis Kulit', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 10320, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY110002', 'name' => 'Kacang Panjang', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 19200, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY110010', 'name' => 'Kemangi', 'description' => null, 'unit' => 'Ikat', 'harga_supplier' => 10000, 'harga_jual' => 7200, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY110014', 'name' => 'Kentang', 'description' => 'Sedang (Ab)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 25200, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY110016', 'name' => 'Kubis', 'description' => 'Kol', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 12000, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY130001', 'name' => 'Manisah/Labu Siam', 'description' => 'Labu Siam', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 7200, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY130003', 'name' => 'Mentimun', 'description' => 'Timun Sp', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 12000, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY160005', 'name' => 'Petai Kupas', 'description' => 'Petai Papan', 'unit' => 'Per Lembar', 'harga_supplier' => 10000, 'harga_jual' => 12000, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY190002', 'name' => 'Sawi Daging', 'description' => 'Pakcoy', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 15600, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY190003', 'name' => 'Sawi Hijau', 'description' => 'Caesim', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 15000, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY190005', 'name' => 'Seledri', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 30000, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY200001', 'name' => 'Tahu', 'description' => null, 'unit' => 'Pack @10pcs', 'harga_supplier' => 10000, 'harga_jual' => 7200, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY200003', 'name' => 'Tauge Besar', 'description' => 'Tauge', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 15000, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY200005', 'name' => 'Tempe', 'description' => 'Bungkus Daun Pisang', 'unit' => 'Pcs', 'harga_supplier' => 10000, 'harga_jual' => 7200, 'category' => 'Sayuran', 'vendor' => 'Pasar'],
            ['kode' => 'SY200010', 'name' => 'Tomat/Tomato', 'description' => 'Merah', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 24000, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],
            ['kode' => 'SY230001', 'name' => 'Wortel - Lokal', 'description' => 'Super', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 21000, 'category' => 'Sayuran', 'vendor' => 'Paskomnas'],

            // D - BUAH & KUDAPAN
            ['kode' => 'BA010001', 'name' => 'Alpokat', 'description' => 'KEMATANGAN 50%', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 37800, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA010006', 'name' => 'Apel Merah', 'description' => 'Fuji', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 62400, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA030001', 'name' => 'Cincau', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 18000, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA100002', 'name' => 'Jeruk/Orange', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 36000, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA110004', 'name' => 'Kolang-Kaling', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 24000, 'category' => 'Buah-buahan', 'vendor' => 'Paskomnas'],
            ['kode' => 'BA130002', 'name' => 'Melon', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 22200, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA140001', 'name' => 'Nenas', 'description' => 'KEMATANGAN 50%', 'unit' => 'Biji/Pc', 'harga_supplier' => 10000, 'harga_jual' => 15360, 'category' => 'Buah-buahan', 'vendor' => 'Paskomnas'],
            ['kode' => 'BA160003', 'name' => 'Pepaya/Papaya', 'description' => 'KEMATANGAN 50%', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 14400, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA160006', 'name' => 'Pisang Kepok', 'description' => 'KEMATANGAN 50%', 'unit' => 'Sisir', 'harga_supplier' => 10000, 'harga_jual' => 36000, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA160009', 'name' => 'Semangka (Tanpa Biji)', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 14400, 'category' => 'Buah-buahan', 'vendor' => 'Pasar'],
            ['kode' => 'BA210001', 'name' => 'Ubi Jalar', 'description' => null, 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 17760, 'category' => 'Buah-buahan', 'vendor' => 'Paskomnas'],

            // E - AYAM & DAGING SAPI
            ['kode' => 'AD010002', 'name' => 'Ayam Horn', 'description' => '1,3 Kg/Ekor (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 51000, 'category' => 'Daging & Unggas', 'vendor' => 'Pasar'],
            ['kode' => 'AD020004', 'name' => 'Bakso Sapi Premium', 'description' => null, 'unit' => 'Pack @50pc', 'harga_supplier' => 10000, 'harga_jual' => 78000, 'category' => 'Daging & Unggas', 'vendor' => 'Toko Langgeng'],
            ['kode' => 'AD040005', 'name' => 'Daging Rawonan/Soto', 'description' => 'Shankel (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 144000, 'category' => 'Daging & Unggas', 'vendor' => 'Sentra Ikan Laut'],
            ['kode' => 'AD040007', 'name' => 'Daging Utk Rendang,Empal', 'description' => 'Knuckel (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 146400, 'category' => 'Daging & Unggas', 'vendor' => 'Sentra Ikan Laut'],
            ['kode' => 'AD010001', 'name' => 'Ayam Dada Fillet', 'description' => 'Frozen', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 66000, 'category' => 'Daging & Unggas', 'vendor' => 'Sentra Ikan Laut'],

            // F - IKAN
            ['kode' => 'IK020002', 'name' => 'Bawal Hitam', 'description' => '600 - 800 Gr/Ekor (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 77160, 'category' => 'Ikan & Seafood', 'vendor' => 'Pasar'],
            ['kode' => 'IK030001', 'name' => 'Cumi Utuh', 'description' => '5 - 7 Cm/Ekor (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 90000, 'category' => 'Ikan & Seafood', 'vendor' => 'Pasar'],
            ['kode' => 'IK110001', 'name' => 'Kakap Skin On', 'description' => '600 - 1 Kg/Ekor (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 114000, 'category' => 'Ikan & Seafood', 'vendor' => 'Pasar'],
            ['kode' => 'IK120001', 'name' => 'Lele', 'description' => 'Ukuran Sedang (Fresh)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 36000, 'category' => 'Ikan & Seafood', 'vendor' => 'Pasar'],
            ['kode' => 'IK130001', 'name' => 'Mujair/Nila', 'description' => '6 - 7 Ekor/Kg (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 40000, 'category' => 'Ikan & Seafood', 'vendor' => 'Pasar'],
            ['kode' => 'IK160002', 'name' => 'Pindang Marning', 'description' => '2 - 3 Pcs/Pack (Dried)', 'unit' => 'Kotak/Box', 'harga_supplier' => 10000, 'harga_jual' => 12000, 'category' => 'Ikan & Seafood', 'vendor' => 'Pasar'],
            ['kode' => 'IK200002', 'name' => 'Tongkol / Tuna', 'description' => 'Tuna 1 - 1,1 Kg/Ekor (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 47160, 'category' => 'Ikan & Seafood', 'vendor' => 'Sentra Ikan Laut'],
            ['kode' => 'IK200004', 'name' => 'Udang', 'description' => 'Vaname 50 - 60 (Frozen)', 'unit' => 'Kg/Kilos', 'harga_supplier' => 10000, 'harga_jual' => 96000, 'category' => 'Ikan & Seafood', 'vendor' => 'Pasar'],

            // H - Tambahan Item
            ['kode' => 'BK180005', 'name' => 'Roti Tawar', 'description' => 'Sari Roti', 'unit' => 'Pack', 'harga_supplier' => 10000, 'harga_jual' => 20000, 'category' => 'Bahan Kering', 'vendor' => 'Sari Roti'],
            ['kode' => 'MN010003', 'name' => 'Aqua Isi ulang', 'description' => 'Aqua / Le Minerale', 'unit' => 'Galon', 'harga_supplier' => 10000, 'harga_jual' => 24000, 'category' => 'Minuman', 'vendor' => 'Toko Ronny'],
        ];

        $vendorCache = [];

        foreach ($products as $data) {
            $vendorName = $data['vendor'];

            if (!isset($vendorCache[$vendorName])) {
                $vendorCache[$vendorName] = Vendor::where('name', $vendorName)->value('id');
            }

            Product::create([
                'vendor_id'       => $vendorCache[$vendorName],
                'kode'            => $data['kode'],
                'name'            => $data['name'],
                'description'     => $data['description'],
                'unit'            => $data['unit'],
                'harga_supplier'  => $data['harga_supplier'],
                'harga_jual'      => $data['harga_jual'],
                'category'        => $data['category'],
                'is_active'       => true,
            ]);
        }
    }
}
