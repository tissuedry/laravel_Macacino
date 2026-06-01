<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    /**
     * Endpoint untuk memproses Text-to-Speech (Suara US & UK)
     * Menggunakan Google Translate TTS API
     */
    public function edgeTtsEndpoint(Request $request)
    {
        // 1. Validasi teks yang dikirim dari JavaScript
        $request->validate([
            'text' => 'required|string',
            'accent' => 'nullable|string'
        ]);

        $text = $request->input('text');
        $accent = $request->input('accent', 'american'); // Default ke aksen Amerika

        // 2. Tentukan kode bahasa
        $langCode = ($accent === 'british') ? 'en-GB' : 'en-US';

        // 3. Panggil API Text-to-Speech 
        $textEncoded = urlencode($text);
        $url = "https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&tl={$langCode}&q={$textEncoded}";

        try {
            // Ambil file audio (.mp3) dari URL
            $audioData = file_get_contents($url);

            if ($audioData === false) {
                throw new \Exception("Gagal mengambil data audio dari server.");
            }

            // 4. Kembalikan data tersebut sebagai file Audio ke Browser
            return response($audioData, 200)
                ->header('Content-Type', 'audio/mpeg')
                ->header('Cache-Control', 'no-cache');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Sistem gagal memproses suara.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint untuk Penjelasan AI (Menggunakan Groq API)
     */
    public function explainText(Request $request)
    {
        // 1. Validasi input dari frontend (Menerima kata dan kalimat konteks)
        $request->validate([
            'text' => 'required|string|max:1000',
            'context' => 'nullable|string|max:2000'
        ]);

        $text = $request->input('text');
        $context = $request->input('context'); // Menangkap kalimat utuh dari reader.js
        $apiKey = env('GROQ_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API Key Groq belum diatur di file .env.'
            ], 500);
        }

        // 2. Prompt instruksi dengan format JSON Object murni menggunakan double quotes
        $systemPrompt = "You are an expert English teacher helping an Indonesian student. Analyze the given English word or phrase BASED ON the context sentence provided. You MUST reply ONLY with a valid JSON object. Do not add markdown blocks like ```json.
        The JSON must have exactly these keys:
        {
          \"explanation\": \"Clear English explanation of the word/phrase tailored to its specific meaning in the context sentence.\",
          \"translation\": \"Accurate Indonesian translation matching its contextual meaning.\",
          \"grammar\": \"Grammar context or part of speech in Indonesian (e.g., kata kerja, kata benda).\",
          \"vocabulary\": [
            {
              \"word\": \"related word 1\",
              \"type\": \"noun/verb/adjective\",
              \"meaning\": \"Indonesian translation\",
              \"example\": \"short English example sentence\"
            },
            {
              \"word\": \"related word 2\",
              \"type\": \"noun/verb/adjective\",
              \"meaning\": \"Indonesian translation\",
              \"example\": \"short English example sentence\"
            }
          ],
          \"idiom_note\": \"Brief explanation in Indonesian if it is an idiom, otherwise leave empty string.\",
          \"tip\": \"A short, encouraging tip about using this word in Indonesian.\"
        }";

        $userPrompt = "Target Word: \"{$text}\"\nContext Sentence: \"{$context}\"";

        try {
            // 3. Panggil API Groq dengan Llama 3
            $response = Http::withoutVerifying() 
                ->withToken($apiKey)
                ->timeout(15) 
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile', 
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.2, 
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'];
                
                // Decode string JSON dari AI menjadi array PHP
                $aiData = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // DI SINI KUNCINYA: Membungkus kembali hasil ke dalam key 'data' 
                    // agar lolos kondisi pengecekan `if(result.data)` di reader.js
                    return response()->json([
                        'data' => $aiData
                    ]);
                }
                
                return response()->json(['error' => 'AI tidak mengembalikan format JSON yang valid.'], 500);
            }

            return response()->json([
                'error' => 'API Groq error',
                'details' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}