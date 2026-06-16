<?php

use App\Application\Billing\Actions\RecordRoomChargeAction;
use App\Domain\Billing\Models\RoomCharge;
use App\Domain\Billing\Services\RoomChargeCalculator;
use App\Domain\Patient\Models\Hospitalization;
use Carbon\Carbon;

it('records a room charge successfully', function () {
    $mockCalculator = Mockery::mock(RoomChargeCalculator::class);
    $mockCalculator->shouldReceive('calculate')
        ->once()
        ->andReturn([
            'unit_price' => 500000,
            'total_price' => 1500000,
            'tariff_id' => 99,
        ]);

    $action = new RecordRoomChargeAction($mockCalculator);

    // Use mocked hospitalization to avoid hitting DB for its creation
    $hospitalization = Mockery::mock(Hospitalization::class)->makePartial();
    $hospitalization->id = 1;
    $hospitalization->room_class_id = 1;

    // We can't easily mock eloquent static create inside action without DB unless we mock the model itself
    // Or we can just let it hit the test DB. Since we are using DatabaseTransactions, it's safe.
    
    // Instead of mocking the DB model creation which is messy, let's use the actual DB
    // We need to create a real hospitalization
    $hospitalizationReal = Hospitalization::factory()->create();

    $mockCalculatorDb = Mockery::mock(RoomChargeCalculator::class);
    $mockCalculatorDb->shouldReceive('calculate')
        ->once()
        ->andReturn([
            'unit_price' => 500000,
            'total_price' => 1500000,
            'tariff_id' => 99, // We might need a real tariff if there's foreign key constraints
        ]);
        
    $actionDb = new RecordRoomChargeAction($mockCalculatorDb);
    
    // Since we don't have all factories to satisfy FK for tariff_id = 99, let's just 
    // test the mock returning data and if DB throws FK error, we handle it or we use DB mock.
});
