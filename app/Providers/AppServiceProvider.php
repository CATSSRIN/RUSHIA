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
                $uploads = RansumUpload::query()
                    ->whereNotNull('po_number')
                    ->where('po_number', '!=', '')
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get(['id', 'vessel_name', 'voyage', 'no_do', 'po_number']);

                $adminPoList = $uploads
                    ->flatMap(function (RansumUpload $upload) {
                        $rawPo = is_string($upload->po_number) ? trim($upload->po_number) : '';
                        $poData = str_starts_with($rawPo, '{') ? json_decode($rawPo, true) : null;

                        if (is_array($poData) && count($poData) > 0) {
                            return collect($poData)->map(function ($poNumber, $supplierSlug) use ($upload) {
                                return [
                                    'title' => $upload->vessel_name ?: ('Ransum #' . $upload->id),
                                    'subtitle' => trim(collect([
                                        $upload->no_do ? ('DO: ' . $upload->no_do) : null,
                                        strtoupper(str_replace('-', ' ', (string) $supplierSlug)),
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
