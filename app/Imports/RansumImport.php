<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

/**
 * Reads the BPB Ransum spreadsheet (sheet "Ransum") into a raw array.
 * Actual parsing of header fields and item rows is handled by RansumParser.
 */
class RansumImport implements ToArray, WithCalculatedFormulas
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
