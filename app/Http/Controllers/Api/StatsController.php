<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Highlight;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $docs = Document::where('user_id', $userId)->get();

        $streak_days = $docs->count();
        $finished_books = $docs->where('last_page', '>=', 'total_pages')->where('total_pages', '>', 0)->count();
        $total_words = Highlight::whereHas('document', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $doc_titles = $docs->pluck('title')->toArray();
        $doc_progress = $docs->map(function ($doc) {
            return $doc->total_pages > 0 ? round(($doc->last_page / $doc->total_pages) * 100) : 0;
        })->toArray();

        $vocab_titles = ['Vocabulary', 'Grammar', 'Idioms'];
        $vocab_counts = [$total_words, ceil($total_words / 3), ceil($total_words / 5)];

        return response()->json([
            'data' => [
                'streak_days' => $streak_days,
                'finished_books' => $finished_books,
                'total_words' => $total_words,
                'documents' => [
                    'titles' => $doc_titles,
                    'progress' => $doc_progress,
                ],
                'vocabulary_stats' => [
                    'titles' => $vocab_titles,
                    'counts' => $vocab_counts,
                ]
            ]
        ]);
    }
}
