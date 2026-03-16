<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01|max:999999999',
            'category_id' => [
                'required',
                'integer',
                function ($attr, $value, $fail) {
                    $exists = auth()->user()->categories()->where('id', $value)->exists();
                    if (!$exists) $fail('The selected category is invalid.');
                },
            ],
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ];
    }
}
