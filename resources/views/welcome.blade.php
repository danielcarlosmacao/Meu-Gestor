@extends('layouts.header')
@section('title', 'BEM-VINDO')
@section('content')

<style>
  .info-card {
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
    border: none;
  }

  .info-card:hover {
    transform: translateY(-5px);
  }

  .info-icon {
    font-size: 2.2rem;
    margin-bottom: 10px;
  }

  .progress {
    height: 8px;
    border-radius: 5px;
  }

  .card-title {
    font-size: 1.1rem;
    font-weight: 600;
  }

  .card-text {
    font-size: 0.95rem;
  }

  .bg-sistema { background-color: #f3f4f6; }
  .bg-memoria { background-color: #e0f7fa; }
  .bg-disco { background-color: #fff3e0; }
  .bg-software { background-color: #ede7f6; }
  .bg-php { background-color: #e8f5e9; }
  .bg-cliente { background-color: #fce4ec; }
</style>

<div class="container py-4">
  <div class="row g-4">
    <h1>Seja bem-vindo ao sistema de gestao</h1>
  </div>
</div>

@endsection