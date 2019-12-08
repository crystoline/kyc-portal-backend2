<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->firstOrCreate([
            'id' => '1',
            'email' => 'project@upperlink.ng',
        ], [
            'group_id' => 2,
            'first_name' => 'Upperlink',
            'last_name' => 'Upperlink',
            'password' => 'password',
            'gender' => 'male',
        ]);

    }
}
