<?php

namespace App\Imports;

/**
 * Parses the raw 2-D array produced by RansumImport into structured header data and items.
 *
 * Spreadsheet layout (1-based row numbers):
 *   Rows 1-3  : Logo / empty
 *   Row  4    : E="Vessel Code"  F=value  G="Vessel Name"      H=value  I="Voyage"   J=value  K="Contact Person"           L=value
 *   Row  5    : E="Year"         F=value  G="Date Start"        H=value  I="Date End" J=value  K="Jumlah Hari Pensupplaian"  L=value
 *   Row  6    : E="ETA"          F=value  G="Vessel Route"      H=value  I="Rute Sekarang" J=value K="Port Tujuan"           L=value
 *   Row  7    : E="Currency"     F=value  G="Conversi Rupiah"   H=value               K="Jumlah Crew"                       L=value
 *   Row  8    : E="Vendor Name"  F=value  G="Barang Non BKP"    H=value  I="Barang BKP"  J=value  K="Pajak 11%"             L=value
 *   Row  9    : E="Budget"       F=value  G="Total Belanja Ransum" H=value I="Selisih Anggaran & Pembelanjaan Ransum" J=value
 *   Row 10    : empty
 *   Row 11    : Main column headers (NAMA RANSUM …)
 *   Row 12+   : Section header rows and item data rows (alternating)
 *
 * Section header detection: a row whose column-B (index 1) value equals "KODE ITEM" (case-insensitive)
 * Column A of that row holds the section name (e.g. "BAHAN KERING").
 *
 * Item row columns (0-based):
 *   0  nama_ransum
 *   1  kode_item
 *   2  items
 *   3  merk_spec
 *   4  ppn
 *   5  supplier
 *   6  harga
 *   7  satuan
 *   8  qty  (PEMESANAN / ORDER)
 *   9  non_bkp
 *  10  bkp
 *  11  ppn_11
 *  12  ket_remarks
 *  13  status_received
 *  14  good_received
 */
class RansumParser
{
    private array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    // ------------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------------

    public function parseHeader(): array
    {
        // Convert to 1-based access using helper
        $r = fn (int $row, int $col) => $this->cell($row, $col);

        return [
            'vessel_code'               => $r(4, 5),   // F4
            'vessel_name'               => $r(4, 7),   // H4
            'voyage'                    => $r(4, 9),   // J4
            'contact_person'            => $r(4, 11),  // L4
            'year'                      => $r(5, 5),   // F5
            'date_start'                => $r(5, 7),   // H5
            'date_end'                  => $r(5, 9),   // J5
            'jumlah_hari_pensupplaian'  => $r(5, 11),  // L5
            'eta'                       => $r(6, 5),   // F6
            'vessel_route'              => $r(6, 7),   // H6
            'rute_sekarang'             => $r(6, 9),   // J6
            'port_tujuan'              => $r(6, 11),  // L6
            'currency'                  => $r(7, 5),   // F7
            'conversi_rupiah'           => $r(7, 7),   // H7
            'jumlah_crew'               => $r(7, 11),  // L7
            'vendor_name'               => $r(8, 5),   // F8
            'barang_non_bkp'            => $this->numeric($r(8, 7)),   // H8
            'barang_bkp'                => $this->numeric($r(8, 9)),   // J8
            'pajak_11'                  => $this->numeric($r(8, 11)),  // L8
            'budget'                    => $this->numeric($r(9, 5)),   // F9
            'total_belanja_ransum'      => $this->numeric($r(9, 7)),   // H9
            'selisih_anggaran'          => $this->numeric($r(9, 9)),   // J9
        ];
    }

    /**
     * Returns items grouped by section:
     * [
     *   ['section' => 'BAHAN KERING', 'items' => [[ ...columns... ], ...]],
     *   ...
     * ]
     */
    public function parseItems(): array
    {
        $sections = [];
        $currentSection = null;

        // Start scanning from row 12 (index 11) onwards
        foreach ($this->rows as $rowIndex => $row) {
            if ($rowIndex < 11) {
                continue;
            }

            $colA = trim((string) ($row[0] ?? ''));
            $colB = trim((string) ($row[1] ?? ''));

            // Section header: column B equals "KODE ITEM"
            if (strcasecmp($colB, 'KODE ITEM') === 0) {
                $currentSection = $colA ?: 'UNKNOWN';
                $sections[$currentSection] = $sections[$currentSection] ?? [];
                continue;
            }

            // Skip completely empty rows or rows without a name/code
            if ($colA === '' && $colB === '') {
                continue;
            }

            // Skip rows that look like sub-header or totals (no item code but starts with known keywords)
            if ($colB === '' && $this->isNonItemRow($colA)) {
                continue;
            }

            // Data row – map to structured item
            $item = $this->mapItemRow($row, $currentSection ?? 'UNKNOWN');

            // Only include rows that have at least a name or item code
            if ($item['nama_ransum'] !== null || $item['kode_item'] !== null) {
                $sections[$currentSection ?? 'UNKNOWN'][] = $item;
            }
        }

        // Convert associative to indexed for consistent output
        $result = [];
        foreach ($sections as $sectionName => $items) {
            if (count($items) > 0) {
                $result[] = ['section' => $sectionName, 'items' => $items];
            }
        }

        return $result;
    }

    /**
     * Flat list of all items (section name is embedded in each row).
     */
    public function parseItemsFlat(): array
    {
        $flat = [];
        foreach ($this->parseItems() as $group) {
            foreach ($group['items'] as $item) {
                $item['section'] = $group['section'];
                $flat[] = $item;
            }
        }
        return $flat;
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** Get a cell value by 1-based row and 1-based column number. */
    private function cell(int $row, int $col): mixed
    {
        $rowIdx = $row - 1;
        $colIdx = $col - 1;
        return $this->rows[$rowIdx][$colIdx] ?? null;
    }

    private function numeric(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $cleaned = str_replace([',', ' '], ['', ''], (string) $value);
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    private function str(mixed $value): ?string
    {
        $s = trim((string) ($value ?? ''));
        return $s !== '' ? $s : null;
    }

    private function isNonItemRow(string $colA): bool
    {
        $keywords = ['jumlah', 'total', 'sub total', 'subtotal', 'grand total', 'keterangan'];
        $lower = strtolower($colA);
        foreach ($keywords as $kw) {
            if (str_starts_with($lower, $kw)) {
                return true;
            }
        }
        return false;
    }

    private function mapItemRow(array $row, string $section): array
    {
        return [
            'section'         => $section,
            'nama_ransum'     => $this->str($row[0] ?? null),
            'kode_item'       => $this->str($row[1] ?? null),
            'items'           => $this->str($row[2] ?? null),
            'merk_spec'       => $this->str($row[3] ?? null),
            'ppn'             => $this->numeric($row[4] ?? null),
            'supplier'        => $this->str($row[5] ?? null),
            'harga'           => $this->numeric($row[6] ?? null),
            'satuan'          => $this->str($row[7] ?? null),
            'qty'             => $this->numeric($row[8] ?? null),
            'non_bkp'         => $this->numeric($row[9] ?? null),
            'bkp'             => $this->numeric($row[10] ?? null),
            'ppn_11'          => $this->numeric($row[11] ?? null),
            'ket_remarks'     => $this->str($row[12] ?? null),
            'status_received' => $this->str($row[13] ?? null),
            'good_received'   => $this->str($row[14] ?? null),
        ];
    }
}
