<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UserShipController as AdminUserShipController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\WarehouseMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/dashboard', function () {
    if (auth()->user()->is_admin) {
        return redirect()->route('admin.orders.index');
    }
    if (auth()->user()->is_warehouse) {
        return redirect()->route('warehouse.index');
    }
    return redirect()->route('ships.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ships
    Route::resource('ships', ShipController::class)->except(['show']);

    // Orders
    Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('orders/{order}/pickup', [OrderController::class, 'updatePickup'])->name('orders.pickup');
});

// Warehouse routes
Route::middleware(['auth', WarehouseMiddleware::class])->name('warehouse.')->group(function () {
    Route::get('warehouse', [WarehouseController::class, 'index'])->name('index');
    Route::post('warehouse/{order}/receipts', [WarehouseController::class, 'storeReceipt'])->name('receipts.store');
});

// Admin routes
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    // Export / File Upload
    Route::get('export', [ExportController::class, 'index'])->name('export.index');
    Route::post('export/upload', [ExportController::class, 'upload'])->name('export.upload');
    Route::get('export/preview/{filename}', [ExportController::class, 'preview'])->name('export.preview');
    Route::get('export/download/{filename}', [ExportController::class, 'download'])->name('export.download');
    Route::delete('export/{filename}', [ExportController::class, 'destroy'])->name('export.destroy');

    Route::resource('vendors', VendorController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status/{status}', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice');

    // Users
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('admins', [AdminUserController::class, 'indexAdmins'])->name('admins.index');
    Route::get('admins/create', [AdminUserController::class, 'createAdmin'])->name('admins.create');
    Route::post('admins', [AdminUserController::class, 'storeAdmin'])->name('admins.store');
    Route::get('admins/{user}', [AdminUserController::class, 'showAdmin'])->name('admins.show');
    Route::delete('admins/{user}', [AdminUserController::class, 'destroyAdmin'])->name('admins.destroy');
    Route::get('warehouses', [AdminUserController::class, 'indexWarehouses'])->name('warehouses.index');
    Route::get('warehouses/create', [AdminUserController::class, 'createWarehouse'])->name('warehouses.create');
    Route::post('warehouses', [AdminUserController::class, 'storeWarehouse'])->name('warehouses.store');
    Route::delete('warehouses/{user}', [AdminUserController::class, 'destroyWarehouse'])->name('warehouses.destroy');
    Route::get('users/{user}/ships/create', [AdminUserShipController::class, 'create'])->name('users.ships.create');
    Route::post('users/{user}/ships', [AdminUserShipController::class, 'store'])->name('users.ships.store');
});

require __DIR__.'/auth.php';
