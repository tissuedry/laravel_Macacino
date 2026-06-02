@extends('layouts.base')
@section('title', 'Macacino | Reader')

@section('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<style>
    /* =========================================================
     KEMBALIKAN DESAIN AI PANEL & READING NOTES YANG HILANG 
     ========================================================= */
  
  #hl-list { overflow-y: auto; overflow-x: hidden; scrollbar-width: thin; scrollbar-color: var(--border) transparent; padding: 12px; }
  #hl-list::-webkit-scrollbar { width: 4px; }
  #hl-list::-webkit-scrollbar-track { background: transparent; }
  #hl-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
  
  .sidebar-note-card { border-radius: 8px; margin-bottom: 12px; background: var(--surface); border: 1px solid var(--border); box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: transform 0.15s; word-wrap: break-word; overflow-wrap: break-word; }
  .sidebar-note-card:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); border-color: var(--border-strong, #ccc); }
  .snc-header { padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; }
  .snc-badge { font-size: 0.72em; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
  .snc-actions { display: flex; gap: 6px; align-items: center; }
  .snc-page-badge { font-size: 0.72em; background: var(--bg-warm); padding: 2px 8px; border-radius: 12px; color: var(--text-primary); border: 1px solid var(--border); }
  .snc-delete-btn { background: none; border: none; cursor: pointer; color: var(--danger); font-size: 0.95em; padding: 2px; }
  .snc-selected-text { padding: 0 12px 10px 12px; display: flex; justify-content: space-between; align-items: flex-start; gap: 6px; }
  .snc-selected-text p { margin: 0; font-size: 0.92em; font-weight: 500; line-height: 1.5; flex: 1; color: var(--text-primary); }
  
  .snc-audio-btn { background: none; border: none; font-size: 1.1em; cursor: pointer; color: var(--text-muted); transition: color 0.2s; padding: 0 4px; }
  .snc-audio-btn:hover { color: var(--accent); }
  
  .snc-accordion { border-top: 1px solid var(--border); }
  .snc-details { border-bottom: 1px solid var(--border); }
  .snc-details:last-child { border-bottom: none; }
  .snc-details summary { list-style: none; padding: 10px 12px; font-size: 0.85em; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center; color: var(--text-primary); background: var(--bg-warm); transition: background 0.15s; user-select: none; }
  .snc-details summary::-webkit-details-marker { display: none; }
  .snc-details summary:hover { background: var(--border); }
  .snc-arrow { font-size: 0.8em; color: var(--text-muted); transition: transform 0.2s; }
  .snc-details[open] summary .snc-arrow { transform: rotate(180deg); }
  
  .ai-panel-body { word-wrap: break-word; overflow-wrap: break-word; overflow-x: hidden; }
  
  .ai-stack details { background:var(--bg-secondary,#f8f9fa); border:1px solid var(--border,#e9ecef); border-radius:8px; margin-bottom:10px; overflow:hidden; }
  .ai-stack summary { padding:10px 15px; font-weight:600; cursor:pointer; background:var(--bg-panel,#fff); color:var(--text-primary); transition:background 0.2s; list-style:none; display:flex; justify-content:space-between; align-items:center; }
  .ai-stack summary::-webkit-details-marker { display:none; }
  .ai-stack summary::after { content:'▼'; font-size:0.8em; color:var(--text-muted); transition:transform 0.2s; }
  .ai-stack details[open] summary::after { transform:rotate(180deg); }
  .ai-stack summary:hover { background:var(--bg-hover,#e2e6ea); }
  .ai-stack .details-content { padding:15px; font-size:0.95em; line-height:1.6; color:var(--text-primary); word-wrap: break-word; }
  
  .save-note-btn { background:var(--primary,#0d6efd); color:white; border:none; padding:12px 15px; border-radius:6px; cursor:pointer; margin-top:25px; width:100%; font-weight:600; transition:background 0.2s; }
  .save-note-btn:hover { background:#0b5ed7; transform:translateY(-1px); }
  
  .play-audio-btn { background:none; border:none; font-size:1.2em; cursor:pointer; padding:2px 5px; border-radius:4px; transition:background 0.2s; color:var(--text-primary); }
  .play-audio-btn:hover { background:var(--bg-hover,#e2e6ea); color:var(--primary); }
  .ai-selected-text-box { background:var(--bg-secondary,#f8f9fa); padding:10px 12px; border-radius:8px; margin-bottom:1rem; border:1px solid var(--border,#e9ecef); word-wrap: break-word; overflow-wrap: break-word; overflow-x: hidden; }

  /* --- DESAIN KARTU 'MORE DETAILS' UNTUK SIDEBAR KANAN & KIRI --- */
  .md-card { background: var(--bg-secondary, #f8f9fa); border: 1px solid var(--border, #e9ecef); border-radius: 8px; padding: 14px; margin-bottom: 12px; }
  .md-card-title { margin: 0 0 8px 0; color: var(--primary, #0d6efd); font-size: 0.82em; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px; font-weight: 700; }
  
  .md-card-pronounce { background: rgba(237, 66, 69, 0.05); border: 1px solid rgba(237, 66, 69, 0.2); }
  .md-card-pronounce .md-card-title { color: #ed4245; }
  
  .btn-youglish { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #ed4245; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: 700; transition: background 0.2s; font-size: 13.5px; width: 100%; box-sizing: border-box; margin-top: 6px;}
  .btn-youglish:hover { background: #d03a3d; color: white; text-decoration: none;}

  .md-vocab-list { padding-left: 0; margin: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; }
  .md-vocab-list li { color: var(--text-primary); line-height: 1.5; font-size: 0.9em; padding-bottom: 8px; border-bottom: 1px dashed var(--border); }
  .md-vocab-list li:last-child { border-bottom: none; padding-bottom: 0; }
  .md-vocab-list strong { color: var(--primary); font-weight: 700; font-size: 1.05em; }
  .md-vocab-list .type { font-style: italic; color: var(--text-muted); font-size: 0.9em; margin-right: 4px; }
  .md-vocab-list .example { color: var(--text-muted); font-size: 0.9em; display: block; margin-top: 4px; }
  /* CSS Global Override: Hanya untuk elemen layout yang harus disembunyikan di mode reader */
  aside:not(.highlights-sidebar):not(.ai-panel),
  .sidebar, .main-sidebar, .navigation-sidebar, #sidebar {
    display: none !important;
  }

  main, .main-content, .content-wrapper, #content {
    margin-left: 0 !important;
    padding-left: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
  }

  /* Perbaikan Fokus Mode & Theme (Tidak berubah dari kode asli Anda) */
  #focus-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; display: flex; justify-content: center; align-items: center; z-index: 9999; opacity: 0; visibility: hidden; transition: opacity 0.5s ease; }
  #focus-overlay.active { opacity: 1; visibility: visible; }
  
  /* Sisa CSS fokus theme tetap sama seperti kode Anda... */
</style>
@endsection

@section('body')
<div class="reader-layout" data-document-id="{{ $document_id }}">

  <header class="reader-topbar">
    <div class="topbar-left">
      <a href="/" class="back-btn" title="Kembali ke Library">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      </a>
      <button class="icon-btn" id="toggle-left-sidebar-btn" title="Tutup/Buka Reading Notes">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
      </button>
      <div class="topbar-doc-info">
        <h1 class="topbar-title" id="doc-title">Loading...</h1>
      </div>
    </div>
    
    <div class="topbar-center">
      <div class="page-control">
        <button class="page-btn" id="prev-page-btn"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
        <div class="page-indicator">
          <input type="number" id="current-page-input" class="page-input" value="1" min="1" />
          <span class="page-sep">/</span>
          <span id="total-pages">—</span>
        </div>
        <button class="page-btn" id="next-page-btn"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
      </div>
    </div>
    
    <div class="topbar-right">
      <button class="icon-btn" id="toggle-highlights-btn" title="Sembunyikan Stabilo"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
      <button class="icon-btn" id="start-focus-btn" title="Mode Fokus"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg></button>
      <button class="icon-btn" id="highlights-toggle-btn" title="Catatan AI"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></button>
    </div>
  </header>

  <div class="reader-body">
    <aside class="highlights-sidebar" id="highlights-sidebar">
      <div class="hl-sidebar-header">
        <h3 class="hl-sidebar-title">Reading Notes</h3>
        <button id="delete-all-notes-btn" class="icon-btn" style="color: var(--danger);"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg></button>
      </div>
      <div class="hl-list" id="hl-list"></div>
      <div class="resizer right-edge" id="left-resizer"></div>
    </aside>

    <div class="pdf-area" id="pdf-area">
      <div class="pdf-loading" id="pdf-loading"><div class="loading-dot-wrap"><span class="loading-dot"></span><span class="loading-dot"></span><span class="loading-dot"></span></div></div>

      <div class="center-reader-wrapper" id="center-reader-wrapper" hidden>
        <div class="resizer left-edge" id="center-resizer-left"></div>
        <div id="html-text-container" class="clean-text-reader"></div>
        <div class="resizer right-edge" id="center-resizer-right"></div>
      </div>
    </div>

    <aside class="ai-panel" id="ai-panel" hidden>
      <div class="resizer left-edge" id="right-resizer"></div>
      <div class="ai-panel-header">
        <h3 class="ai-panel-title">🤖 AI Analysis</h3>
        <button class="ai-panel-close" id="ai-panel-close"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>
      <div class="ai-panel-body" id="ai-panel-body"></div>
    </aside>
  </div>
</div>
@endsection

@section('scripts')
<script>
  window.DOCUMENT_ID = "{{ $document_id }}";
</script>
<script src="{{ asset('js/reader.js') }}"></script>
@endsection