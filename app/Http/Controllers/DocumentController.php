<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        try {
            $docs = Document::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();
            return response()->json(['data' => $docs, 'error' => null]);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $doc = Document::where('id', $id)->where('user_id', Auth::id())->first();
            if (!$doc) {
                return response()->json(['data' => null, 'error' => 'Document not found or access denied'], 404);
            }
            return response()->json(['data' => $doc, 'error' => null]);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'error' => $e->getMessage()], 500);
        }
    }

    public function upload(Request $request)
    {
        try {
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

            return response()->json(['data' => $newDoc, 'error' => null], 201);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateLastPage(Request $request, $id)
    {
        try {
            $page = $request->input('page', 1);
            $doc = Document::where('id', $id)->where('user_id', Auth::id())->first();
            
            if ($doc) {
                $doc->update([
                    'last_page' => $page,
                    'last_read_at' => now()
                ]);
            }
            return response()->json(['data' => ['page' => $page], 'error' => null]);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateTotalPages(Request $request, $id)
    {
        try {
            $totalPages = $request->input('total_pages', 0);
            $doc = Document::where('id', $id)->where('user_id', Auth::id())->first();

            if ($doc) {
                $doc->update(['total_pages' => $totalPages]);
            }
            return response()->json(['data' => ['total_pages' => $totalPages], 'error' => null]);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $doc = Document::where('id', $id)->where('user_id', Auth::id())->first();
            if ($doc) {
                Storage::disk('public')->delete('uploads/' . $doc->filename);
                $doc->delete();
            }
            return response()->json(['data' => 'Deleted successfully', 'error' => null]);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'error' => $e->getMessage()], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json(['error' => 'No documents selected'], 400);
            }

            $docs = Document::whereIn('id', $ids)->where('user_id', Auth::id())->get();
            foreach ($docs as $doc) {
                Storage::disk('public')->delete('uploads/' . $doc->filename);
                $doc->delete();
            }

            return response()->json(['message' => count($docs) . ' documents deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}