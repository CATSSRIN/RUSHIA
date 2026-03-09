<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Generic import class used to read any spreadsheet into a raw 2-D array.
 * Sheet selection and further parsing are left to the caller.
 */
class GenericImport implements ToArray
{
    private array $data = [];

    public function array(array $rows): void
    {
        $this->data = $rows;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
