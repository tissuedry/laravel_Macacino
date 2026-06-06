<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HighlightController;
use App\Http\Controllers\AIController;
use App\Models\Highlight;
use App\Models\Document;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

Route::middleware('auth')->group(function () {


    Route::get('/', function () {
        return view('index');
    })->name('dashboard');


    Route::get('/reader/{id}', function ($id) {
        return view('reader', ['document_id' => $id]);
    });


    Route::get('/notes', function () {
        $highlights = Highlight::whereHas('document', function ($q) {
            $q->where('user_id', Auth::id());
        })->with('document')->orderBy('created_at', 'desc')->get();

        $grouped_notes = $highlights->groupBy(function ($item) {
            return $item->document->title;
        });

        return view('notes', compact('grouped_notes'));
    });


    Route::get('/stats', function () {
        $userId = Auth::id();
        $docs = Document::where('user_id', $userId)->get();

        $streak_days = $docs->count();
        $finished_books = $docs->where('last_page', '>=', 'total_pages')->where('total_pages', '>', 0)->count();
        $total_words = Highlight::whereHas('document', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $doc_titles = json_encode($docs->pluck('title')->toArray());
        $doc_progress = json_encode($docs->map(function ($doc) {
            return $doc->total_pages > 0 ? round(($doc->last_page / $doc->total_pages) * 100) : 0;
        })->toArray());


        $vocab_titles = json_encode(['Vocabulary', 'Grammar', 'Idioms']);
        $vocab_counts = json_encode([$total_words, ceil($total_words / 3), ceil($total_words / 5)]);

        return view('stats', compact('streak_days', 'total_words', 'finished_books', 'doc_titles', 'doc_progress', 'vocab_titles', 'vocab_counts'));
    });


    Route::get('/profile', function () {
        return view('profile');
    })->name('profile.edit');

});


Route::middleware('auth')->prefix('api')->group(function () {


    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/{id}', [DocumentController::class, 'show']);
    Route::post('/documents/upload', [DocumentController::class, 'upload']);
    Route::put('/documents/{id}/last-page', [DocumentController::class, 'updateLastPage']);
    Route::put('/documents/{id}/total-pages', [DocumentController::class, 'updateTotalPages']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    Route::post('/documents/bulk-delete', [DocumentController::class, 'bulkDestroy']);


    Route::get('/highlights/document/{id}', [HighlightController::class, 'getHighlights']);
    Route::post('/highlights', [HighlightController::class, 'createHighlight']);
    Route::post('/highlights/ai-note', [HighlightController::class, 'createAiNote']);
    Route::get('/highlights/{id}', [HighlightController::class, 'show']);
    Route::delete('/highlights/{id}', [HighlightController::class, 'destroy']);


    Route::post('/ai/explain', [AIController::class, 'explainText']);
    Route::post('/ai/tts', [AIController::class, 'edgeTtsEndpoint']);


    Route::post('/profile/update', function (Request $request) {
        $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['data' => 'Password updated', 'error' => null]);
    });

});


require __DIR__ . '/auth.php';