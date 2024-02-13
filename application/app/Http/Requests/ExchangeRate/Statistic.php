<?php
declare(strict_types=1);

namespace App\Http\Requests\ExchangeRate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $from
 * @property string|null $to
 * @property array|null $currency_id
 * @property array|null $bank_id
 */
class Statistic extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => 'nullable|date_format:d.m.Y',
            'to' => 'nullable|exists:banks,id',
            'currency_id' => 'nullable|array',
            'currency_id.*' => 'sometimes|exists:currencies,id',
            'bank_id' => 'nullable|array',
            'bank_id.*' => 'sometimes|exists:banks,id',
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation()
    {

    }
}
