<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'timezone'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'floor_number' => ['nullable', 'integer', 'between:-10,200'],
            'space_type' => ['nullable', Rule::in(['room', 'corridor', 'hall', 'gallery', 'section', 'other'])],
            'area_sqm' => ['nullable', 'numeric', 'between:0,1000000'],
        ];
    }
}
