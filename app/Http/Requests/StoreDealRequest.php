<?php

namespace App\Http\Requests;

use App\Enums\DealStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id'          => ['required', 'exists:companies,id'],
            'contact_id'          => ['nullable', 'exists:contacts,id'],
            'title'               => ['required', 'string', 'max:255'],
            'value'               => ['required', 'numeric', 'min:0'],
            'stage'               => ['required', Rule::enum(DealStage::class)],
            'expected_close_date' => ['nullable', 'date'],
            'notes'               => ['nullable', 'string'],
        ];
    }
}