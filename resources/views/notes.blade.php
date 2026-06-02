@extends('layouts.base')
@section('title', 'Macacino | Notes & Vocabulary')

@section('body')
<div class="library-layout">
  @include('layouts.sidebar')

  <main class="lib-main">
    <header class="lib-header" style="margin-bottom: 24px;">
      <div>
        <h1 class="lib-title">My Notes Collection</h1>
        <p class="lib-subtitle">All your new words and AI notes drawn directly from your database.</p>
      </div>
    </header>

    @if (empty($grouped_notes))
    <div style="text-align: center; padding: 60px; color: var(--text-muted);">
        <span style="font-size: 40px;">📭</span>
        <p style="margin-top: 10px;">You don't have any saved notes or vocabulary yet.</p>
    </div>
    @endif

    @foreach ($grouped_notes as $book_title => $notes)
    <div style="background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 30px;">
      <h3 style="margin-bottom: 20px; font-family: var(--font-heading); display: flex; align-items: center; gap: 8px;">
        <span>📚</span> {{ $book_title }}
      </h3>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 16px;">
        @foreach ($notes as $note)
            @php
                // Proses data JSON dari AI secara aman
                $details = [];
                if (!empty($note->ai_details)) {
                    $details = is_string($note->ai_details) ? json_decode($note->ai_details, true) : $note->ai_details;
                }
                
                $grammar = $details['grammar'] ?? $note->ai_grammar ?? 'Vocabulary';
                $tenseInfo = $details['tense_info'] ?? null;
                $idiomNote = $details['idiom_note'] ?? null;
                $tip = $details['tip'] ?? null;
                $collocations = $details['collocations'] ?? [];
            @endphp

            <div style="padding: 16px; border: 1px solid var(--border); border-radius: var(--radius-md); background: var(--bg-warm); display: flex; flex-direction: column;">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed var(--border-strong);">
                    <mark style="background: {{ $note->color ?? 'rgba(255, 213, 79, 0.55)' }}; border-radius: 4px; padding: 3px 6px; font-style: italic; font-weight: 500; font-size: 14px;">
                        "{{ $note->text_content }}"
                    </mark>
                    <span style="font-size: 11px; color: var(--text-muted); white-space: nowrap; margin-left: 10px;">Pg. {{ $note->page_number }}</span>
                </div>

                <div style="margin-bottom: 4px;">
                    <span style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">🇮🇩 Terjemahan:</span>
                    <p style="font-size: 14px; color: var(--text); font-weight: 500; margin-top: 4px; line-height: 1.4;">
                        {{ $note->ai_translation ?? 'Tidak ada terjemahan.' }}
                    </p>
                </div>

                <details class="note-accordion" style="margin-top: 8px;">
                    <summary style="font-size: 12px; font-weight: 600; color: var(--primary); cursor: pointer; user-select: none; display: inline-flex; align-items: center; gap: 6px; padding: 4px 0;">
                        ✨ Lihat Detail Analisis AI <span class="accordion-arrow">▼</span>
                    </summary>
                    
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border); font-size: 12px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                        
                        @if(!empty($note->ai_explanation))
                        <div style="background: var(--surface); padding: 10px; border-radius: 6px; border: 1px solid var(--border);">
                            <strong style="color: var(--text); display: block; margin-bottom: 4px;">🧠 English Explanation</strong>
                            <span style="line-height: 1.5;">{{ $note->ai_explanation }}</span>
                        </div>
                        @endif

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
                            <div style="background: var(--surface); padding: 10px; border-radius: 6px; border: 1px solid var(--border);">
                                <strong style="color: var(--text); display: block; margin-bottom: 4px;">⚙️ Grammar Context</strong>
                                <span>{{ $grammar }}</span>
                            </div>

                            @if(!empty($tip))
                            <div style="background: var(--surface); padding: 10px; border-radius: 6px; border: 1px solid var(--border); border-left: 3px solid #10b981;">
                                <strong style="color: var(--text); display: block; margin-bottom: 4px;">💡 Pro Tip</strong>
                                <span>{{ $tip }}</span>
                            </div>
                            @endif

                            @if(!empty($tenseInfo) && !str_contains(strtolower($tenseInfo), 'bukan'))
                            <div style="background: var(--surface); padding: 10px; border-radius: 6px; border: 1px solid var(--border);">
                                <strong style="color: var(--text); display: block; margin-bottom: 4px;">🕐 Tense / Verb Forms</strong>
                                <span>{{ $tenseInfo }}</span>
                            </div>
                            @endif

                            @if(!empty($idiomNote) && !str_contains(strtolower($idiomNote), 'bukan'))
                            <div style="background: var(--surface); padding: 10px; border-radius: 6px; border: 1px solid var(--border);">
                                <strong style="color: var(--text); display: block; margin-bottom: 4px;">🎭 Idiom Note</strong>
                                <span>{{ $idiomNote }}</span>
                            </div>
                            @endif
                        </div>

                        @if(!empty($collocations) && count($collocations) > 0)
                        <div style="background: var(--surface); padding: 10px; border-radius: 6px; border: 1px solid var(--border);">
                            <strong style="color: var(--text); display: block; margin-bottom: 6px;">🔗 Common Collocations</strong>
                            <ul style="margin: 0; padding-left: 14px; display: flex; flex-direction: column; gap: 4px;">
                                @foreach($collocations as $colloc)
                                    <li>{{ $colloc }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                    </div>
                </details>

            </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </main>
</div>

<style>
    .note-accordion summary::-webkit-details-marker { display: none; } /* Sembunyikan default panah browser */
    .note-accordion summary { list-style: none; }
    
    .note-accordion[open] .accordion-arrow {
        transform: rotate(180deg);
    }
    .accordion-arrow {
        display: inline-block;
        transition: transform 0.25s ease;
        font-size: 10px;
    }
</style>
@endsection