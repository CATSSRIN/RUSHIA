<?php

namespace App\Imports;

/**
 * Parses the raw 2-D array produced by RansumImport into structured header data and items.
 *
 * Spreadsheet layout (1-based row numbers):
 *   Rows 1-3  : Logo / empty
 *   Row  4    : E="Vessel Code"  F=value  G="Vessel Name"         H=value  I="Voyage"   J=value  K="Contact Person"           L=value
 *   Row  5    : E="Year"         F=value  G="Date Start"          H=value  I="Date End" J=value  K="Jumlah Hari Pensupplaian"  L=value
 *   Row  6    : E="ETA"          F=value  G="Vessel Route"         H=value  I="Rute Sekarang" J=value K="Port Tujuan"          L=value
 *   Row  7    : E="Currency"     F=value  G="Conversi Rupiah"      H=value               K="Jumlah Crew"                      L=value
 *   Row  8    : E="Vendor Name"  F=value  G="Barang Non BKP"       H=value  I="Barang BKP"  J=value  K="Pajak 11%"            L=value
 *   Row  9    : E="Budget"       F=value  G="Total Belanja Ransum" H=value  I="Selisih Anggaran & Pembelanjaan Ransum" J=value
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

    /** Resolved column indices for item rows (0-based). */
    private ?array $colMap = null;

    /** Default column positions (0-based) if header row detection fails. */
    private const DEFAULT_COL = [
        'nama_ransum'     => 0,
        'kode_item'       => 1,
        'items'           => 2,
        'merk_spec'       => 3,
        'ppn'             => 4,
        'supplier'        => 5,
        'harga'           => 6,
        'satuan'          => 7,
        'qty'             => 8,
        'non_bkp'         => 9,
        'bkp'             => 10,
        'ppn_11'          => 11,
        'ket_remarks'     => 12,
        'status_received' => 13,
        'good_received'   => 14,
    ];

    /** Keywords that mark the start of the signature block (case-insensitive). */
    private const SIGNATURE_BLOCK_KEYWORDS = ['pemohon', 'menyetujui'];

    /** Regex to reject non-name values (labels/keywords) in signature rows. */
    private const SIGNATURE_LABEL_PATTERN = '/pemohon|menyetujui|tanggal|date|ttd/i';

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
            'vessel_code'               => $r(4, 6),   // F4
            'vessel_name'               => $r(4, 8),   // H4
            'voyage'                    => $r(4, 10),  // J4
            'contact_person'            => $r(4, 12),  // L4
            'year'                      => $r(5, 6),   // F5
            'date_start'                => $r(5, 8),   // H5
            'date_end'                  => $r(5, 10),  // J5
            'jumlah_hari_pensupplaian'  => $r(5, 12),  // L5
            'eta'                       => $r(6, 6),   // F6
            'vessel_route'              => $r(6, 8),   // H6
            'rute_sekarang'             => $r(6, 10),  // J6
            'port_tujuan'               => $r(6, 12),  // L6
            'currency'                  => $r(7, 6),   // F7
            'conversi_rupiah'           => $r(7, 8),   // H7
            'jumlah_crew'               => $r(7, 12),  // L7
            'vendor_name'               => $r(8, 6),   // F8
            'barang_non_bkp'            => $this->numeric($r(8, 8)),   // H8
            'barang_bkp'                => $this->numeric($r(8, 10)),  // J8
            'pajak_11'                  => $this->numeric($r(8, 12)),  // L8
            'budget'                    => $this->numeric($r(9, 6)),   // F9
            'total_belanja_ransum'      => $this->numeric($r(9, 8)),   // H9
            'selisih_anggaran'          => $this->numeric($r(9, 10)),  // J9
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

            // Stop processing items when the signature block is reached
            if ($this->rowContainsSignatureKeyword($row)) {
                break;
            }

            // Skip completely empty rows or rows without a name/code
            if ($colA === '' && $colB === '') {
                continue;
            }

            // Skip rows that look like sub-header or totals (no item code but starts with known keywords)
            if ($colB === '' && $this->isNonItemRow($colA)) {
                continue;
            }

            // Skip label-only rows: colA has text but no other column has any data.
            // These are typically section descriptors or notes (e.g. in the Frozen section)
            // that should not be treated as item rows.
            if ($colB === '' && $colA !== '' && $this->isLabelOnlyRow($row)) {
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

    /**
     * Returns true when any cell in the row contains a signature block keyword.
     * Used to detect the pemohon/menyetujui footer and stop item parsing.
     */
    private function rowContainsSignatureKeyword(array $row): bool
    {
        foreach ($row as $cell) {
            $lower = strtolower(trim((string)($cell ?? '')));
            foreach (self::SIGNATURE_BLOCK_KEYWORDS as $keyword) {
                if (str_contains($lower, $keyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns true when every column after colA (index 0) is empty.
     * Such rows are descriptive labels (e.g. section notes in the Frozen section)
     * and must not be treated as item rows.
     */
    private function isLabelOnlyRow(array $row): bool
    {
        $len = count($row);
        for ($i = 1; $i < $len; $i++) {
            if (trim((string) ($row[$i] ?? '')) !== '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Detect item column indices from the header row (row 11, index 10).
     * Falls back to DEFAULT_COL if detection fails.
     */
    private function getColMap(): array
    {
        if ($this->colMap !== null) {
            return $this->colMap;
        }

        // Row 11 = index 10
        $headerRow = $this->rows[10] ?? [];

        // Patterns ordered carefully to avoid false matches (ppn_11 before ppn, bkp before non_bkp)
        $patterns = [
            'ppn_11'          => '/ppn.*11|11.*ppn/i',
            'non_bkp'         => '/non.*bkp/i',
            'status_received' => '/status.*rec|received.*status/i',
            'good_received'   => '/good.*rec|gr\b/i',
            'ket_remarks'     => '/ket\.?\s*remarks|ket\b|remarks/i',
            'nama_ransum'     => '/nama.*ransum|ransum/i',
            'kode_item'       => '/kode.*item/i',
            'items'           => '/^items?$/i',
            'merk_spec'       => '/merk|spec/i',
            'ppn'             => '/^ppn$/i',
            'supplier'        => '/supplier/i',
            'harga'           => '/harga/i',
            'satuan'          => '/satuan|unit/i',
            'qty'             => '/qty|pemesanan|order/i',
            'bkp'             => '/^bkp$/i',
        ];

        $map = self::DEFAULT_COL;
        $assigned = [];

        foreach ($headerRow as $idx => $cell) {
            $cell = trim((string) ($cell ?? ''));
            if ($cell === '') {
                continue;
            }
            foreach ($patterns as $field => $pattern) {
                if (!isset($assigned[$field]) && preg_match($pattern, $cell)) {
                    $map[$field]      = $idx;
                    $assigned[$field] = true;
                    break;
                }
            }
        }

        // Resolve conflicts: if an undetected field still holds its DEFAULT_COL index
        // and that same index was already claimed by a detected field, reset it to an
        // out-of-range sentinel so mapItemRow returns null instead of duplicating data.
        //
        // BPB Ransum templates always place satuan (unit text) at column H (index 7) and
        // qty (order quantity) at column I (index 8) in data rows.  The header row often
        // uses a merged cell "PEMESANAN / ORDER" that spans H-I, so PhpSpreadsheet places
        // the label at index 7 and returns null for index 8.  This causes the qty pattern
        // to match at index 7 instead of the correct index 8.
        $detectedIndices = [];
        foreach ($assigned as $detectedField => $_) {
            $detectedIndices[$map[$detectedField]] = $detectedField;
        }

        // If satuan was not explicitly detected and qty was detected at satuan's column
        // (index 7), always move qty to index 8 so that satuan reads the correct unit
        // text and qty reads the correct quantity.  This applies regardless of what else
        // may have been detected at index 8.
        if (!isset($assigned['satuan']) && isset($assigned['qty']) && $map['qty'] === self::DEFAULT_COL['satuan']) {
            unset($detectedIndices[$map['qty']]);
            $map['qty'] = self::DEFAULT_COL['qty'];
            $detectedIndices[$map['qty']] = 'qty';
        }

        // General conflict resolution
        foreach ($map as $field => $idx) {
            if (!isset($assigned[$field]) && isset($detectedIndices[$idx])) {
                $map[$field] = PHP_INT_MAX;
            }
        }

        // Safety fallback: the template always has satuan at index 7 and qty at index 8;
        // never allow conflict resolution to leave either field unreadable.
        if ($map['satuan'] === PHP_INT_MAX) {
            $map['satuan'] = self::DEFAULT_COL['satuan'];
        }
        if ($map['qty'] === PHP_INT_MAX) {
            $map['qty'] = self::DEFAULT_COL['qty'];
        }

        $this->colMap = $map;
        return $this->colMap;
    }

    private function mapItemRow(array $row, string $section): array
    {
        $c = $this->getColMap();

        return [
            'section'         => $section,
            'nama_ransum'     => $this->str($row[$c['nama_ransum']] ?? null),
            'kode_item'       => $this->str($row[$c['kode_item']] ?? null),
            'items'           => $this->str($row[$c['items']] ?? null),
            'merk_spec'       => $this->str($row[$c['merk_spec']] ?? null),
            'ppn'             => $this->numeric($row[$c['ppn']] ?? null),
            'supplier'        => $this->str($row[$c['supplier']] ?? null),
            'harga'           => $this->numeric($row[$c['harga']] ?? null),
            'satuan'          => $this->str($row[$c['satuan']] ?? null),
            'qty'             => $this->numeric($row[$c['qty']] ?? null),
            'non_bkp'         => $this->numeric($row[$c['non_bkp']] ?? null),
            'bkp'             => $this->numeric($row[$c['bkp']] ?? null),
            'ppn_11'          => $this->numeric($row[$c['ppn_11']] ?? null),
            'ket_remarks'     => $this->str($row[$c['ket_remarks']] ?? null),
            'status_received' => $this->str($row[$c['status_received']] ?? null),
            'good_received'   => $this->str($row[$c['good_received']] ?? null),
        ];
    }

    /**
     * Parse signature block from the bottom of the spreadsheet.
     * Returns ['pemohon' => string|null, 'menyetujui' => string|null].
     *
     * Typical layout (appears after all items, near end of file):
     *   Row X  : ... "Pemohon,"  ...  "Menyetujui,"  ...
     *   Row X+1: (signature space)
     *   Row X+2: ... [name1]  ...  [name2]  ...
     */
    public function parseSignatures(): array
    {
        $pemohon    = null;
        $menyetujui = null;

        $totalRows = count($this->rows);
        // Scan from the end, limit to last 30 rows to avoid false positives in item rows
        $startIdx = max(11, $totalRows - 30);

        for ($i = $startIdx; $i < $totalRows; $i++) {
            $row = $this->rows[$i] ?? [];
            $rowText = strtolower(implode(' ', array_map(fn($v) => trim((string)($v ?? '')), $row)));

            $hasPemohon    = str_contains($rowText, 'pemohon');
            $hasMenyetujui = str_contains($rowText, 'menyetujui');

            if (!$hasPemohon && !$hasMenyetujui) {
                continue;
            }

            // Found the label row – find column indices for each label
            $pemohonCol    = null;
            $menyetujuiCol = null;

            foreach ($row as $colIdx => $cell) {
                $cellLower = strtolower(trim((string)($cell ?? '')));
                if ($pemohonCol === null && str_contains($cellLower, 'pemohon')) {
                    $pemohonCol = $colIdx;
                }
                if ($menyetujuiCol === null && str_contains($cellLower, 'menyetujui')) {
                    $menyetujuiCol = $colIdx;
                }
            }

            // Look up to 3 rows below for the actual name
            for ($offset = 1; $offset <= 3; $offset++) {
                $nameRow = $this->rows[$i + $offset] ?? [];

                if ($pemohon === null && $pemohonCol !== null) {
                    $val = $this->str($nameRow[$pemohonCol] ?? null);
                    // Accept if it looks like a name (not another label keyword)
                    if ($val !== null && !preg_match(self::SIGNATURE_LABEL_PATTERN, $val)) {
                        $pemohon = $val;
                    }
                }

                if ($menyetujui === null && $menyetujuiCol !== null) {
                    $val = $this->str($nameRow[$menyetujuiCol] ?? null);
                    if ($val !== null && !preg_match(self::SIGNATURE_LABEL_PATTERN, $val)) {
                        $menyetujui = $val;
                    }
                }

                if ($pemohon !== null && $menyetujui !== null) {
                    break;
                }
            }

            if ($pemohon !== null || $menyetujui !== null) {
                break;
            }
        }

        return [
            'pemohon'    => $pemohon,
            'menyetujui' => $menyetujui,
        ];
    }
}
