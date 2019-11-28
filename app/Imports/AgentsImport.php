<?php

namespace App\Imports;

use App\Models\Agent;
use App\Models\Lga;
use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class AgentsImport implements /*ToModel,*/ OnEachRow, WithHeadingRow, WithValidation
{
    use Importable;
    /**
     * @param Row $row
     */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();

        $p_agent_code = $row['principal_agent_code']??null;
        /** @var Agent $parent_agent */
        $parent_agent = $p_agent_code !== null? Agent::query()->where(['code'=>$p_agent_code])->first(): null;

        $state_name = $row['state']??null;
        /** @var State $parent_agent */
        $state = $state_name !== null? State::query()->where(['name'=>$state_name])->first(): null;

        $lga_name = $row['lga']??null;
        /** @var Lga $parent_agent */
        $lga = $lga_name !== null? State::query()->where(['name'=>$lga_name])->first(): null;

        return Agent::query()->updateOrCreate([
            'code' => $row['agent_code']
        ], [
            'parent_agent_id' => $parent_agent? $parent_agent->id: null,
            'type' => $parent_agent? 'sole-agent': 'principal-agent',
            'first_name' => $row['first_name']??null,
            'last_name' => $row['user_name']??null,
            'user_name' => $row['last_name']??null,
            'gender' => isset($row['gender'])? strtolower($row['gender']): null,
            'is_app_only' =>  isset($row['app_only']) && strtolower($row['app_only']) === 'yes'? : 0,
            'passport' => $row['passport_url']??null,
            'date_of_birth' => $row['date_of_birth']??null,
            'email' => $row['email']??null,
            'phone_number' => $row['phone_number']??null,
            'address' => $row['address']??null,
            'city' => $row['city']??null,
            'state_id' => $state? $state->id: null,
            'lga_id' => $lga? $lga->id: null,

        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        //die('kkks');
        return [
          '*.email' => 'email',
          '*.parent_agent_code' => 'sometimes|exists:agent,code'
        ];
    }


}
