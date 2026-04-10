<?php

namespace Tests\Unit;

use App\Imports\RansumParser;
use PHPUnit\Framework\TestCase;

class RansumParserTest extends TestCase
{
    /** Number of columns in the standard spreadsheet layout. */
    private const COL_COUNT = 16;

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Build a minimal rows array suitable for RansumParser.
     * Rows 0-9  : empty (header meta rows – skipped by the parser).
     * Row  10   : column header row (customisable via $headerRow).
     * Row  11   : section header row ("KODE ITEM" in column B).
     * Row  12   : one data item row (customisable via $dataRow).
     *
     * @param array $headerRow   COL_COUNT-element array for the column header row.
     * @param array $dataRow     COL_COUNT-element array for the item data row.
     */
    private function buildRows(array $headerRow, array $dataRow): array
    {
        $empty = array_fill(0, self::COL_COUNT, null);

        $rows = array_fill(0, 10, $empty);   // rows 0-9 (index 0-9)
        $rows[] = $headerRow;                 // row index 10 (row 11, 1-based)
        $rows[] = array_replace($empty, [0 => 'BAHAN KERING', 1 => 'KODE ITEM']); // section header
        $rows[] = $dataRow;                   // data row

        return $rows;
    }

    /** Standard COL_COUNT-column header row used across multiple tests. */
    private function standardHeader(string $remarksLabel = 'REMARKS'): array
    {
        return [
            'NAMA RANSUM',    // 0
            'KODE ITEM',      // 1
            'ITEMS',          // 2
            'MERK/SPEC',      // 3
            'PPN',            // 4
            'HARGA SUPPLIER', // 5
            'SATUAN',         // 6
            'QTY',            // 7
            'HARGA',          // 8
            'JUMLAH',         // 9
            'NON BKP',        // 10
            'BKP',            // 11
            'PPN 11%',        // 12
            $remarksLabel,    // 13
            'STATUS RECEIVED',// 14
            'GOOD RECEIVED',  // 15
        ];
    }

    /** Standard data row with a remarks value at column index 13. */
    private function standardDataRow(string $remarks = 'Test Remark'): array
    {
        return [
            'Beras Putih',  // 0  nama_ransum
            'BK001',        // 1  kode_item
            'Beras',        // 2  items
            'Merk A',       // 3  merk_spec
            0,              // 4  ppn
            'Supplier A',   // 5  supplier
            'PCS',          // 6  satuan
            10,             // 7  qty
            5000,           // 8  harga
            50000,          // 9  jumlah (skipped)
            null,           // 10 non_bkp
            null,           // 11 bkp
            null,           // 12 ppn_11
            $remarks,       // 13 ket_remarks
            'OK',           // 14 status_received
            'YES',          // 15 good_received
        ];
    }

    // ------------------------------------------------------------------
    // ket_remarks header detection tests
    // ------------------------------------------------------------------

    /** Header column named "REMARKS" (uppercase, no prefix). */
    public function test_remarks_header_uppercase_maps_to_ket_remarks(): void
    {
        $rows   = $this->buildRows($this->standardHeader('REMARKS'), $this->standardDataRow('Urgent order'));
        $parser = new RansumParser($rows);
        $items  = $parser->parseItemsFlat();

        $this->assertCount(1, $items);
        $this->assertSame('Urgent order', $items[0]['ket_remarks']);
    }

    /** Header column named "remarks" (lowercase). */
    public function test_remarks_header_lowercase_maps_to_ket_remarks(): void
    {
        $rows   = $this->buildRows($this->standardHeader('remarks'), $this->standardDataRow('Low stock'));
        $parser = new RansumParser($rows);
        $items  = $parser->parseItemsFlat();

        $this->assertCount(1, $items);
        $this->assertSame('Low stock', $items[0]['ket_remarks']);
    }

    /** Header column named "Remarks" (title case). */
    public function test_remarks_header_titlecase_maps_to_ket_remarks(): void
    {
        $rows   = $this->buildRows($this->standardHeader('Remarks'), $this->standardDataRow('Check quality'));
        $parser = new RansumParser($rows);
        $items  = $parser->parseItemsFlat();

        $this->assertCount(1, $items);
        $this->assertSame('Check quality', $items[0]['ket_remarks']);
    }

    /** Header column named "KET. REMARKS" (full Indonesian label). */
    public function test_ket_remarks_header_maps_to_ket_remarks(): void
    {
        $rows   = $this->buildRows($this->standardHeader('KET. REMARKS'), $this->standardDataRow('Special note'));
        $parser = new RansumParser($rows);
        $items  = $parser->parseItemsFlat();

        $this->assertCount(1, $items);
        $this->assertSame('Special note', $items[0]['ket_remarks']);
    }

    /** Header column named "KET REMARKS" (without dot). */
    public function test_ket_remarks_no_dot_header_maps_to_ket_remarks(): void
    {
        $rows   = $this->buildRows($this->standardHeader('KET REMARKS'), $this->standardDataRow('Noted'));
        $parser = new RansumParser($rows);
        $items  = $parser->parseItemsFlat();

        $this->assertCount(1, $items);
        $this->assertSame('Noted', $items[0]['ket_remarks']);
    }

    /**
     * When the "REMARKS" column is detected at a non-default position,
     * the colMap should use the detected index.
     */
    public function test_remarks_detected_at_non_default_column_index(): void
    {
        // Build a header where REMARKS is at index 14 (not the default 13).
        $header = [
            'NAMA RANSUM',    // 0
            'KODE ITEM',      // 1
            'ITEMS',          // 2
            'MERK/SPEC',      // 3
            'PPN',            // 4
            'HARGA SUPPLIER', // 5
            'SATUAN',         // 6
            'QTY',            // 7
            'HARGA',          // 8
            'JUMLAH',         // 9
            'NON BKP',        // 10
            'BKP',            // 11
            'PPN 11%',        // 12
            'STATUS RECEIVED',// 13  (swapped with default ket_remarks position)
            'REMARKS',        // 14  (moved one position to the right)
            'GOOD RECEIVED',  // 15
        ];

        $dataRow = [
            'Ayam',         // 0
            'AY001',        // 1
            'Ayam Beku',    // 2
            'Merk B',       // 3
            0,              // 4
            'Supplier B',   // 5
            'KG',           // 6
            5,              // 7
            20000,          // 8
            100000,         // 9
            null,           // 10
            null,           // 11
            null,           // 12
            'OK',           // 13 status_received
            'Frozen item',  // 14 ket_remarks (non-default index!)
            'YES',          // 15
        ];

        $rows   = $this->buildRows($header, $dataRow);
        $parser = new RansumParser($rows);
        $items  = $parser->parseItemsFlat();

        $this->assertCount(1, $items);
        $this->assertSame('Frozen item', $items[0]['ket_remarks']);
        $this->assertSame('OK', $items[0]['status_received']);
    }

    /** Empty remarks value produces null ket_remarks. */
    public function test_empty_remarks_cell_produces_null_ket_remarks(): void
    {
        $rows   = $this->buildRows($this->standardHeader('REMARKS'), $this->standardDataRow(''));
        $parser = new RansumParser($rows);
        $items  = $parser->parseItemsFlat();

        $this->assertCount(1, $items);
        $this->assertNull($items[0]['ket_remarks']);
    }
}
