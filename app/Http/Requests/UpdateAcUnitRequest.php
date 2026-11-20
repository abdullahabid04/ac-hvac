<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization()
            && $this->route('ac')->organization_id === $this->user()->currentOrganization()->id;
    }

    public function rules(): array
    {
        $organization = $this->user()->currentOrganization();
        $ac = $this->route('ac');

        return [
            'name' => ['required', 'string', 'max:120'],
            'asset_code' => [
                'required', 'string', 'max:60',
                Rule::unique('ac_units', 'asset_code')->where('organization_id', $organization->id)->ignore($ac->id),
            ],
            'space_id' => [
                'required', 'integer',
                Rule::exists('spaces', 'id')->where('organization_id', $organization->id),
            ],
            'ac_model_id' => ['required', 'integer', 'exists:ac_models,id'],
            'ir_profile_version_id' => ['nullable', 'integer', 'exists:ir_profile_versions,id'],
            'serial_number' => ['nullable', 'string', 'max:80'],
            'installation_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'maintenance', 'retired'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
