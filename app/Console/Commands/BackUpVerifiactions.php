<?php

namespace App\Console\Commands;

use App\Models\Verification;
use App\Util\ExportVerification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class BackUpVerifiactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var Collection $verifications */
        $verifications = Verification::query()->where([
            'status' => 1,
            'backup' => 0
        ])->get();

        /** @var Verification $verification */
        foreach ($verifications as $verification){
            $export =  new ExportVerification($verification);
            try{
                $export->generateFilesFromData();
            }catch (\Exception $exception){
                Log::error("Export: \n".
                    'Message: '.$exception->getMessage()."\n".
                    'File: '.$exception->getFile()."\n".
                    'Line: '.$exception->getLine()."\n"
                );
            }
        }
    }
}
