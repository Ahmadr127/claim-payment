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

it('calculates medical service unit cost using service tariff and percentage', function () {
    // Setup org and room class
    $type = OrganizationType::firstOrCreate(['name' => 'holding'], ['display_name' => 'Holding', 'level' => 1]);
    $orgUnit = OrganizationUnit::firstOrCreate(['code' => 'TEST_UNIT'], ['name' => 'Test Unit', 'type_id' => $type->id, 'is_active' => true]);
    $roomClass = RoomClass::factory()->create(['code' => 'CLASS_TEST_MS', 'is_active' => true]);

    // Create MedicalService
    $serviceGroup = \App\Models\Service\ServiceGroup::create(['code' => 'SG_TEST', 'name' => 'Test Group', 'is_active' => true]);
    $medService = \App\Models\Service\MedicalService::create([
        'service_group_id' => $serviceGroup->id,
        'code' => 'MS_TEST_1',
        'name' => 'Test Service',
        'unit' => 'kali',
        'percentage' => 80.00, // 80% SVC
        'is_active' => true,
    ]);

    // Insert general tariff
    DB::table('service_tariffs')->insert([
        'medical_service_id' => $medService->id,
        'room_class_id' => $roomClass->id,
        'amount' => 500000, // Tariff = 500,000
        'effective_date' => '2025-01-01',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Setup Diagnosis and Pathway
    $diagnosis = Diagnosis::create(['name' => 'Test MS Diagnosis', 'icd_code' => 'T.MS', 'is_active' => true]);
    $pathway = DiagnosisPathway::create(['diagnosis_id' => $diagnosis->id, 'length_of_stay' => 1, 'is_active' => true]);
    $pathway->items()->create([
        'item_type' => \App\Models\Service\MedicalService::class,
        'item_id' => $medService->id,
        'quantity' => 2,
    ]);

    $calcService = new UnitCostCalculationService();

    // Verify matrix calculation: 500,000 * 80% = 400,000 unit cost
    $matrix = $calcService->calculateMatrix($diagnosis, $orgUnit);
    expect($matrix)->toHaveCount(1);
    $tariffData = $matrix[0]['tariffs'][$roomClass->id];
    expect($tariffData['base_amount'])->toBe(500000);
    expect((float) $tariffData['percentage'])->toBe(80.0);
    expect($tariffData['amount'])->toBe(400000); // 500,000 * 80% = 400,000
    expect($tariffData['total'])->toBe(800000); // 400,000 * 2 = 800,000
});

it('calculates medication unit cost using medication HNA and PPN percentage', function () {
    // Setup org and room class
    $type = OrganizationType::firstOrCreate(['name' => 'holding'], ['display_name' => 'Holding', 'level' => 1]);
    $orgUnit = OrganizationUnit::firstOrCreate(['code' => 'TEST_UNIT'], ['name' => 'Test Unit', 'type_id' => $type->id, 'is_active' => true]);
    $roomClass = RoomClass::factory()->create(['code' => 'CLASS_TEST_MED', 'is_active' => true]);

    // Create Medication with global HNA and PPN
    $medCategory = \App\Models\Medication\MedicationCategory::create(['code' => 'CAT_TEST', 'name' => 'Test Category', 'is_active' => true]);
    $medication = \App\Models\Medication\Medication::create([
        'medication_category_id' => $medCategory->id,
        'item_code' => 'MED_TEST_1',
        'name' => 'Test Medicine',
        'unit' => 'pcs',
        'hna' => 100000, // Global HNA = 100,000
        'ppn_percentage' => 11.00, // Global PPN = 11%
        'is_active' => true,
    ]);

    // Setup Diagnosis and Pathway
    $diagnosis = Diagnosis::create(['name' => 'Test Med Diagnosis', 'icd_code' => 'T.MED', 'is_active' => true]);
    $pathway = DiagnosisPathway::create(['diagnosis_id' => $diagnosis->id, 'length_of_stay' => 1, 'is_active' => true]);
    $pathway->items()->create([
        'item_type' => \App\Models\Medication\Medication::class,
        'item_id' => $medication->id,
        'quantity' => 5,
    ]);

    $calcService = new UnitCostCalculationService();

    // Verify matrix calculation: 100,000 * 1.11 = 111,000 unit cost
    $matrix = $calcService->calculateMatrix($diagnosis, $orgUnit);
    expect($matrix)->toHaveCount(1);
    $tariffData = $matrix[0]['tariffs'][$roomClass->id];
    expect((float) $tariffData['hna'])->toBe(100000.0);
    expect((float) $tariffData['ppn'])->toBe(11.0);
    expect($tariffData['amount'])->toBe(111000); // 100,000 + 11% = 111,000
    expect($tariffData['total'])->toBe(555000); // 111,000 * 5 = 555,000
});


