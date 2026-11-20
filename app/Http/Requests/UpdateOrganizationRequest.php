<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'timezone' => ['required', 'timezone'],
            'contact_email' => ['nullable', 'email', 'max:160'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
