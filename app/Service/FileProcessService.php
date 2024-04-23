<?php

namespace App\Service;

use Illuminate\Support\LazyCollection;
use Shuchkin\SimpleXLSX;
class FileProcessService
{

    /**
     * @param $file
     * @return array
     */
    public function processExel($file): array
    {
        $results =  LazyCollection::make(function () use ($file) {
            $xlsx = SimpleXLSX::parse($file);
            foreach ($xlsx->rows() as $row) {
                yield $row;
            }
        })->skip(1);

        return $this->getListArray($results);
    }

    /**
     * @param $file
     * @return array
     */
    public function processCsv($file): array
    {
        $results  =  LazyCollection::make(function () use ($file) {
            $handle = fopen($file, 'r');
            while (($row = fgetcsv($handle, 100000)) !== false) {
                yield  $row;
            }
            fclose($handle);
        })->skip(1);
    
        return $this->getListArray($results);
    }

    /**
     * @param LazyCollection $results
     * @return array
     */
    public function getListArray(LazyCollection $results): array
    {
        $list = [];
        foreach ($results->chunk(500) as $chunks) {
            foreach ($chunks as $row) {
                if (isset($row[1]) && isset($row[0])) {
                    $list[$row[1]] = $row[0];
                }
            }
        }

        return $list;
    }


}
