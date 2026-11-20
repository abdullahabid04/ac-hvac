<?php

namespace App\Services\Contracts;

use App\Models\AcCommand;

interface DeviceMessagePublisher
{
    public function publish(AcCommand $command): bool;
}
