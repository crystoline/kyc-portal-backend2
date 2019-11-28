<?php

use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $group_list  = new Collection([
            'field_officer' => 'Field Officer',
            'kyc_analyst' => 'KYC Analyst',
            'creation_unit' => 'Creation unit',
            'customer_experience_unit' => 'Customer Experience Unit'
        ]);
        $group_list->each(static function ($value, $key){
            Group::query()->firstOrCreate([
                'name' => $value,
                'role' => $key,
            ]);
        });
    }
}
