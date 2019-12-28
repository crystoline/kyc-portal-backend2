<?php


namespace App\Util;


use App\Models\Document;
use App\Models\Verification;
use Exception;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

class ExportVerification
{
    /**
     * @var Verification
     */
    private $verification;

    public function __construct(Verification $verification)
    {

        $this->verification = $verification;
        //$this->generateFilesFromData();
    }

    /**
     * @param $dir_name
     * @return mixed
     */
    private static function getDirectory($dir_name)
    {
       // $contents = collect(Storage::disk('google')->allFiles('/'));// ->directories('/'));
        $contents = collect(Storage::cloud()->listContents('/'));
        dump($contents);
        $dir = $contents->where('filename', '=', $dir_name)->first();
        return $dir;
    }

    /**
     * @throws Exception
     */
    public function generateFilesFromData(): void
    {
        $files = [];
        $temp_dir = '.';//__DIR__;
        $zip_path = $temp_dir.'/'.$this->verification->agent->code.'.zip';
        $zip = Zip::create($zip_path, true);
       $personsal_information_url = $this->generatePersonalDataFile($temp_dir, 'data.txt');
       if($personsal_information_url){
           /*$personal_file = $temp_dir.'/data.txt';
           copy($personsal_information_url, $personal_file);*/
           $files[] = $personsal_information_url;
       }

       if( $this->verification->passport){
           @$content = file_get_contents( $this->verification->passport);
           if($content){
               $passport_path =  $personal_file = $temp_dir.'/passport.jpg';
               file_put_contents($content, $passport_path);
               $files[] =$passport_path;
           }

       }
       if($this->verification->documents){
           /** @var Document $document */
           foreach($this->verification->documents as $document){
               @$content = file_get_contents( $document->url);
               if($content){
                   $title = !$document->page_number? $document->title : $document->title.'Page : '.$document->page_number;
                   $document_path =  $personal_file = "{$temp_dir}/{$title}.jpg";
                   file_put_contents($content, $document_path);
                   $files[] =$document_path;
               }
           }
           if( $this->verification->guarantorInformation->signature){
               @$content = file_get_contents( $this->verification->guarantorInformation->signature);
               if($content){
                   $signature_path =  $personal_file = $temp_dir.'/guarantor-signature.jpg';
                   file_put_contents($content, $signature_path);
                   $files[] =$signature_path;
               }
           }
            if( @$this->verification->guarantorInformation->witness_signature){
                @$content = file_get_contents( $this->verification->guarantorInformation->witness_signature);
                if($content){
                    $witness_signature =  $personal_file = $temp_dir.'/witness-signature.jpg';
                    file_put_contents($content, $witness_signature);
                    $files[] =$witness_signature;
                }
            }
       }
        $zip->add( $files);
        $zip->close();
       // $guarantor_information_url = $this->generateGuarantorDataFile();
        dump($files);
        //save file to drive
        $path = preg_replace('/[^a-zA-Z]/', '-', $this->verification->verificationPeriod->title);

       //Storage::disk('google')->put($path."/{$this->verification->agent->code}.zip", file_get_contents($zip_path));
        self::putInDirectory($path, "{$this->verification->agent->code}.zip", file_get_contents($zip_path));
        unlink($zip_path);
        //copy($zip_path, public_path($this->verification->agent->code.'.zip'));
        $this->verification->backup = 1;
        $this->verification->save();
    }

    /**
     * @param $dir_name
     * @param $file_name
     * @param $file_string
     * @throws Exception
     */
    private static function putInDirectory($dir_name, $file_name, $file_string): void
    {
        $dir = self::checkInDirectory($dir_name); // There could be duplicate directory names!
        if (!$dir) {
            throw new Exception("Could not get/create directory {$dir_name}");
        }
        Storage::disk('google')->put("{$dir['path']}/{$file_name}", $file_string);
    }

    private static function checkInDirectory($dir_name){
        $dir = self::getDirectory($dir_name); // There could be duplicate directory names!
        if (empty($dir)) {
            Storage::disk('google')->makeDirectory($dir_name);
            $dir = self::getDirectory($dir_name);
        }
        return $dir;
    }

    /**
     * Creates a random unique temporary directory, with specified parameters,
     * that does not already exist (like tempnam(), but for dirs).
     *
     * Created dir will begin with the specified prefix, followed by random
     * numbers.
     *
     * @link https://php.net/manual/en/function.tempnam.php
     *
     * @param string|null $dir Base directory under which to create temp dir.
     *     If null, the default system temp dir (sys_get_temp_dir()) will be
     *     used.
     * @param string $prefix String with which to prefix created dirs.
     * @param int $mode Octal file permission mask for the newly-created dir.
     *     Should begin with a 0.
     * @param int $maxAttempts Maximum attempts before giving up (to prevent
     *     endless loops).
     * @return string|bool Full path to newly-created dir, or false on failure.
     * @throws Exception
     */
    public static function tempDir($dir = null, $prefix = 'tmp_', $mode = 0700, $maxAttempts = 1000)
    {
        /* Use the system temp dir by default. */
        if ($dir === null) {
            $dir = sys_get_temp_dir();
        }

        /* Trim trailing slashes from $dir. */
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);

        /* If we don't have permission to create a directory, fail, otherwise we will
         * be stuck in an endless loop.
         */
        if (!is_dir($dir) || !is_writable($dir)) {
            return false;
        }

        /* Make sure characters in prefix are safe. */
        if (strpbrk($prefix, '\\/:*?"<>|') !== false) {
            return false;
        }

        /* Attempt to create a random directory until it works. Abort if we reach
         * $maxAttempts. Something screwy could be happening with the filesystem
         * and our loop could otherwise become endless.
         */
        $attempts = 0;
        do {
            $path = sprintf('%s%s%s%s', $dir, DIRECTORY_SEPARATOR, $prefix, random_int(100000, mt_getrandmax()));
        } while (!mkdir($path, $mode) && !is_dir($path) && $attempts++ < $maxAttempts);

        return $path;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function generatePersonalDataFile($dir, $filename)
    {
        $fh = fopen($dir.'/'.$filename, 'w+');
        if (!$fh) {
            throw new Exception('Could not create tmp file for ' . __METHOD__);
        }
        $path = stream_get_meta_data($fh)['uri'];
        //print_r($path);
        //dump($this->verification->personalInformation);
        $data = $this->verification->load([
            'personalInformation',
            'guarantorInformation',
            'verifiedBy',
            'approvedBy'
        ])->toFormattedString();
        fwrite($fh, $data);
        fclose($fh);
       // dump($data);
        return $path;

    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function generateGuarantorDataFile()
    {
        $fh = tmpfile();
        if (!$fh) {
            throw new Exception('Could not create tmp file for ' . __METHOD__);
        }
        $path = stream_get_meta_data($fh)['uri'];
        print_r($path);
        $data = $this->verification->guarantorInformation->toFormattedString();
        fwrite($fh, $data);
        fclose($fh);
        dump($data);
        return $path;
    }

}