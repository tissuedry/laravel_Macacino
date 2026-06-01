@extends('layouts.base')
@section('title', 'Macacino — Learning Statistics')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .chart-container { background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); }
  .chart-title { font-family: var(--font-heading); font-size: 16px; margin-bottom: 20px; color: var(--text-primary); text-align: center; }
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
      <div class="doc-card" style="text-align: center; padding: 30px 20px;">
        <div style="font-size: 40px; margin-bottom: 10px;">🔥</div>
        <h2 style="font-size: 28px; color: var(--accent);">{{ $streak_days ?? 0 }} Books</h2>
        <p class="text-muted" style="font-size: 13px;">Previously accessed/read</p>
      </div>
      <div class="doc-card" style="text-align: center; padding: 30px 20px;">
        <div style="font-size: 40px; margin-bottom: 10px;">📝</div>
        <h2 style="font-size: 28px; color: var(--primary);">{{ $total_words ?? 0 }} Notes</h2>
        <p class="text-muted" style="font-size: 13px;">Saved texts/sentences</p>
      </div>
      <div class="doc-card" style="text-align: center; padding: 30px 20px;">
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
            <h3 class="chart-title">📝 Notes Distribution</h3>
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

    const chartColors = [
        'rgba(74, 124, 89, 0.7)',
        'rgba(181, 114, 42, 0.7)',
        'rgba(166, 64, 64, 0.7)',
        'rgba(100, 160, 220, 0.7)',
        'rgba(155, 145, 138, 0.7)',
        'rgba(142, 68, 173, 0.7)',
        'rgba(230, 126, 34, 0.7)'
    ];
    const chartBorders = chartColors.map(color => color.replace('0.7', '1'));

    const ctxProgress = document.getElementById('progressChart').getContext('2d');
    new Chart(ctxProgress, {
        type: 'bar',
        data: {
            labels: docTitles.length > 0 ? docTitles : ['No books yet'],
            datasets: [{
                label: 'Completion (%)',
                data: docProgress.length > 0 ? docProgress : [0],
                backgroundColor: chartColors,
                borderColor: chartBorders,
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    const ctxVocab = document.getElementById('vocabChart').getContext('2d');
    new Chart(ctxVocab, {
        type: 'doughnut',
        data: {
            labels: vocabTitles.length > 0 ? vocabTitles : ['No notes yet'],
            datasets: [{
                data: vocabCounts.length > 0 ? vocabCounts : [1],
                backgroundColor: chartColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
        }
    });
</script>
@endsection