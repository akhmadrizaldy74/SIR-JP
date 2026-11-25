<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$testUsers = App\Models\User::where('email', 'like', '%@test.com')
    ->orWhere('email', 'like', '%test_%')
    ->get(['id', 'name', 'email']);

foreach ($testUsers as $u) {
    echo "Deleting test user: {$u->id} - {$u->name} ({$u->email})\n";
    $u->delete();
}

echo "Cleanup test users completed. Remaining users: " . App\Models\User::count() . "\n";
