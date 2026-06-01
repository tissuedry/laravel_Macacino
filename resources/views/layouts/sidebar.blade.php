<aside class="lib-sidebar">
  <div class="lib-brand">
    <span class="brand-icon">📖</span><span class="brand-name">Macacino</span>
  </div>
  <nav class="lib-nav">
    <a href="/" class="lib-nav-item {{ request()->is('/') && !request()->has('filter') ? 'active' : '' }}" id="nav-all">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      All Documents
    </a>
    <a href="/?filter=reading" class="lib-nav-item {{ request('filter') == 'reading' ? 'active' : '' }}" id="nav-reading">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Currently Reading
    </a>
    <a href="/?filter=finished" class="lib-nav-item {{ request('filter') == 'finished' ? 'active' : '' }}" id="nav-finished">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      Completed
    </a>
    
    <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
      <p style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-left: 12px;">Learning Menu</p>
      <a href="/stats" class="lib-nav-item {{ request()->is('stats') ? 'active' : '' }}" id="nav-stats">📊 Learning Stats</a>
      <a href="/notes" class="lib-nav-item {{ request()->is('notes') ? 'active' : '' }}" id="nav-notes">📝 Notes & Vocabulary</a>
      <a href="/profile" class="lib-nav-item {{ request()->is('profile') ? 'active' : '' }}" id="nav-profile">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 2px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          Profil Saya
      </a>
    </div>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
        @csrf
        <button type="submit" class="lib-nav-item" style="color: var(--danger); background: transparent; border: none; width: 100%; text-align: left; cursor: pointer;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Log Out
        </button>
    </form>
  </nav>
</aside>