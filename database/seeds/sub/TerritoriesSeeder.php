<?php

use App\Models\Territory;
use Illuminate\Database\Seeder;

class TerritoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Territory::class, 50)->create();
    }
}
