@extends('layouts.base')
@section('title', 'Macacino | Library')

@section('body')
<div class="library-layout">
  @include('layouts.sidebar')

  <main class="lib-main">
    <header class="lib-header">
      <div>
        <h1 class="lib-title">Library</h1>
        <p class="lib-subtitle">Manage your PDF collection</p>
      </div>
      <button class="btn btn-primary" id="upload-btn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Upload PDF
      </button>
    </header>

    <div class="lib-search-wrap">
      <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="search-input" class="lib-search" placeholder="Search documents..." />
    </div>

    <div class="doc-grid" id="doc-grid">
      <div class="doc-empty" id="empty-state">
        <div class="empty-icon">📚</div>
        <h3>No documents yet</h3>
        <p>Upload a PDF to start reading and learning.</p>
      </div>
    </div>
  </main>
</div>

<div class="modal-backdrop" id="upload-modal" hidden>
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title">Upload PDF</h2>
      <button class="modal-close" id="modal-close-btn">&times;</button>
    </div>
    <div class="modal-body">
      
      <div class="drop-zone" id="drop-zone">
        <div class="drop-zone-icon">📄</div>
        <p class="drop-zone-text">Drag & Drop file PDF di sini</p>
        <p class="drop-zone-hint" style="margin-bottom: 12px;">atau</p>
        <button type="button" class="btn btn-outline" onclick="document.getElementById('file-input').click()">
          Pilih File
        </button>
        <input type="file" id="file-input" accept=".pdf" hidden />
      </div>

      <div id="file-selected" hidden>
        <div class="file-preview">
          <div class="file-preview-icon">📑</div>
          <div class="file-preview-info">
            <div class="file-preview-name" id="file-preview-name">nama-file.pdf</div>
            <div class="file-preview-size" id="file-preview-size">0 MB</div>
          </div>
          <button class="file-remove-btn" id="file-remove-btn" title="Hapus file">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="doc-title">Judul Dokumen</label>
          <input type="text" id="doc-title" class="form-input" placeholder="Masukkan judul buku/dokumen..." />
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" id="cancel-upload-btn">Batal</button>
      <button class="btn btn-primary" id="confirm-upload-btn" disabled>
        <span id="upload-spinner" class="spinner" hidden style="margin-right: 6px;"></span>
        <span id="upload-btn-text">Upload</span>
      </button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/index.js') }}"></script>
@endsection