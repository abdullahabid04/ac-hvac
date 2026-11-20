<?php

namespace App\Http\Requests;

use App\Models\Space;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        $organization = $this->user()->currentOrganization();

        return [
            'name' => ['required', 'string', 'max:120'],
            'asset_code' => [
                'required', 'string', 'max:60',
                Rule::unique('ac_units', 'asset_code')->where('organization_id', $organization->id),
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $organization = $this->user()->currentOrganization();
            $space = Space::where('organization_id', $organization->id)->find($this->input('space_id'));

            if ($space && $space->status !== 'active') {
                $validator->errors()->add('space_id', 'The selected location is not active.');
            }

            if ($this->filled('ir_profile_version_id') && $this->filled('ac_model_id')) {
                $belongs = \App\Models\IrProfileVersion::whereKey($this->input('ir_profile_version_id'))
                    ->whereHas('profile', fn ($query) => $query->where('ac_model_id', $this->input('ac_model_id')))
                    ->exists();

                if (! $belongs) {
                    $validator->errors()->add('ir_profile_version_id', 'The selected profile version does not belong to the chosen AC model.');
                }
            }
        });
    }
}
