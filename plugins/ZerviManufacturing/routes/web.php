<?php

use Illuminate\Support\Facades\Route;
use Zervi\Manufacturing\Http\Controllers\KanbanController;

Route::middleware(['auth'])->prefix('manufacturing')->group(function () {
    // Kanban board operations
    Route::post('/kanban/move', [KanbanController::class, 'moveWorkOrder'])->name('manufacturing.kanban.move');
    Route::post('/kanban/start-task', [KanbanController::class, 'startTask'])->name('manufacturing.kanban.start-task');
    Route::post('/kanban/complete-task', [KanbanController::class, 'completeTask'])->name('manufacturing.kanban.complete-task');
    Route::post('/kanban/block-task', [KanbanController::class, 'blockTask'])->name('manufacturing.kanban.block-task');
    Route::post('/kanban/unblock-task', [KanbanController::class, 'unblockTask'])->name('manufacturing.kanban.unblock-task');
    
    // Issue reporting
    Route::post('/report-shortage', [KanbanController::class, 'reportShortage'])->name('manufacturing.report-shortage');
    Route::post('/report-qc-fail', [KanbanController::class, 'reportQcFail'])->name('manufacturing.report-qc-fail');
    
    // Dashboard widgets
    Route::get('/dashboard/data', [KanbanController::class, 'getDashboardData'])->name('manufacturing.dashboard.data');
});