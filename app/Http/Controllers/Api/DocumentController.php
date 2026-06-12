<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Resources\DocumentResource;

class DocumentController extends Controller
{
    public function index()
    {
        $docs = Document::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return DocumentResource::collection($docs);
    }

    public function show($id)
    {
        $doc = Document::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$doc) {
            return response()->json(['message' => 'Document not found or access denied'], 404);
        }
        return new DocumentResource($doc);
    }

    public function download($id)
    {
        $doc = Document::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$doc) {
            return response()->json(['message' => 'Document not found or access denied'], 404);
        }

        $path = 'uploads/' . $doc->filename;
        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'File not found on storage'], 404);
        }

        $file = Storage::disk('public')->path($path);

        return response()->file($file, [
            'Content-Type' => 'application/pdf',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
            'Content-Disposition' => 'inline; filename="' . $doc->title . '"'
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:51200',
            'title' => 'nullable|string|max:255'
        ]);

        $file = $request->file('file');
        $title = $request->input('title') ?? $file->getClientOriginalName();

        $fileId = (string) Str::uuid();
        $safeName = $fileId . '_' . str_replace(' ', '_', $file->getClientOriginalName());

        $file->storeAs('uploads', $safeName, 'public');

        $newDoc = Document::create([
            'user_id' => Auth::id(),
            'title' => $title,
            'filename' => $safeName,
            'last_page' => 1,
            'total_pages' => 0
        ]);

        return response()->json(['data' => new DocumentResource($newDoc)], 201);
    }

    public function updateLastPage(Request $request, $id)
    {
        $page = $request->input('page', 1);
        $doc = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $doc->update([
            'last_page' => $page,
            'last_read_at' => now()
        ]);

        return response()->json(['message' => 'Progress updated', 'last_page' => $page]);
    }

    public function updateTotalPages(Request $request, $id)
    {
        $totalPages = $request->input('total_pages', 0);
        $doc = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $doc->update(['total_pages' => $totalPages]);

        return response()->json(['message' => 'Total pages updated', 'total_pages' => $totalPages]);
    }

    public function destroy($id)
    {
        $doc = Document::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        Storage::disk('public')->delete('uploads/' . $doc->filename);
        $doc->delete();

        return response()->json(['message' => 'Deleted successfully'], 204);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No documents selected'], 400);
        }

        $docs = Document::whereIn('id', $ids)->where('user_id', Auth::id())->get();
        foreach ($docs as $doc) {
            Storage::disk('public')->delete('uploads/' . $doc->filename);
            $doc->delete();
        }

        return response()->json(['message' => count($docs) . ' documents deleted successfully']);
    }
}
