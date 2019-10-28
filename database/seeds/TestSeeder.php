<?php

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        print 'Seeding Users';
        $this->call(UserSeeder::class);

        print 'Seeding Agents';
        $this->call(AgentSeeder::class);
    }
}
