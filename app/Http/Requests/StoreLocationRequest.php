<?php

namespace App\Http\Requests;

use App\Models\Space;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        $organization = $this->user()->currentOrganization();
        $type = $this->input('location_type');

        $rules = [
            'location_type' => ['required', Rule::in(['site', 'building', 'floor', 'space'])],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'timezone'],
            'floor_number' => ['nullable', 'integer', 'between:-10,200'],
            'space_type' => ['nullable', Rule::in(array_keys(Space::TYPES))],
            'area_sqm' => ['nullable', 'numeric', 'between:0,1000000'],
        ];

        if ($type === 'building') {
            $rules['site_id'] = ['required', 'integer', Rule::exists('sites', 'id')->where('organization_id', $organization->id)];
        }

        if ($type === 'floor') {
            $rules['building_id'] = ['required', 'integer', Rule::exists('buildings', 'id')->where('organization_id', $organization->id)];
        }

        if ($type === 'space') {
            $rules['floor_id'] = ['required', 'integer', Rule::exists('floors', 'id')->where('organization_id', $organization->id)];
            $rules['parent_space_id'] = ['nullable', 'integer', Rule::exists('spaces', 'id')->where('organization_id', $organization->id)];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('location_type') !== 'space' || ! $this->filled('parent_space_id')) {
                return;
            }

            $parent = Space::where('organization_id', $this->user()->currentOrganization()->id)
                ->find($this->input('parent_space_id'));

            if ($parent && (int) $parent->floor_id !== (int) $this->input('floor_id')) {
                $validator->errors()->add('parent_space_id', 'The parent space must be on the selected floor.');
            }
        });
    }
}
