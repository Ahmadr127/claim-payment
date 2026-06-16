<?php

use App\Domain\Billing\Services\RoomChargeCalculator;
use App\Domain\Patient\Models\Hospitalization;
use App\Domain\Room\Models\RoomClass;
use App\Domain\Room\Models\RoomTariff;
use App\Domain\Room\Models\RoomTariffType;
use App\Domain\Room\Services\RoomTariffResolver;
use Carbon\Carbon;

it('calculates room charge correctly when tariff is found', function () {
    // Mock the resolver
    $mockResolver = Mockery::mock(RoomTariffResolver::class);
    $tariffId = 100;
    
    $mockTariff = Mockery::mock(RoomTariff::class);
    $mockTariff->shouldReceive('getAttribute')->with('id')->andReturn($tariffId);
    $mockTariff->shouldReceive('getAttribute')->with('amount')->andReturn(500000);
    
    $mockResolver->shouldReceive('resolve')
        ->once()
        ->with(1, 2, '2025-01-15')
        ->andReturn($mockTariff);

    $calculator = new RoomChargeCalculator($mockResolver);
    
    $hospitalization = new Hospitalization(['room_class_id' => 1]);
    
    $result = $calculator->calculate($hospitalization, 2, 3, '2025-01-15');
    
    expect($result)->toBeArray()
        ->and($result['unit_price'])->toBe(500000)
        ->and($result['total_price'])->toBe(1500000)
        ->and($result['tariff_id'])->toBe($tariffId);
});

it('throws exception when tariff is not found', function () {
    $mockResolver = Mockery::mock(RoomTariffResolver::class);
    $mockResolver->shouldReceive('resolve')->andReturn(null);

    $calculator = new RoomChargeCalculator($mockResolver);
    $hospitalization = new Hospitalization(['room_class_id' => 1]);
    
    expect(fn() => $calculator->calculate($hospitalization, 2, 1, '2025-01-15'))
        ->toThrow(Exception::class, 'Tarif kamar tidak ditemukan');
});
