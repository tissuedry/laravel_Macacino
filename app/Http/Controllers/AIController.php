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
        $context = $request->input('context', 'Tidak ada konteks spesifik.'); // Fallback jika context kosong
        $apiKey = env('GROQ_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API Key Groq belum diatur di file .env.'
            ], 500);
        }

        // 2. Prompt instruksi: Penegasan HANYA menerjemahkan kata yang disorot
$systemPrompt = "You are an expert English teacher helping an Indonesian student. 
                The user will provide a 'Target Text' (which could be a single word, a phrase, or a full sentence) and a 'Context Sentence'.
                
                YOUR STRICT INSTRUCTIONS:
                1. You MUST translate and explain the ENTIRE 'Target Text' provided.
                2. If the 'Target Text' is a single word, use the 'Context Sentence' to find its specific meaning.
                3. If the 'Target Text' is a full sentence, translate it accurately as a sentence.
                4. You MUST reply ONLY with a valid JSON object. Do not add markdown blocks like ```json.
                
                The JSON must have exactly these keys:
                {
                \"explanation\": \"Clear English explanation of the Target Text. If it's a phrase/sentence, explain the overall meaning. in english only\",
                \"translation\": \"Accurate Indonesian translation of the ENTIRE Target Text.\",
                \"grammar\": \"If single word: Part of Speech (e.g., Kata Kerja, Kata Benda). If phrase/sentence: Grammatical Structure (e.g., Frasa Kata Sifat, Kalimat Lengkap). Write in Indonesian.\",
                \"collocations\": [\"contoh 1: (arti)\", \"contoh 2: (arti)\"], // Jika Target Text adalah kalimat panjang, biarkan array ini KOSONG [].
                \"nuance\": \"Brief description in Indonesian of the Target Text's connotation. Jika tidak ada, tulis 'Konteks umum'.\",
                \"tense_info\": \"Identify the main Tense used (e.g., Simple Present Tense, Past Continuous). Briefly explain why it is used. If no verb, write 'Bukan kata kerja / Tidak relevan'.\",
                \"idiom_note\": \"Brief explanation in Indonesian if the Target Text contains an idiom, otherwise write 'Bukan ungkapan/idiom'.\",
                \"tip\": \"Provide a practical tip on how to use this word/phrase/sentence structure. Format: 'Sering digunakan dalam pola [sebutkan pola]. Contoh: [kalimat singkat].'\"
                }";

        $userPrompt = "Target Text: \"{$text}\"\nContext Sentence: \"{$context}\"";

        try {
            // 3. Panggil API Groq dengan Llama 3
            $response = Http::withoutVerifying() 
                ->withToken($apiKey)
                ->timeout(15) 
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-120b', 
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
                
                $aiData = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
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