<?php

use App\Domain\Claim\Services\ClaimAggregator;
use App\Domain\Patient\Models\Hospitalization;

it('aggregates empty charges gracefully', function () {
    $aggregator = new ClaimAggregator();
    $hospitalization = new Hospitalization();
    $hospitalization->id = 9999; // Mock ID that won't have any charges
    
    $result = $aggregator->aggregate($hospitalization);
    
    expect($result)->toBeArray()
        ->and($result['total_room_charge'])->toBe(0)
        ->and($result['total_service_charge'])->toBe(0)
        ->and($result['total_medication_charge'])->toBe(0)
        ->and($result['grand_total'])->toBe(0)
        ->and($result['items']->count())->toBe(0);
});
