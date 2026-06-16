<?php

use App\Domain\Billing\Services\ServiceChargeCalculator;
use App\Domain\Patient\Models\Hospitalization;
use App\Domain\Service\Models\ServiceTariff;
use App\Domain\Service\Services\ServiceTariffResolver;

it('calculates service charge correctly when tariff is found', function () {
    $mockResolver = Mockery::mock(ServiceTariffResolver::class);
    $tariffId = 200;
    
    $mockTariff = Mockery::mock(ServiceTariff::class);
    $mockTariff->shouldReceive('getAttribute')->with('id')->andReturn($tariffId);
    $mockTariff->shouldReceive('getAttribute')->with('amount')->andReturn(150000);
    
    $mockResolver->shouldReceive('resolve')
        ->once()
        ->with(5, 1, '2025-01-15')
        ->andReturn($mockTariff);

    $calculator = new ServiceChargeCalculator($mockResolver);
    $hospitalization = new Hospitalization(['room_class_id' => 1]);
    
    $result = $calculator->calculate($hospitalization, 5, 2, '2025-01-15');
    
    expect($result)->toBeArray()
        ->and($result['unit_price'])->toBe(150000)
        ->and($result['total_price'])->toBe(300000)
        ->and($result['tariff_id'])->toBe($tariffId);
});

it('throws exception when service tariff is not found', function () {
    $mockResolver = Mockery::mock(ServiceTariffResolver::class);
    $mockResolver->shouldReceive('resolve')->andReturn(null);

    $calculator = new ServiceChargeCalculator($mockResolver);
    $hospitalization = new Hospitalization(['room_class_id' => 1]);
    
    expect(fn() => $calculator->calculate($hospitalization, 5, 1, '2025-01-15'))
        ->toThrow(Exception::class, 'Tarif jasa medis tidak ditemukan');
});
