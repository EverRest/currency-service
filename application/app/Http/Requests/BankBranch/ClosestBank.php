<?php
declare(strict_types=1);

namespace App\Http\Requests\BankBranch;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property numeric $lat
 * @property numeric $lng
 * @property numeric $request
 */
class ClosestBank extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'radius' => ['required', 'numeric', 'min:1', 'max:100000']
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'radius' => (float) $this->radius
        ]);
    }
}
