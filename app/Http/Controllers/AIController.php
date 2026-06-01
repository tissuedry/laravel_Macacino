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
     * Endpoint untuk Penjelasan AI (Menggunakan Groq API / Llama 3)
     */
    public function explainText(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'text' => 'required|string|max:1000'
        ]);

        $text = $request->input('text');
        $apiKey = env('GROQ_API_KEY');

        // Pastikan API Key ada di file .env
        if (!$apiKey) {
            return response()->json([
                'error' => 'API Key Groq belum diatur di sistem (.env).'
            ], 500);
        }

        // 2. Prompt sistem (Instruksi ketat agar Groq merespons dalam format JSON)
        $systemPrompt = "You are an expert English teacher helping an Indonesian student. Analyze the given English text/word. You MUST reply ONLY with a valid JSON object. Do not add markdown blocks like ```json. The JSON must have exactly these keys:
        'explanation': (string) Clear English explanation of the word/phrase.
        'translation': (string) Indonesian translation.
        'grammar': (string) Grammar context or part of speech in Indonesian.
        'vocabulary': (array of strings) 2 to 3 related English words or synonyms.
        'idiom_note': (string) If it's an idiom, explain briefly in Indonesian. If not, leave empty.
        'tip': (string) A short, encouraging tip about using this word in Indonesian.";

       try {
            // 3. Memanggil API Groq
            $response = Http::withoutVerifying() // Tambahkan ini agar tidak diblokir SSL Windows lokal
                ->withToken($apiKey)
                ->timeout(15) // Batas waktu maksimal 15 detik
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $text]
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.3, // Rendah agar format JSON stabil
                ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Mengambil teks balasan dari Groq
                $content = $result['choices'][0]['message']['content'];
                
                // Mengubah string JSON dari AI menjadi Array PHP
                $aiData = json_decode($content, true);

                // Kirim kembali ke browser (sesuai format yang diharapkan reader.js)
                return response()->json([
                    'data' => $aiData
                ]);
            }

            // Jika API Groq menolak request (misal kuota habis)
            return response()->json([
                'error' => 'Gagal mendapatkan respons dari server AI Groq.',
                'details' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan sistem saat menghubungi AI: ' . $e->getMessage()
            ], 500);
        }
    }
}