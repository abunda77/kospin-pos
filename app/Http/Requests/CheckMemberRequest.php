<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckMemberRequest extends FormRequest
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
        return [
            'nik' => ['required', 'string', 'regex:/^[0-9]{10,16}$/', 'max:16'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nik.required' => 'NIK/No.WA wajib diisi.',
            'nik.regex' => 'NIK/No.WA harus berupa angka 10-16 digit.',
            'nik.max' => 'NIK/No.WA maksimal 16 digit.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize NIK input - remove all non-numeric characters
        if ($this->route('nik')) {
            $this->merge([
                'nik' => preg_replace('/[^0-9]/', '', $this->route('nik')),
            ]);
        }
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Additional sanitization after validation
        if (isset($validated['nik'])) {
            $validated['nik'] = htmlspecialchars($validated['nik'], ENT_QUOTES, 'UTF-8');
        }
        
        return $validated;
    }
}
