<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $coffee = Category::where('slug', 'coffee')->firstOrFail();
        $nonCoffee = Category::where('slug', 'non-coffee')->firstOrFail();
        $food = Category::where('slug', 'food')->firstOrFail();
        $pastry = Category::where('slug', 'pastry')->firstOrFail();

        $products = [
            // Coffee
            ['category_id' => $coffee->id, 'kode_produk' => 'K001', 'nama_produk' => 'Espresso', 'harga_jual' => 18000, 'harga_pokok' => 8000, 'stok' => 50, 'unit' => 'cup'],
            ['category_id' => $coffee->id, 'kode_produk' => 'K002', 'nama_produk' => 'Americano', 'harga_jual' => 20000, 'harga_pokok' => 9000, 'stok' => 50, 'unit' => 'cup'],
            ['category_id' => $coffee->id, 'kode_produk' => 'K003', 'nama_produk' => 'Cafe Latte', 'harga_jual' => 25000, 'harga_pokok' => 11000, 'stok' => 40, 'unit' => 'cup'],
            ['category_id' => $coffee->id, 'kode_produk' => 'K004', 'nama_produk' => 'Cappuccino', 'harga_jual' => 25000, 'harga_pokok' => 11000, 'stok' => 40, 'unit' => 'cup'],
            ['category_id' => $coffee->id, 'kode_produk' => 'K005', 'nama_produk' => 'Kopi Tubruk', 'harga_jual' => 15000, 'harga_pokok' => 6000, 'stok' => 60, 'unit' => 'cup'],
            ['category_id' => $coffee->id, 'kode_produk' => 'K006', 'nama_produk' => 'V60 Manual Brew', 'harga_jual' => 28000, 'harga_pokok' => 12000, 'stok' => 25, 'unit' => 'cup'],
            // Non-Coffee
            ['category_id' => $nonCoffee->id, 'kode_produk' => 'N001', 'nama_produk' => 'Matcha Latte', 'harga_jual' => 26000, 'harga_pokok' => 12000, 'stok' => 30, 'unit' => 'cup'],
            ['category_id' => $nonCoffee->id, 'kode_produk' => 'N002', 'nama_produk' => 'Chocolate', 'harga_jual' => 24000, 'harga_pokok' => 10000, 'stok' => 35, 'unit' => 'cup'],
            ['category_id' => $nonCoffee->id, 'kode_produk' => 'N003', 'nama_produk' => 'Teh Melati', 'harga_jual' => 15000, 'harga_pokok' => 5000, 'stok' => 50, 'unit' => 'cup'],
            ['category_id' => $nonCoffee->id, 'kode_produk' => 'N004', 'nama_produk' => 'Lemon Tea', 'harga_jual' => 18000, 'harga_pokok' => 6000, 'stok' => 40, 'unit' => 'cup'],
            // Food
            ['category_id' => $food->id, 'kode_produk' => 'F001', 'nama_produk' => 'Nasi Goreng Titik Temu', 'harga_jual' => 28000, 'harga_pokok' => 12000, 'stok' => 30, 'unit' => 'porsi'],
            ['category_id' => $food->id, 'kode_produk' => 'F002', 'nama_produk' => 'Mie Goreng', 'harga_jual' => 25000, 'harga_pokok' => 11000, 'stok' => 30, 'unit' => 'porsi'],
            ['category_id' => $food->id, 'kode_produk' => 'F003', 'nama_produk' => 'Kentang Goreng', 'harga_jual' => 20000, 'harga_pokok' => 8000, 'stok' => 40, 'unit' => 'porsi'],
            ['category_id' => $food->id, 'kode_produk' => 'F004', 'nama_produk' => 'Roti Bakar Coklat Keju', 'harga_jual' => 18000, 'harga_pokok' => 7000, 'stok' => 35, 'unit' => 'porsi'],
            // Pastry
            ['category_id' => $pastry->id, 'kode_produk' => 'P001', 'nama_produk' => 'Croissant', 'harga_jual' => 22000, 'harga_pokok' => 10000, 'stok' => 20, 'unit' => 'pcs'],
            ['category_id' => $pastry->id, 'kode_produk' => 'P002', 'nama_produk' => 'Donat Gula', 'harga_jual' => 12000, 'harga_pokok' => 5000, 'stok' => 30, 'unit' => 'pcs'],
            ['category_id' => $pastry->id, 'kode_produk' => 'P003', 'nama_produk' => 'Brownies', 'harga_jual' => 18000, 'harga_pokok' => 8000, 'stok' => 25, 'unit' => 'pcs'],
            ['category_id' => $pastry->id, 'kode_produk' => 'P004', 'nama_produk' => 'Cake Slice', 'harga_jual' => 25000, 'harga_pokok' => 11000, 'stok' => 15, 'unit' => 'pcs'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
