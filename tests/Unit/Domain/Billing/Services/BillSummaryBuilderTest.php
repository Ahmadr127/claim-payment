<?php

use App\Domain\Billing\Services\BillSummaryBuilder;
use App\Domain\Claim\Models\Claim;
use App\Domain\Claim\Models\ClaimItem;
use App\Domain\Patient\Models\Hospitalization;

it('builds summary with 6 percent admin fee correctly', function () {
    $hospitalization = Mockery::mock(Hospitalization::class)->makePartial();
    $hospitalization->shouldReceive('getAttribute')->with('admitted_at')->andReturn(now()->subDays(2));
    $hospitalization->shouldReceive('getAttribute')->with('discharged_at')->andReturn(now());
    
    $claim = Mockery::mock(Claim::class)->makePartial();
    $claim->shouldReceive('getAttribute')->with('hospitalization')->andReturn($hospitalization);
    $claim->shouldReceive('loadMissing')->andReturnSelf();
    
    $items = collect([
        (new ClaimItem)->forceFill(['category' => 'room', 'item_code' => 'R1', 'item_name' => 'Room', 'qty' => 1, 'unit_price' => 1000000, 'total_price' => 1000000]),
        (new ClaimItem)->forceFill(['category' => 'service', 'item_code' => 'S1', 'item_name' => 'Service', 'qty' => 1, 'unit_price' => 500000, 'total_price' => 500000]),
        (new ClaimItem)->forceFill(['category' => 'medication', 'item_code' => 'M1', 'item_name' => 'Med', 'qty' => 1, 'unit_price' => 500000, 'total_price' => 500000]),
    ]);
    
    $claim->shouldReceive('getAttribute')->with('items')->andReturn($items);
    $claim->shouldReceive('getAttribute')->with('patient')->andReturn(null);
    $claim->shouldReceive('getAttribute')->with('roomClass')->andReturn(null);

    $builder = new BillSummaryBuilder();
    $summary = $builder->build($claim);
    
    // Total before admin = 2,000,000
    // Admin 6% of 2,000,000 = 120,000
    // Grand Total = 2,120,000
    
    expect($summary['summary']['total_before_admin'])->toBe(2000000)
        ->and($summary['summary']['admin_percentage'])->toBe(6)
        ->and($summary['summary']['admin_fee'])->toBe(120000)
        ->and($summary['summary']['grand_total'])->toBe(2120000);
});
