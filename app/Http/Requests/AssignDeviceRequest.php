<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        $organization = $this->user()->currentOrganization();

        return [
            'device_id' => [
                'nullable', 'integer',
                Rule::exists('devices', 'id')->where('organization_id', $organization->id)->where('status', 'active'),
            ],
        ];
    }
}
