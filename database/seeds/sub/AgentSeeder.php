<?php

use App\Models\Agent;
use App\Models\Lga;
use App\Models\State;
use App\Models\Territory;
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
       //factory(Agent::class, 200)->create();
        $agent_list =  json_decode(file_get_contents(__DIR__.'/extra/agents.json'), true);

        foreach ($agent_list as $agent_data){
            /**
            "Username": "360GUARDAPP (018717)",
             */

            /** @var State $state */
            $state = State::query()->where('name', $agent_data['State'])->first();
            $territory =  Territory::query()->where([
                'state_id' => $state->id??null,
                'name' => $agent_data['Area']??''
            ])->first();
            $lga =  Lga::query()->where([
                'state_id' => $state->id??null,
                'name' => $agent_data['Area']??''
            ])->first();
            $names = explode(' ', $agent_data['Agent']);

            $agent_code = explode(' ', $agent_data['Username']??' ');

            Agent::query()->create([
                'territory_id' => $territory->id?? null,
                'is_app_only' => 0,
                'first_name'   => @$names[0],
                'last_name'    => @$names[1],
                'user_name'    => @$agent_code[0],
                'device_serial_no' => $agent_data['Device Serial Number'],
                'status'    => @$agent_data['Area'] === 'Yes' ? 1: 0,
                'code'      => trim(@$agent_code[1], '()'),
                'phone_number' => $agent_data['Phone Number']??'',
                'email' => '',
                'address' => $agent_data['Address']??'',
                'city' => $agent_data['Area']?? '',
                'lga_id' => $lga->id??null,
                'state_id' => $state->id??null,
            ]);
        }
    }
}
