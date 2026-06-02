@extends('layouts.base')
@section('title', 'Lupa Password | Macacino')

@section('head')
<style>
  body { background-color: #0f172a; margin: 0; overflow: hidden; }
  .focus-auth-wrapper { position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
  .focus-breathing-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e3a8a 0%, #0f172a 80%); opacity: 0.2; z-index: -1; animation: breatheAuth 10s infinite ease-in-out; will-change: opacity, transform; }
  @keyframes breatheAuth { 0% { opacity: 0.2; transform: scale(1); } 50% { opacity: 0.6; transform: scale(1.1); } 100% { opacity: 0.2; transform: scale(1); } }
  .focus-auth-container { position: relative; z-index: 1; width: 100%; max-width: 340px; text-align: center; padding: 20px; }
  .focus-auth-title {font-family: 'Inter', sans-serif; font-size: 32px; font-weight: 700; color: #f1f5f9; margin-bottom: 16px; letter-spacing: 0.5px; }
  
  .focus-desc { font-family: 'Inter', sans-serif; font-size: 14px; color: rgba(255, 255, 255, 0.6); line-height: 1.6; margin-bottom: 32px; }

  .focus-input-group { position: relative; margin-bottom: 24px; }
  .focus-input { width: 100%; background: transparent; border: none; border-bottom: 1.5px solid rgba(255, 255, 255, 0.15); padding: 12px 0; font-size: 16px; color: #f1f5f9; text-align: center; outline: none; transition: border-color 0.3s ease; font-family: 'Inter', sans-serif; }
  .focus-input::placeholder { color: rgba(255, 255, 255, 0.25); }
  .focus-input:focus { border-bottom-color: rgba(255, 255, 255, 0.5); }

  .focus-submit-btn { width: 100%; background: rgba(255, 255, 255, 0.1); color: #f1f5f9; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; font-size: 15px; border-radius: 30px; cursor: pointer; transition: all 0.3s ease; font-family: 'Inter', sans-serif; backdrop-filter: blur(4px); font-weight: 600; margin-bottom: 20px;}
  .focus-submit-btn:hover { background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.4); transform: translateY(-1px); }
  
  .focus-hint { font-size: 13px; color: rgba(255, 255, 255, 0.4); font-family: 'Inter', sans-serif; }
  .focus-hint a { color: rgba(255, 255, 255, 0.8); text-decoration: none; font-weight: 600; transition: all 0.2s; border-bottom: 1px solid transparent; }
  .focus-hint a:hover { color: #fff; border-bottom-color: rgba(255, 255, 255, 0.6); }

  .focus-alert { font-size: 14px; margin-bottom: 24px; padding: 12px 16px; border-radius: 8px; font-weight: 600; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
  .focus-alert.error { color: #ffffff; background: #e63946; border: 1px solid #c1121f; }
  .focus-alert.success { color: #ffffff; background: #2a9d8f; border: 1px solid #21867a; }
</style>
@endsection

@section('body')
<div class="focus-auth-wrapper">
  <div class="focus-breathing-bg"></div>
  
  <div class="focus-auth-container">
    <h1 class="focus-auth-title">Pulihkan Akses</h1>
    <p class="focus-desc">Lupa kata sandi? Tidak masalah. Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi.</p>

    @if (session('status'))
        <div class="focus-alert success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="focus-alert error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      
      <div class="focus-input-group">
        <input type="email" name="email" class="focus-input" required placeholder="Alamat Email Anda" value="{{ old('email') }}" autocomplete="email" autofocus>
      </div>

      <button type="submit" class="focus-submit-btn">Kirim Tautan Reset</button>
      
      <p class="focus-hint">
        Ingat kata sandi Anda? <a href="{{ route('login') }}">Kembali ke Login</a>.
      </p>
    </form>
  </div>
</div>
@endsection