<?php

use App\Models\AgentType;
use Illuminate\Database\Seeder;

class AgentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AgentType::query()->create([
            'name' => 'Principal Agent',
            'slug' =>'principal-agent',
        ]);
        AgentType::query()->create([
            'name' => 'Sole Agent',
            'slug' =>'sole-agent',
        ]);
    }
}
