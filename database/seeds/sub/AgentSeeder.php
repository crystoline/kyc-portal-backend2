<?php

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       factory(Agent::class, 200)->create();
    }
}
