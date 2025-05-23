@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h1>Bem-vindo ao Sistema de Controle de Obra!</h1>
                    <p>Esta é a página principal do seu sistema.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
