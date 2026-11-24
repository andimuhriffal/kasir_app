<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_pesanan' => ['required', 'in:dine_in,takeaway'],
            'nomor_meja' => ['nullable', 'string', 'max:20', 'required_if:tipe_pesanan,dine_in'],
            'nama_pemesan' => ['nullable', 'string', 'max:100'],
            'diskon_nominal' => ['nullable', 'numeric', 'min:0'],
            'metode_bayar' => ['required', 'in:tunai,qris,transfer,debit'],
            'uang_dibayar' => ['nullable', 'numeric', 'min:0', 'required_if:metode_bayar,tunai'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.kuantiti' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.catatan_khusus' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipe_pesanan.required' => 'Tipe pesanan wajib dipilih.',
            'nomor_meja.required_if' => 'Nomor meja wajib diisi untuk Dine-In.',
            'metode_bayar.required' => 'Metode pembayaran wajib dipilih.',
            'uang_dibayar.required_if' => 'Uang dibayar wajib diisi untuk pembayaran tunai.',
            'items.required' => 'Minimal 1 item pesanan.',
            'items.*.product_id.exists' => 'Menu yang dipilih tidak ditemukan.',
            'items.*.kuantiti.min' => 'Kuantiti minimal 1.',
        ];
    }
}
