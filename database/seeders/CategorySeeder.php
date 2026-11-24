<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Coffee', 'Non-Coffee', 'Food', 'Pastry'];

        foreach ($categories as $name) {
            Category::create([
                'nama_kategori' => $name,
                'slug' => Str::slug($name),
                'status' => 'aktif',
            ]);
        }
    }
}
