<?php

use App\Domain\Room\Models\RoomClass;
use App\Domain\Room\Models\RoomTariff;
use App\Domain\Room\Models\RoomTariffType;
use App\Domain\Room\Services\RoomTariffResolver;
use Carbon\Carbon;

it('resolves active room tariff for a specific date', function () {
    $roomClass = RoomClass::factory()->create(['code' => uniqid('C_')]);
    $tariffType = RoomTariffType::factory()->create(['code' => uniqid('T_')]);

    // Create an old tariff
    RoomTariff::factory()->create([
        'room_class_id' => $roomClass->id,
        'room_tariff_type_id' => $tariffType->id,
        'amount' => 500000,
        'effective_date' => '2024-01-01',
        'expired_date' => '2024-12-31',
        'is_active' => true,
    ]);

    // Create current active tariff
    $activeTariff = RoomTariff::factory()->create([
        'room_class_id' => $roomClass->id,
        'room_tariff_type_id' => $tariffType->id,
        'amount' => 700000,
        'effective_date' => '2025-01-01',
        'expired_date' => null,
        'is_active' => true,
    ]);

    $resolver = new RoomTariffResolver();
    
    // Test resolving on a date within active period
    $resolved = $resolver->resolve($roomClass->id, $tariffType->id, '2025-01-15');
    
    expect($resolved)->not->toBeNull()
        ->and($resolved->id)->toBe($activeTariff->id)
        ->and($resolved->amount)->toBe(700000);
});

it('returns null if no active tariff is found', function () {
    $resolver = new RoomTariffResolver();
    $resolved = $resolver->resolve(999, 999, '2025-01-01');
    expect($resolved)->toBeNull();
});
