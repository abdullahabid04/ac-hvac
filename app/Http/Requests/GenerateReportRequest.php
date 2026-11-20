<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        return [
            'report_type' => ['required', Rule::in(array_keys(config('hvac.report_types')))],
            'format' => ['required', Rule::in(['pdf', 'csv'])],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'site_id' => ['nullable', 'integer'],
            'building_id' => ['nullable', 'integer'],
            'floor_id' => ['nullable', 'integer'],
            'space_id' => ['nullable', 'integer'],
            'ac_id' => ['nullable', 'integer'],
        ];
    }
}
