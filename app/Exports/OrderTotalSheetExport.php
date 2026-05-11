<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderTotalSheetExport implements FromArray, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    public function __construct(private readonly Order $order)
    {
    }

    public function array(): array
    {
        $order = $this->order->loadMissing(['user', 'ship', 'items.product.vendor']);

        $rows = [
            ['Lembar Total Harga'],
            ['Order ID', $order->id],
            ['Tanggal', $order->created_at?->format('d-m-Y H:i')],
            ['Perusahaan', $order->user->company_name ?? $order->user->name],
            ['Kapal', $order->ship->name],
            ['Status', ucfirst($order->status)],
            [],
            ['No', 'Produk', 'Vendor', 'Satuan', 'Qty', 'Harga Satuan', 'Subtotal'],
        ];

        foreach ($order->items as $index => $item) {
            $rows[] = [
                $index + 1,
                $item->product?->name,
                $item->product?->vendor?->name,
                $item->product?->unit ?? '-',
                (float) $item->quantity,
                (float) $item->unit_price,
                (float) $item->subtotal,
            ];
        }

        $rows[] = [];
        $rows[] = ['', '', '', '', '', 'Total', (float) $order->total_price];

        return $rows;
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $itemHeaderRow = 8;
        $totalRow = $itemHeaderRow + $this->order->items->count() + 2;

        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
            ],
            $itemHeaderRow => [
                'font' => ['bold' => true],
            ],
            $totalRow => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
