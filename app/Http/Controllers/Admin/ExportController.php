<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf as DompdfWriter;

class ExportController extends Controller
{
    protected string $uploadDir;
    protected string $pdfDir;

    public function __construct()
    {
        $this->uploadDir = public_path('uploaded_file');
        $this->pdfDir = public_path('uploaded_file/pdf');
    }

    public function index()
    {
        $files = collect(glob($this->uploadDir . '/*.{xls,xlsx,csv}', GLOB_BRACE))
            ->map(function ($path) {
                $size = @filesize($path);
                $mtime = @filemtime($path);
                return [
                    'name' => basename($path),
                    'size' => $size !== false ? $size : 0,
                    'uploaded_at' => $mtime !== false ? $mtime : 0,
                ];
            })
            ->sortByDesc('uploaded_at')
            ->values();

        return view('admin.export.index', compact('files'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xls,xlsx,csv', 'max:10240'],
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $uniqueName = uniqid('', true) . '_' . $safeName . '.' . $extension;

        $file->move($this->uploadDir, $uniqueName);

        return redirect()->route('admin.export.index')->with('success', 'File "' . basename($file->getClientOriginalName()) . '" uploaded successfully.');
    }

    public function preview(string $filename)
    {
        $safeName = basename($filename);
        $filePath = $this->uploadDir . '/' . $safeName;

        // Resolve the real path and confirm it stays inside the upload directory
        $realPath = realpath($filePath);
        $realUploadDir = realpath($this->uploadDir);

        if ($realPath === false || $realUploadDir === false || !str_starts_with($realPath, $realUploadDir . DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        if (!preg_match('/\.(xls|xlsx|csv)$/i', $safeName)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            $rows = [];
            $truncated = false;
            if (($handle = fopen($realPath, 'r')) !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $rows[] = $row;
                    // Limit to 1 header + 1 000 data rows to prevent memory exhaustion
                    if (count($rows) >= 1001) {
                        $truncated = true;
                        break;
                    }
                }
                fclose($handle);
            }
            return view('admin.export.preview', compact('safeName', 'rows', 'extension', 'truncated'));
        }

        // XLS / XLSX — convert to PDF on-demand and show via an iframe.
        $rows = [];
        $truncated = false;
        $pdfUrl = null;

        $pdfName = $safeName . '.pdf';
        $pdfPath = $this->pdfDir . '/' . $pdfName;

        if (!file_exists($pdfPath)) {
            if (!is_dir($this->pdfDir)) {
                mkdir($this->pdfDir, 0755, true);
            }
            try {
                $spreadsheet = IOFactory::load($realPath);
                IOFactory::registerWriter('Pdf', DompdfWriter::class);
                $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
                $writer->setSheetIndex(0);
                $writer->save($pdfPath);
            } catch (\Throwable $e) {
                // PDF generation failed; $pdfUrl remains null
            }
        }

        if (file_exists($pdfPath)) {
            $pdfUrl = asset('uploaded_file/pdf/' . $pdfName);
        }

        return view('admin.export.preview', compact('safeName', 'rows', 'extension', 'truncated', 'pdfUrl'));
    }

    public function renderHtml(string $filename): \Illuminate\Http\Response
    {
        $safeName = basename($filename);
        $filePath = $this->uploadDir . '/' . $safeName;

        $realPath = realpath($filePath);
        $realUploadDir = realpath($this->uploadDir);

        if ($realPath === false || $realUploadDir === false || !str_starts_with($realPath, $realUploadDir . DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        if (!preg_match('/\.(xls|xlsx)$/i', $safeName)) {
            abort(404);
        }

        try {
            $spreadsheet = IOFactory::load($realPath);
            $writer = new HtmlWriter($spreadsheet);
            $writer->setSheetIndex(0);
            $writer->setEmbedImages(false);
            $html = $writer->generateHtmlAll();
        } catch (\Throwable $e) {
            $html = '<html><body><p>Could not render file.</p></body></html>';
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function download(string $filename)
    {
        $safeName = basename($filename);
        $filePath = $this->uploadDir . '/' . $safeName;

        if (!file_exists($filePath) || !preg_match('/\.(xls|xlsx|csv)$/i', $safeName)) {
            abort(404);
        }

        return response()->download($filePath, $safeName);
    }

    public function destroy(string $filename)
    {
        $safeName = basename($filename);
        $filePath = $this->uploadDir . '/' . $safeName;

        if (file_exists($filePath) && preg_match('/\.(xls|xlsx|csv)$/i', $safeName)) {
            if (!unlink($filePath)) {
                return redirect()->route('admin.export.index')->with('error', 'Failed to delete the file.');
            }
        }

        return redirect()->route('admin.export.index')->with('success', 'File deleted.');
    }
}
