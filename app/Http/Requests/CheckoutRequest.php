<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'is_member' => ['required', 'boolean'],
        ];

        // Validation for non-member
        if ($this->input('is_member') == 0) {
            $rules['name'] = ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'];
            $rules['whatsapp'] = ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'max:15'];
            $rules['address'] = ['required', 'string', 'max:500', 'min:10'];
        } else {
            // Validation for member
            $rules['member_id'] = ['required', 'integer', 'exists:anggotas,id'];
            $rules['nik'] = ['required', 'string', 'regex:/^[0-9]{16}$/'];
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['whatsapp'] = ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'max:15'];
            $rules['address'] = ['required', 'string', 'max:500', 'min:10'];
        }

        // Payment specific validation
        if ($this->has('payment_type')) {
            $rules['payment_type'] = ['required', 'string', Rule::in(['bank_transfer', 'gopay', 'credit_card'])];
        }

        // Bank transfer validation
        if ($this->input('payment_type') === 'bank_transfer' && $this->has('bank')) {
            $rules['bank'] = ['required', 'string', Rule::in(['bca', 'bni', 'bri', 'mandiri', 'permata', 'cimb', 'other'])];
        }

        // Credit card validation
        if ($this->input('payment_type') === 'credit_card') {
            $rules['card_number'] = ['required', 'string', 'regex:/^[0-9]{13,19}$/'];
            $rules['card_exp_month'] = ['required', 'string', 'regex:/^(0[1-9]|1[0-2])$/'];
            $rules['card_exp_year'] = ['required', 'integer', 'min:' . date('Y'), 'max:' . (date('Y') + 20)];
            $rules['card_cvv'] = ['required', 'string', 'regex:/^[0-9]{3,4}$/'];
        }

        // QRIS validation
        if ($this->has('qris_dynamic_id')) {
            $rules['qris_dynamic_id'] = ['nullable', 'string', 'max:255'];
        }

        // GoPay validation
        if ($this->has('gopay_transaction_id')) {
            $rules['gopay_transaction_id'] = ['nullable', 'string', 'max:255'];
            $rules['gopay_order_id'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp harus berupa angka 10-15 digit.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.min' => 'Alamat minimal 10 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'payment_method_id.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method_id.exists' => 'Metode pembayaran tidak valid.',
            'member_id.required' => 'Data anggota tidak valid.',
            'member_id.exists' => 'Anggota tidak ditemukan.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.regex' => 'NIK harus berupa 16 digit angka.',
            'card_number.required' => 'Nomor kartu wajib diisi.',
            'card_number.regex' => 'Nomor kartu tidak valid (13-19 digit).',
            'card_exp_month.required' => 'Bulan kadaluarsa wajib diisi.',
            'card_exp_month.regex' => 'Bulan kadaluarsa tidak valid (01-12).',
            'card_exp_year.required' => 'Tahun kadaluarsa wajib diisi.',
            'card_exp_year.min' => 'Tahun kadaluarsa tidak valid.',
            'card_cvv.required' => 'CVV wajib diisi.',
            'card_cvv.regex' => 'CVV harus berupa 3-4 digit angka.',
            'bank.required' => 'Bank wajib dipilih.',
            'bank.in' => 'Bank yang dipilih tidak valid.',
            'payment_type.required' => 'Tipe pembayaran wajib dipilih.',
            'payment_type.in' => 'Tipe pembayaran tidak valid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs
        if ($this->has('name')) {
            $this->merge([
                'name' => strip_tags(trim($this->name)),
            ]);
        }

        if ($this->has('whatsapp')) {
            $this->merge([
                'whatsapp' => preg_replace('/[^0-9]/', '', $this->whatsapp),
            ]);
        }

        if ($this->has('address')) {
            $this->merge([
                'address' => strip_tags(trim($this->address)),
            ]);
        }

        if ($this->has('nik')) {
            $this->merge([
                'nik' => preg_replace('/[^0-9]/', '', $this->nik),
            ]);
        }

        if ($this->has('card_number')) {
            $this->merge([
                'card_number' => preg_replace('/[^0-9]/', '', $this->card_number),
            ]);
        }

        if ($this->has('card_cvv')) {
            $this->merge([
                'card_cvv' => preg_replace('/[^0-9]/', '', $this->card_cvv),
            ]);
        }
    }
}
