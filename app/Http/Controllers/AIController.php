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

        $request->validate([
            'text' => 'required|string',
            'accent' => 'nullable|string'
        ]);

        $text = $request->input('text');
        $accent = $request->input('accent', 'american');


        $langCode = ($accent === 'british') ? 'en-GB' : 'en-US';


        $textEncoded = urlencode($text);
        $url = "https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&tl={$langCode}&q={$textEncoded}";

        try {

            $audioData = file_get_contents($url);

            if ($audioData === false) {
                throw new \Exception("Gagal mengambil data audio dari server.");
            }


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

        $request->validate([
            'text' => 'required|string|max:1000',
            'context' => 'nullable|string|max:2000'
        ]);

        $text = $request->input('text');
        $context = $request->input('context', 'Tidak ada konteks spesifik.');
        $apiKey = env('GROQ_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API Key Groq belum diatur di file .env.'
            ], 500);
        }


        $systemPrompt = "You are an expert English teacher helping Indonesian learners.
                The user will provide a 'Target Text' (single word, phrase, or sentence) and a 'Context Sentence' from a novel.

                YOUR STRICT INSTRUCTIONS:
                1. You MUST reply ONLY with a valid JSON object. Do not add markdown code blocks like ```json.
                2. Explain target text based strictly on the provided 'Context Sentence', focusing on its contextual meaning rather than general definition.
                3. The JSON must have exactly these keys:
                {
                \"explanation\": \"A clear, 1-2 sentence explanation of the Target Text in English only, focusing on its meaning within the context of the sentence (e.g., 'In this context, ...').\",
                \"translation\": \"Accurate Indonesian translation of the ENTIRE Target Text. Provide the most contextually appropriate translation instead of a literal one.\",
                \"grammar\": \"Grammar context of the Target Text. Format: English (Indonesia). Choose only from: 'Verb (kata kerja)', 'Noun (kata benda)', 'Adjective (kata sifat)', 'Adverb (kata keterangan)', 'Preposition (kata depan)', 'Conjunction (kata sambung)', or 'Phrase (frasa)'.\",
                \"collocations\": [\"English phrase (brief explanation in Indonesian)\", \"English phrase 2 (brief explanation in Indonesian)\"], // Max 3 common collocations in this format. If the Target Text is a long sentence, leave this array empty [].\",
                \"nuance\": \"Full Indonesian explanation of the word's connotation or nuance. Use one of these color emoji prefixes to indicate the connotation: 🔴 (negatif), 🟡 (netral / formal), ⚪ (netral / tenang), or 🟢 (positif). E.g., '⚪ Memberikan kesan tenang, diam, dan tidak aktif.'\",
                \"tense_info\": \"If the Target Text is a finite verb (kata kerja utama), identify the main Tense used and explain why it is used in Indonesian. If it is NOT a finite verb, write an empty string \\\"\\\".\",
                \"idiom_note\": \"If the Target Text is part of a common idiom, write: 'English idiom/phrase (penjelasan dalam bahasa Indonesia)'. If not, write: 'Tidak ada idiom umum untuk kata ini'.\",
                \"tip\": \"Practical tip in Indonesian with example sentences in English only, focusing on patterns or common mistakes. Before writing the example sentence, you MUST verify that the word's grammatical function in your example is IDENTIK (identical) to the function you are explaining: \n                - [ADJECTIVE]: Example must show the word modifying a noun or after a linking verb. NEVER as a verb or part of a tense (e.g., use 'a sleeping child', NOT 'The river was flowing' which is continuous tense).\n                - [VERB]: Example must show it as an action/state with a clear subject and correct tense (e.g., 'She ran to the door', NOT as a noun/adjective).\n                - [NOUN]: Example must show it as a subject/object/complement (e.g., 'The courage she showed...', NOT as a modifier/adjective).\n                - [ADVERB]: Example must show it modifying a verb/adjective/adverb, NOT a noun (e.g., 'She spoke softly', NOT 'A softly voice').\n                - [PREPOSITION]: Example must show relation with a clear object (e.g., 'The jar was on the shelf').\n                - [PHRASE / IDIOM]: Example must use the phrase/idiom in a full sentence with consistent meaning (e.g., 'Let's not bring it up — let sleeping dogs lie').\n                Format: 'Explanation in Indonesian. Contoh: English example sentence.'\"
                }";

        $userPrompt = "Target Text: \"{$text}\"\nContext Sentence: \"{$context}\"";

        try {

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