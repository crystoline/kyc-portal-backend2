<?php

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $ng_states = include 'extra/nigeria-states-array.php';
        if (!isset($ng_states)) {
            $ng_states = [];
        }
        foreach ($ng_states as $state) {
            //$state_obj = $nigeria->states()->create(['name' => $state['name'], 'code' => $state['code']]);

            if (!empty($state['name']) && !empty( $state['code']) && !empty($state['lga'])) {

                /** @var State $ng_state */
                $ng_state = State::query()->firstOrCreate([
                    'name' => $state['name'],
                    'code' => $state['code']
                ]);
                if ($ng_state) {
                    $ng_state->lgas()->createMany($state['lga']);
                }
            }
        }
    }
}
