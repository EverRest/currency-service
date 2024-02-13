<?php
declare(strict_types=1);

namespace App\Http\Requests\ExchangeRate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class Current extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currency_id' => 'required|array',
            'currency_id.*' => 'required|exists:currencies,id',
            'bank_id' => 'required|array',
            'bank_id.*' => 'required|exists:banks,id',
        ];
    }
}
