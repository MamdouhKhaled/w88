<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookmarkController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
//    Route::apiResource('bookmarks', BookmarkController::class);
    Route::post('bookmarks', [BookmarkController::class, 'store'])->middleware('can:create,App\Models\Bookmark');
    Route::get('bookmarks', [BookmarkController::class, 'index'])->middleware('can:viewAny,App\Models\Bookmark');
    Route::get('bookmarks/{bookmark}', [BookmarkController::class, 'show'])->middleware('can:view,bookmark');
    Route::delete('bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->middleware('can:delete,bookmark');
    Route::put('bookmarks/{bookmark}/share', [BookmarkController::class, 'share_store'])->middleware('can:update,bookmark');
    Route::put('bookmarks/{bookmark}', [BookmarkController::class, 'update'])->middleware('can:update,bookmark');
});

Route::get('bookmarks/{bookmark:share_token}/share', [BookmarkController::class, 'share']);
