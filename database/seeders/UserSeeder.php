<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun demo (password diserahkan ke cast 'hashed' di model -> Bcrypt).
     * Kredensial ini juga ditulis di README.md.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'nama_lengkap' => 'Administrator',
            'password' => 'password123',
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        User::create([
            'username' => 'kasir',
            'nama_lengkap' => 'Kasir Utama',
            'password' => 'password123',
            'role' => 'kasir',
            'status' => 'aktif',
        ]);
    }
}
