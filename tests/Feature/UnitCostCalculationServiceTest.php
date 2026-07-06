<?php

use App\Models\ClinicalPathway\Diagnosis;
use App\Models\ClinicalPathway\DiagnosisPathway;
use App\Models\Room\RoomClass;
use App\Models\Room\RoomTariffType;
use App\Services\UnitCostCalculationService;
use App\Models\OrganizationUnit;
use App\Models\OrganizationType;
use Illuminate\Support\Facades\DB;

it('calculates room tariffs using percentage/svc', function () {
    // 1. Create or get Organization Type and Unit
    $type = OrganizationType::firstOrCreate(
        ['name' => 'holding'],
        [
            'display_name' => 'Holding',
            'level' => 1,
            'description' => 'Holding Unit',
        ]
    );

    $orgUnit = OrganizationUnit::firstOrCreate(
        ['code' => 'TEST_UNIT'],
        [
            'name' => 'Test Unit',
            'type_id' => $type->id,
            'is_active' => true,
        ]
    );

    // 2. Create Room Class
    $roomClass = RoomClass::factory()->create([
        'code' => 'TEST_CLASS',
        'name' => 'Test Class',
        'is_active' => true,
    ]);

    // 3. Create Room Tariff Type (Kamar)
    $tariffType = RoomTariffType::factory()->create([
        'code' => 'kamar_test',
        'name' => 'Kamar Test',
        'is_active' => true,
    ]);

    // 4. Create active Room Tariff
    DB::table('room_tariffs')->insert([
        'room_class_id' => $roomClass->id,
        'room_tariff_type_id' => $tariffType->id,
        'amount' => 1000000,
        'effective_date' => '2025-01-01',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 5. Create Diagnosis & Pathway
    $diagnosis = Diagnosis::create([
        'name' => 'Test Diagnosis',
        'icd_code' => 'T.123',
        'is_active' => true,
    ]);

    $pathway = DiagnosisPathway::create([
        'diagnosis_id' => $diagnosis->id,
        'length_of_stay' => 3,
        'is_active' => true,
    ]);

    // Attach Room Tariff to Pathway
    $pathway->items()->create([
        'item_type' => RoomTariffType::class,
        'item_id' => $tariffType->id,
        'quantity' => 3,
    ]);

    // 6. Calculate Matrix
    $service = new UnitCostCalculationService();
    $matrix = $service->calculateMatrix($diagnosis, $orgUnit);

    // Verify matrix structure and percentage default
    expect($matrix)->toHaveCount(1);
    
    $row = $matrix[0];
    expect($row['type'])->toBe('RoomTariffType');
    expect($row['qty'])->toBe(3);
    
    $tariffData = $row['tariffs'][$roomClass->id];
    expect($tariffData['percentage'])->toBe(100);
    expect($tariffData['base_amount'])->toBe(1000000);
    expect($tariffData['amount'])->toBe(1000000); // 1,000,000 * 100% = 1,000,000
    expect($tariffData['total'])->toBe(3000000); // 1,000,000 * 3 = 3,000,000
});
