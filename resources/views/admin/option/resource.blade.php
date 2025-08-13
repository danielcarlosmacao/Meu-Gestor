@extends('layouts.header')
@section('title', 'Recursos do sistema')
@section('content')

    <div class="container py-4">
        <h2>Editar Recursos do sistema</h2>

        <form action="{{ route('options.resource.update') }}" method="POST" class="mt-4">
            @csrf

            <div class="form-group mb-3">
                <label for="hours_Generation">Horas de geração:</label>
                <input type="number" id="hours_Generation" name="hours_Generation" class="form-control"
                    value="{{ $options['hours_Generation'] ?? '5' }}">
                @error('hours_Generation')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="hours_autonomy">Horas de autonomia</label>
                <input type="number" id="hours_autonomy" name="hours_autonomy" class="form-control"
                    value="{{ $options['hours_autonomy'] ?? '48' }}">
                @error('hours_autonomy')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="pagination">Registro por paginas</label>
                <input type="number" id="pagination" name="pagination" class="form-control"
                    value="{{ $options['pagination'] ?? '20' }}">
                @error('pagination')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="whatsapp_method">Tipo do envio</label>
                <select name="whatsapp_method" class="form-select rounded-pill" >
                    <option value="GET" {{ isset($options['whatsapp_method']) && $options['whatsapp_method'] === 'GET' ? 'selected' : '' }}>GET</option>
                    <option value="POST" {{isset($options['whatsapp_method']) && $options['whatsapp_method'] === 'POST' ? 'selected' : '' }}>POST</option>
                </select>

            </div>
            <div class="form-group mb-3">
                <label for="whatsapp_ip">IP da API Whatsapp</label>
                <input type="text" id="whatsapp_ip" name="whatsapp_ip" class="form-control"
                    value="{{ $options['whatsapp_ip'] ?? '0.0.0.0:0' }}">
                @error('whatsapp_ip')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="whatsapp_user">Usuario da API</label>
                <input type="text" id="whatsapp_user" name="whatsapp_user" class="form-control"
                    value="{{ $options['whatsapp_user'] ?? 'admin' }}">
                @error('whatsapp_user')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label for="whatsapp_token">Token da API</label>
                <input type="text" id="whatsapp_token" name="whatsapp_token" class="form-control"
                    value="{{ $options['whatsapp_token'] ?? '1234567' }}">
                @error('whatsapp_token')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
            <a href="" class="btn btn-warning" role="button" aria-pressed="true"
                onclick="mostrarPopupConfirmacao(); return false;">Reparar Torres</a>
        </form>
        <br>





    </div>


@endsection
