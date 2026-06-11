@extends('layouts.base')
@section('title', 'Macacino | Learning Statistics')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .chart-container { 
      background: var(--surface); 
      border: 1px solid var(--border); 
      border-radius: var(--radius-xl); 
      padding: 24px; 
      box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
      transition: transform 0.2s ease, box-shadow 0.2s ease; 
  }
  .chart-container:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 24px rgba(0,0,0,0.06);
  }
  .chart-title { 
      font-family: var(--font-heading); 
      font-size: 16px; 
      margin-bottom: 20px; 
      color: var(--text-primary); 
      text-align: center; 
      font-weight: 600;
  }
  .doc-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
  }
  .doc-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
      border-color: var(--primary) !important;
  }
</style>
@endsection

@section('body')
<div class="library-layout">
  @include('layouts.sidebar')

  <main class="lib-main">
    <header class="lib-header" style="margin-bottom: 40px;">
      <div>
        <h1 class="lib-title">Progress Dashboard</h1>
        <p class="lib-subtitle">Monitor your reading activity and saved notes.</p>
      </div>
    </header>

    <div class="doc-grid" style="grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
      <div class="doc-card" style="text-align: center; padding: 30px 20px; cursor: default;">
        <div style="font-size: 40px; margin-bottom: 10px;">🔥</div>
        <h2 style="font-size: 28px; color: var(--accent);">{{ $streak_days ?? 0 }} {{ Str::plural('Day', $streak_days) }}</h2>
        <p class="text-muted" style="font-size: 13px;">Consecutive study streak</p>
      </div>
      <div class="doc-card" style="text-align: center; padding: 30px 20px; cursor: default;">
        <div style="font-size: 40px; margin-bottom: 10px;">📝</div>
        <h2 style="font-size: 28px; color: var(--primary);">{{ $total_words ?? 0 }} {{ Str::plural('Note', $total_words) }}</h2>
        <p class="text-muted" style="font-size: 13px;">Saved highlights & AI notes</p>
      </div>
      <div class="doc-card" style="text-align: center; padding: 30px 20px; cursor: default;">
        <div style="font-size: 40px; margin-bottom: 10px;">🏆</div>
        <h2 style="font-size: 28px; color: var(--success);">{{ $finished_books ?? 0 }} Completed</h2>
        <p class="text-muted" style="font-size: 13px;">Books you have finished</p>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <div class="chart-container">
            <h3 class="chart-title">📈 Reading Progress (%)</h3>
            <canvas id="progressChart" height="200"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">📚 Notes per Book</h3>
            <canvas id="vocabChart" height="250"></canvas>
        </div>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
    const docTitles = {!! isset($doc_titles) ? $doc_titles : '[]' !!};
    const docProgress = {!! isset($doc_progress) ? $doc_progress : '[]' !!};
    const vocabTitles = {!! isset($vocab_titles) ? $vocab_titles : '[]' !!};
    const vocabCounts = {!! isset($vocab_counts) ? $vocab_counts : '[]' !!};

    const ctxProgress = document.getElementById('progressChart').getContext('2d');
    
    // Palet warna premium untuk masing-masing bar agar warnanya berbeda tiap buku
    const barColors = [
        'rgba(52, 211, 153, 0.75)', // Emerald
        'rgba(14, 165, 233, 0.75)',  // Sky Blue
        'rgba(245, 158, 11, 0.75)',  // Amber
        'rgba(139, 92, 246, 0.75)',  // Purple
        'rgba(239, 68, 68, 0.75)',   // Rose
        'rgba(236, 72, 153, 0.75)',  // Pink
        'rgba(20, 184, 166, 0.75)'   // Teal
    ];
    const barBorders = [
        'rgba(52, 211, 153, 1)',
        'rgba(14, 165, 233, 1)',
        'rgba(245, 158, 11, 1)',
        'rgba(139, 92, 246, 1)',
        'rgba(239, 68, 68, 1)',
        'rgba(236, 72, 153, 1)',
        'rgba(20, 184, 166, 1)'
    ];

    new Chart(ctxProgress, {
        type: 'bar',
        data: {
            labels: docTitles.length > 0 ? docTitles : ['No books yet'],
            datasets: [{
                label: 'Completion (%)',
                data: docProgress.length > 0 ? docProgress : [0],
                backgroundColor: barColors,
                borderColor: barBorders,
                borderWidth: 1.5,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { display: false } 
            },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    min: 0, 
                    max: 100,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.04)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    const ctxVocab = document.getElementById('vocabChart').getContext('2d');
    
    // Warna premium untuk sebaran buku (7 warna berputar)
    const vocabColors = [
        'rgba(52, 211, 153, 0.85)', // Emerald
        'rgba(14, 165, 233, 0.85)',  // Sky Blue
        'rgba(245, 158, 11, 0.85)',  // Amber
        'rgba(139, 92, 246, 0.85)',  // Purple
        'rgba(239, 68, 68, 0.85)',   // Rose
        'rgba(236, 72, 153, 0.85)',  // Pink
        'rgba(20, 184, 166, 0.85)'   // Teal
    ];

    // Cek jika datanya kosong total
    const totalNotes = vocabCounts.reduce((a, b) => a + b, 0);

    new Chart(ctxVocab, {
        type: 'doughnut',
        data: {
            labels: totalNotes > 0 ? vocabTitles : ['No notes yet'],
            datasets: [{
                data: totalNotes > 0 ? vocabCounts : [1],
                backgroundColor: totalNotes > 0 ? vocabColors : ['rgba(155, 145, 138, 0.2)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '72%',
            plugins: { 
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        boxWidth: 12, 
                        font: { size: 12, weight: '500' },
                        color: 'var(--text-secondary)'
                    } 
                } 
            }
        }
    });
</script>
@endsection