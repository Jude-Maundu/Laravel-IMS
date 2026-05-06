<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\RepairsController;
use App\Http\Controllers\ChecklistsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public item lookup
Route::get('/item/{unique_code}', [App\Http\Controllers\ItemLookupController::class, 'show'])->name('item.lookup');

// Customer Portal Routes
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/book', [App\Http\Controllers\CustomerPortalController::class, 'showBookingForm'])->name('book');
    Route::post('/book', [App\Http\Controllers\CustomerPortalController::class, 'submitBookingRequest'])->name('book.post');
    Route::get('/login', [App\Http\Controllers\CustomerPortalController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\CustomerPortalController::class, 'login'])->name('login.post');
    Route::get('/logout', [App\Http\Controllers\CustomerPortalController::class, 'logout'])->name('logout');

    Route::middleware(['customer.auth'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\CustomerPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/event/{event}', [App\Http\Controllers\CustomerPortalController::class, 'showEvent'])->name('event.show');
        Route::post('/pay/{event}', [App\Http\Controllers\CustomerPortalController::class, 'initiatePayment'])->name('pay');
        Route::get('/receipt/{event}', [App\Http\Controllers\CustomerPortalController::class, 'downloadReceipt'])->name('receipt');
        Route::get('/check-status/{event}', [App\Http\Controllers\CustomerPortalController::class, 'checkPaymentStatus'])->name('check-status');
    });
});

// M-Pesa Callback (Exempt from CSRF in VerifyCsrfToken middleware if it exists, or handled in bootstrap/app.php in Laravel 11+)
Route::post('/api/mpesa/callback', [App\Http\Controllers\MpesaController::class, 'callback'])->name('mpesa.callback');

// Scan Session Routes (handle their own auth via middleware)
Route::middleware(['validate.scan'])->group(function () {
    Route::get('/scan/{token}', [App\Http\Controllers\ScanController::class, 'show'])->name('scan.show');
    Route::post('/scan/{token}/process', [App\Http\Controllers\ScanController::class, 'process'])->name('scan.process');
    Route::post('/scan/{token}/submit', [App\Http\Controllers\ScanController::class, 'submit'])->name('scan.submit');
    Route::post('/scan/{token}/save-progress', [App\Http\Controllers\ScanController::class, 'saveProgress'])->name('scan.save-progress');
    Route::get('/scan/{token}/complete', [App\Http\Controllers\ScanController::class, 'complete'])->name('scan.complete');
});

// Receive Session Routes (handle their own auth via middleware)
Route::middleware(['validate.receive'])->group(function () {
    Route::get('/receive/{token}', [App\Http\Controllers\ReceiveController::class, 'show'])->name('receive.show');
    Route::post('/receive/{token}/process', [App\Http\Controllers\ReceiveController::class, 'process'])->name('receive.process');
    Route::post('/receive/{token}/submit', [App\Http\Controllers\ReceiveController::class, 'submit'])->name('receive.submit');
    Route::post('/receive/{token}/save-progress', [App\Http\Controllers\ReceiveController::class, 'saveProgress'])->name('receive.save-progress');
    Route::post('/receive/{token}/mark-missing', [App\Http\Controllers\ReceiveController::class, 'markMissing'])->name('receive.mark-missing');
    Route::get('/receive/{token}/complete', [App\Http\Controllers\ReceiveController::class, 'complete'])->name('receive.complete');
});

// Health Check for Render/Railway
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'status' => 'OK',
            'timestamp' => now()->toIso8601String(),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Error',
            'message' => 'Database connection failed',
            'timestamp' => now()->toIso8601String(),
        ], 500);
    }
});

// Debug route to check dispatch records for an event
Route::get('/debug/event/{event}/dispatches', function ($event) {
    $dispatches = \App\Models\EventPieceDispatch::where('event_id', $event)
        ->with('itemPiece.item')
        ->get()
        ->map(function($d) use ($event) {
            return [
                'id' => $d->id,
                'piece_code' => $d->itemPiece?->unique_code,
                'piece_id' => $d->item_piece_id,
                'item_name' => $d->itemPiece?->item?->name,
                'piece_status' => $d->itemPiece?->status,
                'piece_event_id' => $d->itemPiece?->current_event_id,
                'dispatched_at' => $d->dispatched_at?->format('Y-m-d H:i:s'),
                'returned_at' => $d->returned_at?->format('Y-m-d H:i:s'),
                'can_receive' => $d->dispatched_at && !$d->returned_at && $d->itemPiece?->status === 'Assigned' && $d->itemPiece?->current_event_id == $event,
            ];
        });

    return response()->json([
        'event_id' => $event,
        'total_dispatches' => $dispatches->count(),
        'dispatches' => $dispatches,
    ]);
})->middleware('auth');

// Debug route to check a specific piece
Route::get('/debug/piece/{code}', function ($code) {
    $piece = \App\Models\ItemPiece::where('unique_code', strtoupper($code))->first();

    if (!$piece) {
        return response()->json(['error' => 'Piece not found']);
    }

    $dispatches = \App\Models\EventPieceDispatch::where('item_piece_id', $piece->id)
        ->with('event')
        ->get()
        ->map(function($d) {
            return [
                'event_id' => $d->event_id,
                'event_name' => $d->event->name,
                'dispatched_at' => $d->dispatched_at?->format('Y-m-d H:i:s'),
                'returned_at' => $d->returned_at?->format('Y-m-d H:i:s'),
            ];
        });

    return response()->json([
        'unique_code' => $piece->unique_code,
        'item_name' => $piece->item->name,
        'status' => $piece->status,
        'current_event_id' => $piece->current_event_id,
        'dispatch_history' => $dispatches,
    ]);
})->middleware('auth');

// Test receive scan endpoint
Route::get('/debug/test-receive/{event}/{code}', function ($eventId, $code) {
    $session = \App\Models\ReceiveSession::where('event_id', $eventId)
        ->where('status', 'active')
        ->first();

    if (!$session) {
        return response()->json(['error' => 'No active receive session for this event']);
    }

    // Simulate the receive process
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'unique_code' => $code,
        'condition_score' => null,
        'destination' => null,
    ]);

    $controller = new \App\Http\Controllers\ReceiveController();

    try {
        $response = $controller->process($request, $session->session_token);
        return $response;
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
})->middleware('auth');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Role switching route
// Route::get('/switch-role/{role}', function ($role) {
//     session(['current_user_role' => $role]);
//     return redirect()->back();
// })->name('switch-role');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/available', [InventoryController::class, 'available'])->name('inventory.available');
    Route::get('/inventory/pieces', [InventoryController::class, 'pieces'])->name('inventory.pieces');
    Route::post('/inventory/pieces/bulk-update', [InventoryController::class, 'bulkUpdatePieces'])->name('inventory.pieces.bulkUpdate');
    Route::get('/inventory/pieces/{piece}/label', [App\Http\Controllers\LabelController::class, 'single'])->name('labels.single');
    Route::get('/inventory/{item}/labels', [App\Http\Controllers\LabelController::class, 'byItem'])->name('labels.byItem');
    Route::get('/inventory/labels/category/{category}', [App\Http\Controllers\LabelController::class, 'byCategory'])->name('labels.byCategory');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->middleware('check.role:create inventory')->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->middleware('check.role:create inventory')->name('inventory.store');
    Route::post('/inventory/categories', [InventoryController::class, 'storeCategory'])->middleware('check.role:create inventory')->name('inventory.category.store');
    Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->middleware('check.role:edit inventory')->name('inventory.edit');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->middleware('check.role:edit inventory')->name('inventory.update');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->middleware('check.role:delete inventory')->name('inventory.destroy');
    Route::post('/inventory/{id}/change-status', [InventoryController::class, 'changeStatus'])->middleware('check.role:edit inventory')->name('inventory.changeStatus');
    Route::post('/inventory/{id}/assign', [InventoryController::class, 'assign'])->middleware('check.role:assign items')->name('inventory.assign');
    Route::post('/inventory/{id}/return', [InventoryController::class, 'returnItem'])->middleware('check.role:return items')->name('inventory.return');
    Route::post('/inventory/{item}/images', [InventoryController::class, 'uploadImage'])->name('inventory.image.upload');
    Route::post('/inventory/{item}/images/{image}/primary', [InventoryController::class, 'setPrimaryImage'])->name('inventory.image.primary');
    Route::delete('/inventory/{item}/images/{image}', [InventoryController::class, 'deleteImage'])->name('inventory.image.delete');

    // API Endpoints
    Route::get('/api/items/{item}/availability', [InventoryController::class, 'availability'])->name('api.items.availability');
    Route::get('/api/pieces/validate', [App\Http\Controllers\EventController::class, 'validatePiece'])->name('api.pieces.validate');
    Route::get('/api/events/{event}/scan-session/{scanSession}/progress', [App\Http\Controllers\EventController::class, 'scanProgress'])->name('api.scan.progress');
    Route::get('/api/events/{event}/scan-session/{scanSession}/stream', [App\Http\Controllers\EventController::class, 'scanProgressStream'])->name('api.scan.stream');
    Route::get('/api/pieces/{piece}/qr', [InventoryController::class, 'pieceQR'])->name('api.pieces.qr');
    Route::get('/api/items/{item}/pieces-qr', [InventoryController::class, 'itemPiecesQR'])->name('api.items.pieces-qr');
    Route::get('/api/items/{item}/qr', [InventoryController::class, 'itemQR'])->name('api.items.qr');

    // Assignments
    Route::resource('assignments', AssignmentsController::class)->middleware('check.role:assign items');
    Route::post('/assignments/{id}/return', [AssignmentsController::class, 'return'])->middleware('check.role:return items')->name('assignments.return');
    
    // Repairs
    Route::resource('repairs', RepairsController::class)->middleware('check.role:view inventory');
    
    // Cleaning
    Route::get('/cleaning', [App\Http\Controllers\CleaningController::class, 'index'])->name('cleaning.index');
    Route::post('/cleaning/bulk-complete', [App\Http\Controllers\CleaningController::class, 'bulkComplete'])->name('cleaning.bulkComplete');
    Route::post('/cleaning/{item}/complete', [App\Http\Controllers\CleaningController::class, 'complete'])->name('cleaning.complete');

    // Categories
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Checklist
    Route::resource('checklist', ChecklistsController::class)->except(['show', 'edit', 'update'])->middleware('check.role:assign items');
    
    // Events
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/',           [App\Http\Controllers\EventController::class, 'index'])  ->name('index');
        Route::get('/create',     [App\Http\Controllers\EventController::class, 'create']) ->name('create');
        Route::get('/requests',   [App\Http\Controllers\EventController::class, 'requests'])->name('requests');
        Route::post('/',          [App\Http\Controllers\EventController::class, 'store'])  ->name('store');
        Route::get('/{event}',    [App\Http\Controllers\EventController::class, 'show'])   ->name('show');
        Route::get('/{event}/edit',   [App\Http\Controllers\EventController::class, 'edit'])   ->name('edit');
        Route::put('/{event}',        [App\Http\Controllers\EventController::class, 'update']) ->name('update');
        Route::delete('/{event}',     [App\Http\Controllers\EventController::class, 'destroy'])->name('destroy');

        Route::get('/{event}/checklist',       [App\Http\Controllers\EventController::class, 'checklist'])      ->name('checklist');
        Route::post('/{event}/checklist',      [App\Http\Controllers\EventController::class, 'saveChecklist'])  ->name('checklist.save');
        Route::get('/{event}/team',            [App\Http\Controllers\EventController::class, 'team'])           ->name('team');
        Route::get('/{event}/team/search',     [App\Http\Controllers\EventController::class, 'teamSearch'])     ->name('team.search');
        Route::post('/{event}/team',           [App\Http\Controllers\EventController::class, 'teamStore'])      ->name('team.store');
        Route::get('/{event}/review',          [App\Http\Controllers\EventController::class, 'review'])         ->name('review');
        Route::post('/{event}/confirm',        [App\Http\Controllers\EventController::class, 'confirm'])        ->name('confirm');
        Route::get('/{event}/packing-list/planning', [App\Http\Controllers\EventController::class, 'planningPackingList'])->name('packing-list.planning');
        Route::get('/{event}/dispatch-note',   [App\Http\Controllers\EventController::class, 'dispatchNote'])   ->name('dispatch-note');

        // Dispatch modal and scan session
        Route::post('/{event}/scan-session/start', [App\Http\Controllers\EventController::class, 'scanStart'])->name('scan.start');
        Route::get('/{event}/scan-session/{scanSession}', [App\Http\Controllers\EventController::class, 'scanMonitor'])->name('scan.monitor');
        Route::post('/{event}/scan-session/{scanSession}/extend', [App\Http\Controllers\EventController::class, 'scanExtend'])->name('scan.extend');
        Route::post('/{event}/scan-session/{scanSession}/cancel', [App\Http\Controllers\EventController::class, 'scanCancel'])->name('scan.cancel');
        Route::post('/{event}/scan-session/{scanSession}/undo', [App\Http\Controllers\EventController::class, 'scanUndo'])->name('scan.undo');
        Route::post('/{event}/scan-session/{scanSession}/confirm-dispatch', [App\Http\Controllers\EventController::class, 'scanConfirmDispatch'])->name('scan.confirm-dispatch');

        // Manual dispatch
        Route::get('/{event}/dispatch/manual', [App\Http\Controllers\EventController::class, 'manualDispatch'])->name('dispatch.manual');
        Route::post('/{event}/dispatch/manual', [App\Http\Controllers\EventController::class, 'manualDispatchStore'])->name('dispatch.manual.store');

        // Additional dispatch
        Route::post('/{event}/dispatch/additional', [App\Http\Controllers\EventController::class, 'dispatchAdditionalStore'])->name('dispatch.additional.store');

        // Dispatch packing list PDF with QR
        Route::get('/{event}/packing-list/dispatch/{scanSession}', [App\Http\Controllers\EventController::class, 'dispatchPackingList'])->name('packing-list.dispatch');

        // Receive session routes
        Route::post('/{event}/receive-session/start', [App\Http\Controllers\EventController::class, 'receiveStart'])->name('receive.start');
        Route::get('/{event}/receive-session/{receiveSession}', [App\Http\Controllers\EventController::class, 'receiveMonitor'])->name('receive.monitor');
        Route::post('/{event}/receive-session/{receiveSession}/extend', [App\Http\Controllers\EventController::class, 'receiveExtend'])->name('receive.extend');
        Route::post('/{event}/receive-session/{receiveSession}/cancel', [App\Http\Controllers\EventController::class, 'receiveCancel'])->name('receive.cancel');
        Route::post('/{event}/receive-session/{receiveSession}/confirm', [App\Http\Controllers\EventController::class, 'receiveConfirm'])->name('receive.confirm');

        // Manual receive fallback
        Route::get('/{event}/receive/manual', [App\Http\Controllers\EventController::class, 'manualReceive'])->name('receive.manual');
        Route::post('/{event}/receive/manual', [App\Http\Controllers\EventController::class, 'manualReceiveStore'])->name('receive.manual.store');

        // Receipt note PDF
        Route::get('/{event}/receipt-note/{receiveSession}', [App\Http\Controllers\EventController::class, 'receiptNote'])->name('receipt-note');

        // Receiving report PDF
        Route::get('/{event}/receiving-report', [App\Http\Controllers\EventController::class, 'receivingReport'])->name('receiving-report');

        // Receive progress API for laptop monitor polling
        Route::get('/{event}/receive-session/{receiveSession}/progress', [App\Http\Controllers\EventController::class, 'receiveProgress'])->name('receive.progress');

        // Piece validation for manual receive
        Route::get('/{event}/validate-piece-return', [App\Http\Controllers\EventController::class, 'validatePieceReturn'])->name('validate-piece-return');

        // Missing items resolution
        Route::patch('/{event}/missing/{missing}/resolve', [App\Http\Controllers\EventController::class, 'resolveMissing'])->name('missing.resolve');

        // Site-to-Site Linking
        Route::get('/{event}/site-to-site',    [App\Http\Controllers\EventController::class, 'siteToSiteWizard'])->name('site-to-site.wizard');
        Route::post('/{event}/site-to-site',   [App\Http\Controllers\EventController::class, 'createSiteToSite'])->name('site-to-site.create');
    });

    // Activity Log (placeholder — module not yet built)
    Route::get('/activity', fn() => view('coming-soon', ['module' => 'Activity Log']))->name('activity.index');

    // Administration
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware('check.role:manage users');
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->middleware('check.role:manage users')->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->middleware('check.role:manage users')->name('settings.update');
    Route::post('/settings/clear-cache', [\App\Http\Controllers\SettingsController::class, 'clearCache'])->middleware('check.role:manage users')->name('settings.clear-cache');
    Route::post('/settings/clear-data', [\App\Http\Controllers\SettingsController::class, 'clearDatabase'])->middleware('check.role:manage users')->name('settings.clear-data');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->middleware('check.role:view reports')->name('reports.index');
    Route::get('/reports/inventory', [ReportsController::class, 'inventoryReport'])->middleware('check.role:generate reports')->name('reports.inventory');
    Route::get('/reports/inventory/pdf', [ReportsController::class, 'inventoryReportPdf'])->middleware('check.role:generate reports')->name('reports.inventory.pdf');
    Route::get('/reports/assignments', [ReportsController::class, 'assignmentsReport'])->middleware('check.role:generate reports')->name('reports.assignments');
    Route::get('/reports/assignments/pdf', [ReportsController::class, 'assignmentsReportPdf'])->middleware('check.role:generate reports')->name('reports.assignments.pdf');
    Route::get('/reports/repairs', [ReportsController::class, 'repairsReport'])->middleware('check.role:generate reports')->name('reports.repairs');
    Route::get('/reports/repairs/pdf', [ReportsController::class, 'repairsReportPdf'])->middleware('check.role:generate reports')->name('reports.repairs.pdf');
    Route::get('/reports/activity', [ReportsController::class, 'activityReport'])->middleware('check.role:generate reports')->name('reports.activity');
    Route::get('/reports/activity/pdf', [ReportsController::class, 'activityReportPdf'])->middleware('check.role:generate reports')->name('reports.activity.pdf');
    Route::get('/reports/cleaning/pdf', [ReportsController::class, 'cleaningReportPdf'])->middleware('check.role:generate reports')->name('reports.cleaning.pdf');
    
    // New Targeted Reports
    Route::get('/reports/item/{item}/pdf', [ReportsController::class, 'itemReportPdf'])->middleware('check.role:generate reports')->name('reports.item.pdf');
    Route::get('/reports/event/{event}/{type}/pdf', [ReportsController::class, 'eventReportPdf'])->middleware('check.role:generate reports')->name('reports.event.pdf');
    Route::get('/reports/repair/{repair}/pdf', [ReportsController::class, 'singleRepairReportPdf'])->middleware('check.role:generate reports')->name('reports.repair.pdf');
});
