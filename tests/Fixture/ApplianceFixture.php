<?php
declare(strict_types=1);

namespace Tests\Fixture;

use App\Enums\ActiveStatus;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Appliance\Services\ApplianceService;
use Illuminate\Foundation\Testing\WithFaker;

class ApplianceFixture
{
    use WithFaker;

    private ApplianceService $applianceService;

    public function __construct(ApplianceService $applianceService)
    {
        $this->applianceService = $applianceService;
    }

    public function create(string $name): Appliance
    {
        return $this->applianceService->create($name, 'Fixture', ActiveStatus::create(ActiveStatus::ACTIVE));
    }
}
