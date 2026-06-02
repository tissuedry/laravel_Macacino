@extends('layouts.base')
@section('title', 'Profil | Macacino')

@section('body')
<div class="library-layout">
  @include('layouts.sidebar')

  <main class="lib-main">
    <header class="lib-header">
      <div>
        <h1 class="lib-title">Profil Saya</h1>
        <p class="lib-subtitle">Kelola keamanan akun Anda.</p>
      </div>
    </header>

    <div style="margin-top: 10px; max-width: 500px;">
      <div class="doc-card" style="padding: 24px;">
        <h3 style="margin-bottom: 20px;">Detail Akun</h3>

        <form id="profile-form">
          <div class="form-group">
            <label class="form-label">Username</label>
            <input type="text" value="{{ Auth::user()->username }}" class="form-input" readonly style="background-color: var(--bg-secondary); cursor: not-allowed; color: var(--text-muted);">
            <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">* Hak paten sistem: Username tidak dapat diubah.</p>
          </div>

          <div class="form-group" style="margin-top: 20px;">
            <label class="form-label">Password Baru</label>
            <div style="position: relative;">
              <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Ketik untuk mengubah password..." style="padding-right: 40px;">
              <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted);">
                <svg id="eye-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 25px;" id="save-btn">Perbarui Password</button>
        </form>
      </div>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  function togglePassword() {
    const pwdInput = document.getElementById('new_password');
    const eyeIcon = document.getElementById('eye-icon');

    if (pwdInput.type === 'password') {
      pwdInput.type = 'text';
      eyeIcon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
    } else {
      pwdInput.type = 'password';
      eyeIcon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
    }
  }

  document.getElementById('profile-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const newPassword = document.getElementById('new_password').value;
    const btn = document.getElementById('save-btn');

    if (!newPassword) {
      showToast('Harap isi password jika ingin merubahnya.', 'error');
      return;
    }

    btn.disabled = true;
    btn.innerText = 'Menyimpan...';

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const res = await fetch('/api/profile/update', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken 
        },
        body: JSON.stringify({ new_password: newPassword })
      });

      const data = await res.json();

      if (res.ok) {
        showToast('Password berhasil diperbarui!', 'success');
        document.getElementById('new_password').value = ''; 
      } else {
        throw new Error(data.error || 'Terjadi kesalahan');
      }
    } catch (err) {
      showToast(err.message, 'error');
    } finally {
      btn.disabled = false;
      btn.innerText = 'Perbarui Password';
    }
  });
</script>
@endsection