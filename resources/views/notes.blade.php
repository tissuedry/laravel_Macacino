@extends('layouts.base')
@section('title', 'Macacino — Notes & Vocabulary')

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
      
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
        @foreach ($notes as $note)
            <div style="padding: 14px; border: 1px solid var(--border); border-radius: var(--radius-md); background: var(--bg-warm); display: flex; flex-direction: column;">
                <p style="font-style: italic; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-strong); font-size: 13px;">
                    <mark style="background: {{ $note->color ?? 'rgba(255, 213, 79, 0.55)' }}; border-radius: 3px; padding: 2px 4px;">"{{ $note->text_content }}"</mark>
                    <span style="font-size: 11px; color: var(--text-muted); float: right;">Pg. {{ $note->page_number }}</span>
                </p>

                @if ($note->ai_vocabulary)
                    <div style="margin-top: auto;">
                        <span style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">Key Vocabulary:</span>
                        <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 6px;">
                        @foreach ($note->ai_vocabulary as $vocab)
                            <div style="background: var(--surface); border: 1px solid var(--border); padding: 6px 10px; border-radius: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 2px;">
                                    <strong style="color: var(--accent); font-size: 14px;">{{ $vocab['word'] ?? '' }}</strong> 
                                    <span style="color: var(--text-muted); font-size: 10px; background: var(--bg); padding: 2px 6px; border-radius: 4px;">{{ $vocab['type'] ?? '' }}</span>
                                </div>
                                <span style="color: var(--text-secondary); font-size: 12px; line-height: 1.4; display: block;">{{ $vocab['meaning'] ?? '' }}</span>
                            </div>
                        @endforeach
                        </div>
                    </div>
                @else
                    <div style="margin-top: auto;">
                        <p style="font-size: 12px; color: var(--text-secondary); margin-top: 8px;">
                            <strong style="font-size: 11px; text-transform: uppercase;">Translation / Notes:</strong><br>
                            {{ $note->ai_translation ?? ($note->ai_explanation ?? "No specific notes.") }}
                        </p>
                    </div>
                @endif
            </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </main>
</div>
@endsection