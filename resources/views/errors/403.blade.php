@extends('layouts.error')

@section('title', 'Acesso Não Autorizado')

@section('styles')
    <style>
        .error-403-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #033D60 0%, #055a8c 60%, #0a7abf 100%);
            position: relative;
            overflow: hidden;
        }

        .error-403-page::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .error-403-page::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: -10%;
            width: 450px;
            height: 450px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 50px 60px;
            text-align: center;
            max-width: 520px;
            width: 90%;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .error-icon-wrapper {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #033D60, #0a7abf);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 30px rgba(3, 61, 96, 0.4);
        }

        .error-icon-wrapper i {
            font-size: 48px;
            color: #fff;
        }

        .error-code {
            font-size: 80px;
            font-weight: 800;
            line-height: 1;
            color: #033D60;
            letter-spacing: -2px;
            margin-bottom: 8px;
        }

        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a2a3a;
            margin-bottom: 12px;
        }

        .error-message {
            color: #6c757d;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .error-message strong {
            color: #033D60;
        }

        .btn-group-error {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-back-home {
            background: linear-gradient(135deg, #033D60, #0a7abf);
            color: #fff !important;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(3, 61, 96, 0.35);
        }

        .btn-back-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(3, 61, 96, 0.45);
            color: #fff !important;
        }

        .btn-go-back {
            background: transparent;
            color: #033D60 !important;
            border: 2px solid #033D60;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-go-back:hover {
            background: #033D60;
            color: #fff !important;
            transform: translateY(-2px);
        }

        .error-divider {
            height: 3px;
            width: 60px;
            background: linear-gradient(90deg, #033D60, #0a7abf);
            border-radius: 2px;
            margin: 16px auto 20px;
        }

        .logo-top {
            max-height: 36px;
            margin-bottom: 24px;
            opacity: 0.85;
        }
    </style>
@endsection

@section('content')
    <div class="error-403-page">
        <div class="error-card">

            <img src="{{ asset('assets/images/logo-dark.svg') }}" alt="SIBEM Web" class="logo-top">

            <div class="error-icon-wrapper">
                <i class="ti ti-lock"></i>
            </div>

            <div class="error-code">403</div>

            <div class="error-divider"></div>

            <h1 class="error-title">Acesso Não Autorizado</h1>

            <p class="error-message">
                Você não tem permissão para acessar esta área.<br>
                @if(!empty($exception->getMessage()))
                    <strong>{{ $exception->getMessage() }}</strong>
                @else
                    Caso acredite que isso é um erro, entre em contato com o administrador do sistema.
                @endif
            </p>

            <div class="btn-group-error">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-back-home">
                        <i class="ti ti-home"></i>
                        Ir para o Dashboard
                    </a>
                    <a href="javascript:history.back()" class="btn-go-back">
                        <i class="ti ti-arrow-left"></i>
                        Voltar
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-back-home">
                        <i class="ti ti-login"></i>
                        Fazer Login
                    </a>
                @endauth
            </div>

        </div>
    </div>
@endsection
