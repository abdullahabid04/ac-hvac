<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRemotePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        return [
            'policy_value' => ['required', Rule::in(['inherit', 'enabled', 'disabled'])],
            'scope_type' => ['required', Rule::in(['organization', 'site', 'building', 'floor', 'space', 'ac'])],
            'scope_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
