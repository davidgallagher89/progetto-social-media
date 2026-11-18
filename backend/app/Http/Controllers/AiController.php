<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function generatePost(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:200',
        ]);

        $apiKey = env('DEEPSEEK_API_KEY');

        if (!$apiKey || $apiKey === 'inserisci_qui_la_tua_chiave_deepseek') {
            return response()->json([
                'error' => 'Chiave API DeepSeek non configurata nel file .env.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://api.deepseek.com/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Sei un assistente per social media. Scrivi post brevi in italiano.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Scrivi un breve post su questo argomento: ' . $request->input('topic')
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 250,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['choices'][0]['message']['content'] ?? '';

                return response()->json([
                    'success' => true,
                    'text'    => trim($generatedText),
                ]);
            }

            return response()->json([
                'error'   => 'Errore nella risposta di DeepSeek',
                'details' => $response->body()
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Impossibile contattare il servizio AI: ' . $e->getMessage()
            ], 500);
        }
    }

    public function summarizeComments(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::with('comments.user')->findOrFail($request->input('post_id'));

        if ($post->comments->isEmpty()) {
            return response()->json([
                'summary' => 'Non ci sono ancora commenti su questo post.'
            ]);
        }

        $commentsText = $post->comments->map(function ($c) {
            return "- " . ($c->user->name ?? 'Utente') . ": " . $c->content;
        })->implode("\n");

        $apiKey = env('DEEPSEEK_API_KEY');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://api.deepseek.com/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Riassumi in 2-3 frasi i commenti ricevuti sotto un post.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Post: \"{$post->content}\"\n\nCommenti:\n{$commentsText}\n\nRiassunto:"
                    ]
                ],
            ]);

            $data = $response->json();
            $summary = $data['choices'][0]['message']['content'] ?? 'Nessun riassunto disponibile.';

            return response()->json([
                'summary' => trim($summary)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Errore nel riassunto: ' . $e->getMessage()
            ], 500);
        }
    }
}
