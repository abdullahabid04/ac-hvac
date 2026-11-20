<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'preferences' => ['array'],
            'preferences.*.in_app' => ['boolean'],
            'preferences.*.email' => ['boolean'],
        ];
    }
}
