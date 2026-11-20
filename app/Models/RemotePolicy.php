<?php

namespace App\Models;

use App\Enums\PolicyScope;
use App\Enums\PolicyValue;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemotePolicy extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $fillable = [
        'organization_id',
        'scope_type',
        'scope_id',
        'policy_value',
        'version',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'scope_type' => PolicyScope::class,
            'policy_value' => PolicyValue::class,
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
