<?php

use App\Domain\Billing\Services\MedicationChargeCalculator;
use App\Domain\Patient\Models\Hospitalization;
use App\Domain\Medication\Models\MedicationTariff;
use App\Domain\Medication\Services\MedicationTariffResolver;

it('calculates medication charge correctly when tariff is found', function () {
    $mockResolver = Mockery::mock(MedicationTariffResolver::class);
    $tariffId = 300;
    
    $mockTariff = Mockery::mock(MedicationTariff::class);
    $mockTariff->shouldReceive('getAttribute')->with('id')->andReturn($tariffId);
    $mockTariff->shouldReceive('getAttribute')->with('amount')->andReturn(20000);
    
    $mockResolver->shouldReceive('resolve')
        ->once()
        ->with(10, 1, '2025-01-15')
        ->andReturn($mockTariff);

    $calculator = new MedicationChargeCalculator($mockResolver);
    $hospitalization = new Hospitalization(['room_class_id' => 1]);
    
    $result = $calculator->calculate($hospitalization, 10, 5, '2025-01-15');
    
    expect($result)->toBeArray()
        ->and($result['unit_price'])->toBe(20000)
        ->and($result['total_price'])->toBe(100000)
        ->and($result['tariff_id'])->toBe($tariffId);
});

it('throws exception when medication tariff is not found', function () {
    $mockResolver = Mockery::mock(MedicationTariffResolver::class);
    $mockResolver->shouldReceive('resolve')->andReturn(null);

    $calculator = new MedicationChargeCalculator($mockResolver);
    $hospitalization = new Hospitalization(['room_class_id' => 1]);
    
    expect(fn() => $calculator->calculate($hospitalization, 10, 1, '2025-01-15'))
        ->toThrow(Exception::class, 'Tarif obat/alkes tidak ditemukan');
});
