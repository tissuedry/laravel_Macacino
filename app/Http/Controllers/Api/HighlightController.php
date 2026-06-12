<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Highlight;
use Illuminate\Http\Request;
use App\Http\Resources\HighlightResource;

class HighlightController extends Controller
{
    public function getHighlights(Request $request, $document_id)
    {
        $page = $request->query('page');
        $query = Highlight::where('document_id', $document_id);

        if ($page !== null) {
            $query->where('page_number', intval($page));
        }

        $highlights = $query->orderBy('created_at', 'asc')->get();
        return HighlightResource::collection($highlights);
    }

    public function createHighlight(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'page_number' => 'required|integer',
            'text_content' => 'required|string',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'position_width' => 'required|numeric',
            'position_height' => 'required|numeric',
            'color' => 'nullable|string'
        ]);

        $highlight = Highlight::create($validated);
        return response()->json(['data' => new HighlightResource($highlight)], 201);
    }

    public function createAiNote(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'page_number' => 'required|integer',
            'text_content' => 'required|string',
        ]);

        $aiDetailsStr = $request->input('ai_details');

        $aiGrammar = null;
        $aiVocabulary = [];
        $aiIdiomNote = null;

        if ($aiDetailsStr) {
            $details = json_decode($aiDetailsStr, true);
            $aiGrammar = $details['grammar'] ?? null;
            $aiVocabulary = $details['vocabulary'] ?? [];
            $aiIdiomNote = $details['idiom_note'] ?? null;
        }

        $aiNote = Highlight::create([
            'document_id' => $request->input('document_id'),
            'page_number' => $request->input('page_number'),
            'text_content' => $request->input('text_content'),
            'position_x' => 0.0,
            'position_y' => 0.0,
            'position_width' => 0.0,
            'position_height' => 0.0,
            'ai_explanation' => $request->input('ai_explanation'),
            'ai_translation' => $request->input('ai_translation'),
            'ai_grammar' => $aiGrammar,
            'ai_vocabulary' => $aiVocabulary,
            'ai_idiom_note' => $aiIdiomNote,
            'ai_details' => $aiDetailsStr,
            'color' => $request->input('color', 'rgba(255, 213, 79, 0.3)'),
        ]);

        return response()->json(['data' => new HighlightResource($aiNote)], 201);
    }

    public function destroy($id)
    {
        $highlight = Highlight::find($id);
        if ($highlight) {
            $highlight->delete();
        }
        return response()->json(['message' => 'Deleted'], 204);
    }
}
