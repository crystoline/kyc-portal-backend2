<?php

use App\Models\State;
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
        //factory(Territory::class, 50)->create();s
        $territories_list =  json_decode(file_get_contents(__DIR__.'/extra/territories.json'), true);
        foreach ($territories_list as $data){
            $state_name = $data['State']??'';
            $name = $data['Name']??'';
            $territory = $data['Territory']??'';
            $region = $data['Region']??'';
            /** @var State $state */
            $state = State::query()->where('name', $state_name)->first();
            if($state !== null) {
                Territory::query()->create([
                    'name' => $name,
                    'state_id' => $state->id,
                    'territory' => $territory,
                    'region' => $region
                ]);
            }
        }
    }
}
