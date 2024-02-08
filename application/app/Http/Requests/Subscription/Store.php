<?php
declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'bank_id' => 'nullable|integer|exists:banks,id',
        ];
    }
}
