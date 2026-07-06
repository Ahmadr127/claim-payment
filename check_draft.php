<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$result = DB::table('unit_cost_assignments')->where('id', 1)->first();
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);




