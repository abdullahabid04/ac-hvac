<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcCommandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->currentOrganization();
    }

    public function rules(): array
    {
        return [
            'action' => ['nullable', Rule::in(['set_state'])],
            'desired_state' => ['required', 'array'],
            'desired_state.power' => ['nullable', Rule::in(['on', 'off'])],
            'desired_state.temperature_setpoint_c' => ['nullable', 'integer', 'between:-20,60'],
            'desired_state.mode' => ['nullable', 'string', 'max:30'],
            'desired_state.fan' => ['nullable', 'string', 'max:30'],
            'desired_state.swing' => ['nullable', 'string', 'max:30'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'client_request_id' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $state = $this->input('desired_state', []);

            if (! is_array($state) || $state === []) {
                $validator->errors()->add('desired_state', 'Provide at least one supported state value.');
            }
        });
    }
}
