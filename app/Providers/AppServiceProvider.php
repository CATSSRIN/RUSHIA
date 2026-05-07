<?php

namespace App\Providers;

use App\Models\RansumUpload;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $adminPoList = collect();

            if (Auth::check() && Auth::user()->is_admin) {
                $formatSupplierLabel = static fn ($supplierSlug) => Str::upper(Str::replace('-', ' ', (string) $supplierSlug));

                $uploads = RansumUpload::query()
                    ->whereNotNull('po_number')
                    ->where('po_number', '!=', '')
                    ->orderByDesc('created_at')
                    ->take(20)
                    ->get(['id', 'vessel_name', 'voyage', 'no_do', 'po_number']);

                $adminPoList = $uploads
                    ->flatMap(function (RansumUpload $upload) use ($formatSupplierLabel) {
                        $rawPo = is_string($upload->po_number) ? trim($upload->po_number) : '';
                        $decodedPo = str_starts_with($rawPo, '{') ? json_decode($rawPo, true) : null;
                        $poData = (is_array($decodedPo) && json_last_error() === JSON_ERROR_NONE) ? $decodedPo : null;

                        if (is_array($poData) && count($poData) > 0) {
                            return collect($poData)->map(function ($poNumber, $supplierSlug) use ($upload, $formatSupplierLabel) {
                                return [
                                    'title' => $upload->vessel_name ?: ('Ransum #' . $upload->id),
                                    'subtitle' => trim(collect([
                                        $upload->no_do ? ('DO: ' . $upload->no_do) : null,
                                        $formatSupplierLabel($supplierSlug),
                                    ])->filter()->implode(' • ')),
                                    'po_number' => (string) $poNumber,
                                    'url' => route('admin.ransum.po.preview', $upload->id),
                                ];
                            });
                        }

                        return [[
                            'title' => $upload->vessel_name ?: ('Ransum #' . $upload->id),
                            'subtitle' => trim(collect([
                                $upload->no_do ? ('DO: ' . $upload->no_do) : null,
                                $upload->voyage,
                            ])->filter()->implode(' • ')),
                            'po_number' => $rawPo,
                            'url' => route('admin.ransum.po.preview', $upload->id),
                        ]];
                    })
                    ->filter(fn (array $item) => $item['po_number'] !== '')
                    ->take(10)
                    ->values();
            }

            $view->with('adminPoList', $adminPoList);
        });
    }
}
