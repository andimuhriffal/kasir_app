<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'nama_kategori' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('nama_kategori') && ! $this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->input('nama_kategori'))]);
        }
    }
}
