<?php

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        print 'seeding groups';
        $this->call(GroupSeeder::class);
        print 'seeding admin user';
        $this->call(AdminUserSeeder::class);
        print 'seeding state data';
        $this->call(StateSeeder::class);
        print 'seeding bank data';
        $this->call(BankSeeder::class);
        print 'seeding agent types';
        $this->call(AgentTypeSeeder::class);
        print 'seeding territories';
        $this->call(TerritoriesSeeder::class);

    }
}
