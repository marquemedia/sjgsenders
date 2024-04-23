<?php

namespace App\Service;

use App\Models\Contact;
use App\Models\EmailContact;
use App\Models\Import;
use Illuminate\Support\LazyCollection;
use Shuchkin\SimpleXLSX;
class ImportContactService
{

    /**
     * @param array $row
     * @return Import
     */
    public function save(array $row): Import {
        
        return Import::create($row);
    }

    /**
     * @param string $path
     * @param string $mime
     * @param int|null $userId
     * @param string $type
     * @param int $groupId
     * @return array
     */
    public function prepParams(string $path, string $mime, ?int $userId, string $type, int $groupId): array{

        return [
            'user_id' => $userId,
            'name' => request()->file('file')->getClientOriginalName(),
            'path' => $path,
            'mime' => $mime,
            'group_id' => $groupId,
            'type' => $type,
        ];
    }


    /**
     * @param string $filename
     * @return array
     */
    public function getCsv(string $filename): array{
        $header = null;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {

                if (!$header) {
                    $header = $row;
                    break;
                }
            }
            fclose($handle);
        }
        return $header;
    }


    /**
     * @param Import $import
     * @param string $filename
     * @param array $hearder
     * @return void
     */
    public function importContactFormCsv(Import $import, string $filename, array $hearder): void
    {

        LazyCollection::make(function () use ($import,$filename,$hearder) {
            $handle = fopen($filename, 'r');
            while (($row = fgetcsv($handle, 100000)) !== false) {
                yield  $row;
            }
            fclose($handle);

        })->skip(1)->chunk(500)->each(function (LazyCollection $records) use ($import){
            $records->each(function($record, $key) use($import){
                if($import->type == "email"){
                    if(isset($record['0']) && $record['1'] && filter_var($record['1'], FILTER_VALIDATE_EMAIL)) {
                        Contact::create([
                            'user_id'       => $import->user_id,
                            'group_id'      => $import->group_id,
                            'name'          => $record['0'],
                            'email'         => $record['1'],
                            'status'        => 1,
                        ]);
                    }

                }

                if($import->type == "sms"){
                    if( isset($record['0']) && $record['1'] && filter_var($record['1'], FILTER_SANITIZE_NUMBER_INT)) {
                        $contact = preg_replace('/[^0-9]/', '', trim(str_replace('+', '', $record['1'])));
                        Contact::create([
                            'user_id'   => $import->user_id,
                            'group_id'  => $import->group_id,
                            'name'      => $record['0'],
                            'contact_no'=> $contact,
                            'status'    => 1,
                        ]);
                    }
                }
            });
        });

        if($import->status == 1){
            $this->unlinkFile($import);
        }
    }

    /**
     * @param Import $import
     * @param string $filename
     * @return void
     */
    public function importContactFormExel(Import $import, string $filename): void
    {

        LazyCollection::make(function () use ($import,$filename) {
            $xlsx = SimpleXLSX::parse($filename);
            foreach ($xlsx->rows() as $row) {
                yield $row;

            }

        })->skip(1)->chunk(500)->each(function (LazyCollection $records) use ($import){
            $records->each(function($record, $key) use($import){
                if($import->type == "email"){
                    if(isset($record['0']) && $record['1'] && filter_var($record['1'], FILTER_VALIDATE_EMAIL)){
                        EmailContact::create([
                            'user_id'       => $import->user_id,
                            'email_group_id'=> $import->group_id,
                            'name'          => $record['0'],
                            'email'         => $record['1'],
                            'status'        => 1,
                        ]);
                    }
                }
                if($import->type == "sms"){
                    if( isset($record['0']) && $record['1'] && filter_var($record['1'], FILTER_SANITIZE_NUMBER_INT)){
                        $contact = preg_replace('/[^0-9]/', '', trim(str_replace('+', '', $record['1'])));
                        Contact::create([
                            'user_id'   => $import->user_id,
                            'group_id'  => $import->group_id,
                            'name'      => $record['0'],
                            'contact_no'=>   $contact,
                            'status'    => 1,
                        ]);
                    }

                }

            });
        });


        if($import->status == 1){
            $this->unlinkFile($import);
        }
    }

    /**
     * @param $import
     * @return void
     */
    public function unlinkFile($import): void
    {
        if(@unlink(('assets/file/import/'.$import->path))){
            $import->delete();
        }

    }

}
