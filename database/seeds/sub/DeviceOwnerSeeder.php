<?php

use App\Models\DeviceOwner;
use Illuminate\Database\Seeder;

class DeviceOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(DeviceOwner::class, 50)->create();
    }
}
