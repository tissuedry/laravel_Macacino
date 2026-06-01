@extends('layouts.base')
@section('title', 'Daftar — Macacino')

@section('head')
<style>
  body { background-color: #0f172a; margin: 0; overflow: hidden; }
  .focus-auth-wrapper { position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
  .focus-breathing-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e3a8a 0%, #0f172a 80%); opacity: 0.2; z-index: -1; animation: breatheAuth 10s infinite ease-in-out; will-change: opacity, transform; }
  @keyframes breatheAuth { 0% { opacity: 0.2; transform: scale(1); } 50% { opacity: 0.6; transform: scale(1.1); } 100% { opacity: 0.2; transform: scale(1); } }
  .focus-auth-container { position: relative; z-index: 1; width: 100%; max-width: 320px; text-align: center; padding: 20px; }
  .focus-auth-title { font-family: 'Inter', sans-serif; font-weight: 700; font-size: 32px; font-weight: 400; color: #f1f5f9; margin-bottom: 40px; letter-spacing: 1px; }
  
  .focus-input-group { position: relative; margin-bottom: 24px; }
  .focus-input { width: 100%; background: transparent; border: none; border-bottom: 1.5px solid rgba(255, 255, 255, 0.15); padding: 10px 0; font-size: 16px; color: #f1f5f9; text-align: center; outline: none; transition: border-color 0.3s ease, background 0.3s ease; font-family: 'Inter', sans-serif; }
  .focus-input::placeholder { color: rgba(255, 255, 255, 0.25); }
  .focus-input:focus { border-bottom-color: rgba(255, 255, 255, 0.5); }
  
  .focus-eye-btn { position: absolute; right: 0; top: 10px; background: none; border: none; color: rgba(255, 255, 255, 0.3); cursor: pointer; transition: color 0.2s; }
  .focus-eye-btn:hover { color: #f1f5f9; }

  /* PERUBAHAN TEMA KALEM DI SINI */
  .focus-btn-group { display: flex; flex-direction: column; gap: 12px; margin-top: 16px; }
  .focus-submit-btn { width: 100%; background: rgba(255, 255, 255, 0.08); color: #f1f5f9; border: 1px solid rgba(255, 255, 255, 0.15); padding: 12px; font-size: 15px; border-radius: 30px; cursor: pointer; transition: all 0.3s ease;font-family: 'Inter', sans-serif; letter-spacing: 0.5px; backdrop-filter: blur(4px); font-weight: 500; }
  .focus-submit-btn:hover { background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.3); transform: translateY(-1px); }
  
  .focus-hint { font-size: 13px; color: rgba(255, 255, 255, 0.35); margin-top: 28px; line-height: 1.6; font-family: 'Inter', sans-serif; }
  .focus-hint a { color: rgba(255, 255, 255, 0.6); text-decoration: none; font-weight: 500; transition: all 0.2s; border-bottom: 1px solid transparent; }
  .focus-hint a:hover { color: #f1f5f9; border-bottom-color: rgba(255, 255, 255, 0.4); }

  .focus-alert { font-size: 13px; margin-bottom: 24px; padding: 10px 14px; border-radius: 8px; font-weight: 600; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
  .focus-alert.error { color: #ffffff; background: #e63946; border: 1px solid #c1121f; }
</style>
@endsection

@section('body')
<div class="focus-auth-wrapper">
  <div class="focus-breathing-bg"></div>
  
  <div class="focus-auth-container">
    <h1 class="focus-auth-title">Daftar Akun Baru</h1>

    @if ($errors->any())
        <div class="focus-alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf
      
      <div class="focus-input-group">
        <input type="text" name="username" class="focus-input" required placeholder="Nama Pengguna" value="{{ old('username') }}" autocomplete="off" autofocus>
      </div>

      <div class="focus-input-group">
        <input type="email" name="email" class="focus-input" required placeholder="Alamat Email" value="{{ old('email') }}" autocomplete="off">
      </div>
      
      <div class="focus-input-group">
        <input type="password" name="password" id="focus-password" class="focus-input" required placeholder="Password">
        <button type="button" id="toggle-focus-eye" class="focus-eye-btn" tabindex="-1" title="Lihat Password">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>

      <div class="focus-input-group">
        <input type="password" name="password_confirmation" id="focus-password-confirm" class="focus-input" required placeholder="Konfirmasi Password">
      </div>

      <div class="focus-btn-group">
        <button type="submit" class="focus-submit-btn">Daftar Sekarang</button>
      </div>
      
      <p class="focus-hint">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>.
      </p>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const btn = document.getElementById('toggle-focus-eye');
  const inp = document.getElementById('focus-password');
  const confirmInp = document.getElementById('focus-password-confirm');
  
  btn.addEventListener('click', () => {
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    confirmInp.type = show ? 'text' : 'password';
    
    btn.innerHTML = show 
      ? '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
      : '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
  });
</script>
@endsection