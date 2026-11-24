# Titik Temu Kopi — Sistem Kasir & Pemesanan (POS)

Aplikasi kasir (Point of Sales) Dine-In & Takeaway untuk kafe **Titik Temu Kopi**.
Dibangun mengikuti panduan praktik **MED-WEB-01** (Sistem Kasir & Pemesanan Kafe Modern).

## Tech Stack

| Komponen | Teknologi |
| --- | --- |
| Framework | Laravel 13 (PHP 8.3+) |
| Database | MySQL 8.0 |
| Frontend | Blade + TailwindCSS v4 (Vite) |
| Autentikasi | Session + Bcrypt (BCRYPT_ROUNDS=12) |
| Ekspor PDF | barryvdh/laravel-dompdf |
| Ekspor Spreadsheet | CSV (kompatibel Excel) |

## Fitur (4 Modul Wajib)

### 1. Autentikasi & Hak Akses
- Login username + password (password di-hash Bcrypt, tidak pernah disimpan plaintext).
- Dua role: **Administrator/Manager** (akses penuh) dan **Kasir/Waiter** (operasional).
- Proteksi route (middleware `auth` + `role`), logout menghancurkan sesi.
- Hanya akun berstatus `aktif` yang bisa login.

### 2. Dashboard Eksekutif
- 4 KPI: Total Omset Hari Ini, Jumlah Pesanan Hari Ini, Menu Terlaris, Peringatan Stok Rendah (stok ≤ 10).
- Grafik tren omset 7 hari terakhir (bar chart, tanpa dependensi eksternal).
- Tabel 8 transaksi terbaru + tombol aksi cepat (Buat Pesanan, Riwayat, Laporan).

### 3. Transaksi Inti (Master-Detail)
- Nomor faktur otomatis `INV-POS-YYYYMMDD-XXXX` (urutan per hari, unik).
- Tipe pesanan: **Dine-In** (wajib nomor meja) atau **Takeaway**.
- Baris item dinamis: pilih menu, kuantiti, catatan khusus (mis. Less Sugar, Extra Ice).
- Kalkulasi otomatis: Subtotal → Diskon → Pajak PB1 10% dari (Subtotal − Diskon) → Grand Total.
- Pembayaran: Tunai (hitung kembalian otomatis, ditolak jika kurang dari total), QRIS, Transfer Bank, Kartu Debit.
- Status pesanan: Diproses → Selesai / Dibatalkan.
- **Status realtime:** halaman admin/manajer memperbarui badge status pesanan
  secara otomatis setiap 5 detik (polling `GET /orders/statuses?ids=...`).
  Jadi kalau kasir membatalkan pesanan, status di layar admin/manajer berubah
  menjadi "Dibatalkan" tanpa refresh manual; di halaman detail, tombol aksi
  ikut disembunyikan dan muncul pemberitahuan. Polling dijeda saat tab
  tidak aktif. Kasir hanya menerima status pesanannya sendiri.
- **KPI realtime:** 4 kartu KPI di dashboard (Omset Hari Ini, Jumlah Pesanan,
  Menu Terlaris, Stok Rendah) diperbarui otomatis setiap 5 detik lewat
  polling `GET /dashboard/kpis`, jadi angka ikut berubah saat kasir membuat
  atau membatalkan pesanan. Grafik tren 7 hari dan daftar stok menipis
  diperbarui saat halaman dimuat ulang.
- Validasi stok sebelum simpan; harga selalu diambil dari database (bukan dari form).
- Transaksi database ACID: header + detail tersimpan bersamaan atau rollback; stok berkurang atomik.
- Pembatalan mengembalikan stok.
- Struk siap cetak (format thermal 80mm, `window.print()`).

### 4. Laporan & Ekspor Data
- Filter: rentang tanggal, kategori menu (Coffee, Non-Coffee, Food, Pastry), metode bayar.
- Ringkasan: Omset Bruto, Total Diskon, Total Pajak PB1, Omset Bersih, Rata-rata / Transaksi, Jumlah Transaksi.
- Tabel: No. Faktur, Tanggal & Waktu, Kasir, Tipe, Item Terjual, Subtotal, Pajak, Grand Total, Status.
- Ekspor PDF (tabel rapi, A4 landscape) dan Excel/CSV.

### Laporan Laba Rugi (`/reports/laba-rugi`, admin)
- Hanya pesanan berstatus **Selesai** yang dihitung (pesanan Diproses belum diakui sebagai pendapatan). Filter: rentang tanggal.
- Ringkasan: Penjualan Kotor (Σ subtotal), Total Diskon, Pendapatan Bersih (= Kotor − Diskon), Total HPP (Σ kuantiti × harga_pokok), Laba Kotor (= Pendapatan Bersih − HPP), Margin Laba Kotor (%), Pajak PB1 terkumpul (info saja, bukan bagian laba), Jumlah Transaksi.
- Rincian per produk (diurutkan dari laba terbesar): Qty, Penjualan, HPP, Laba, Margin. Laba per produk dihitung sebelum alokasi diskon (diskon berlaku per pesanan), jadi total laba di tabel = Laba Kotor + Total Diskon.
- HPP memakai **harga_pokok produk saat ini** (order_details tidak menyimpan harga pokok saat transaksi) — perlakukan sebagai estimasi bila harga pokok pernah berubah.
- Ekspor PDF (A4 landscape) dan Excel/CSV.

## Struktur Project (modular)

```
app/
├── Http/
│   ├── Controllers/        # Auth, Dashboard, Order, Product, Category, User, Report
│   ├── Middleware/         # EnsureRole (role:admin / role:admin,kasir)
│   └── Requests/           # Form Request validation (Order, Product, Category, User)
├── Models/                 # User, Category, Product, Order, OrderDetail
└── Services/               # OrderService (nomor faktur, kalkulasi, ACID, stok)
database/
├── migrations/             # users, categories, products, orders, order_details
└── seeders/                # User, Category, Product, Order (data demo 7 hari)
resources/css/
├── app.css                 # Entry utama: Tailwind + tema + import partial di bawah
├── base/
│   └── print.css           # Aturan @media print layout app (.no-print)
├── components/
│   └── chart.css           # Grafik tren omset (.trend-chart, .trend-chart-bar)
└── documents/
    └── receipt.css         # Struk thermal 80mm (orders/receipt.blade.php)
resources/js/
├── app.js                  # Entry JS bawaan Laravel (kosong)
└── realtime.js             # sidebar mobile + polling status pesanan (5 dtk)
                            # + polling dashboard/KPI/grafik/stok/transaksi (5 dtk)
                            # (URL endpoint dari data-status-url & data-kpi-url di <body>)
resources/views/
├── layouts/                # app (sidebar + topbar), guest (login)
├── auth/ dashboard/ orders/ products/ categories/ users/ reports/
└── partials/               # alert (notifikasi sukses/gagal)
routes/web.php              # route + middleware auth/role
```

> **Catatan:** style laporan PDF (`reports/pdf.blade.php`) sengaja tetap
> sebagai blok `<style>` di template, bukan file CSS terpisah — dompdf
> (library pembuat PDF) tidak bisa memuat CSS eksternal secara andal.
> Style struk (`receipt.css`) dan grafik (`chart.css`) sudah dipisah ke
> file CSS karena keduanya dirender browser, bukan dompdf.

## Instalasi

```bash
# 1. Clone / salin project, lalu instal dependensi
composer install
npm install

# 2. Salin .env dan isi konfigurasi database
cp .env.example .env
php artisan key:generate
```

Isi `.env` (MySQL):

```env
APP_NAME="Titik Temu Kopi"
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kasir_app
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 3. Buat database (MySQL)
mysql -u root -e "CREATE DATABASE db_kasir_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Migrasi + seeder (data demo: akun, kategori, menu, pesanan 7 hari)
php artisan migrate --seed

# 5. Build aset frontend
npm run build

# 6. Jalankan server
php artisan serve
# Buka http://localhost:8000
```

Untuk development (hot reload Tailwind/Vite), jalankan `npm run dev` di terminal terpisah.

## Akun Uji

| Role | Username | Password |
| --- | --- | --- |
| Administrator | `admin` | `password123` |
| Kasir | `kasir` | `password123` |

> Ganti password akun ini sebelum dipakai produksi (`php artisan tinker` atau lewat menu Pengguna).

## Skema Database

- **users** — id, username (unik), nama_lengkap, password (hash), role (admin/kasir), status (aktif/nonaktif), created_at
- **categories** — id, nama_kategori, slug (unik), status, created_at
- **products** — id, category_id, kode_produk (unik), nama_produk, harga_jual, harga_pokok, stok, unit, created_at
- **orders** — id, invoice_number (unik), user_id, tipe_pesanan (dine_in/takeaway), nomor_meja, nama_pemesan, total_subtotal, pajak_nominal, diskon_nominal, grand_total, metode_bayar (tunai/qris/transfer/debit), uang_dibayar, uang_kembali, status (diproses/selesai/dibatalkan), created_at
- **order_details** — id, order_id, product_id, harga_satuan, kuantiti, subtotal_baris, catatan_khusus, created_at

## Aturan Bisnis Penting

- Pajak restoran **PB1 = 10%** dari (Subtotal − Diskon).
- KPI Dashboard (Omset & Jumlah Pesanan Hari Ini) hanya menghitung pesanan berstatus **Selesai** (sesuai panduan: transaksi lunas/selesai).
- Laporan: Omset Bersih = Omset Bruto − Total Pajak (diskon sudah dikurangkan di grand total, jadi tidak dikurangkan lagi).
- Pembayaran tunai wajib ≥ Grand Total; kembalian = Uang Dibayar − Grand Total.
- Non-tunai (QRIS/Transfer/Debit): Uang Dibayar = Grand Total, Kembalian = 0.
- Stok dicek dan dikunci (`lockForUpdate`) saat pesanan dibuat; gagal → seluruh transaksi rollback.
- Pesanan yang sudah **Selesai** tidak bisa dibatalkan; pembatalan mengembalikan stok.
- Laporan hanya menghitung pesanan yang **tidak dibatalkan**.
- Kasir hanya melihat pesanan yang dibuatnya sendiri; Administrator melihat semua dan mengelola master data + laporan.

## Pengujian Cepat

1. Login sebagai `kasir` → buat pesanan Dine-In (Meja 3) 2 item → bayar tunai → cek kembalian & struk.
2. Login sebagai `admin` → Dashboard (KPI + grafik) → Menu/Kategori/Pengguna → Laporan → Ekspor PDF & CSV.
3. Coba bayar tunai kurang dari total → harus ditolak dengan pesan error.
4. Coba pesan melebihi stok → harus ditolak dan stok tidak berubah.
5. Batalkan pesanan Diproses → stok kembali seperti semula.
