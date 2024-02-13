<?php
declare(strict_types=1);

namespace App\Http\Requests\ExchangeRate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'currency_id' => 'sometimes|exists:currencies,id',
        ];
    }
}
