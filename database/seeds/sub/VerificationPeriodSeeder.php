<?php

use App\Models\VerificationPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VerificationPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       VerificationPeriod::query()->create([
           'title' => 'Initial Verification',
           'date_start' => Carbon::today()
       ]);
    }
}
