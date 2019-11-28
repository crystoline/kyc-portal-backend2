<?php

use App\Models\BankType;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = json_decode(file_get_contents(__DIR__.'/banks.json'), true);

        foreach ($banks as $bank){
            /** @var BankType $bank_type */
            $bank_type = BankType::query()->firstOrCreate(['name' => $bank['type']]);
            if($bank_type){
                $bank_type->banks()->create(['name' => $bank['name'], 'nibss_code' => $bank['nibss_code'], 'isw_code' => $bank['isw_code']]);
            }

        }
    }
}
