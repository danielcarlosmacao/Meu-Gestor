@extends('layouts.header')
@section('title', 'Destinatários')
@section('content')

    <div class="container mt-5">
        <h2 class="text-center">Lista de Destinatários
            <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addRecipientModal">
                <i class="bi bi-plus-lg"></i>
            </button>
            <a href="{{ route('admin.recipients.logs') }}" class="btn dcm-btn-primary btn-sm">
                <i class="bi bi-journal-text"></i>
            </a>

        </h2>

        <table class="table table-striped mt-4">
            <thead class="bgc-primary">
                <tr>
                    <th>Nome</th>
                    <th>Tipo do envio</th>
                    <th>Número</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recipients as $recipient)
                    <tr>
                        <td>{{ $recipient->name }}</td>
                        <td>
    @if($recipient->references->isNotEmpty())
        {{ $recipient->references->map(fn($ref) => __('reference.' . $ref->name))->join(', ') }}
    @else
        -
    @endif
</td>

                        <td>{{ $recipient->number }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editRecipientModal{{ $recipient->id }}">
                                Editar
                            </button>

                            <form action="{{ route('admin.recipients.destroy', $recipient->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @include('admin.recipients.partials.form', ['recipient' => $recipient])
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $recipients->links() }}
        </div>
    </div>

    @include('admin.recipients.partials.form', ['recipient' => null]) <!-- Modal de Adição -->

@endsection
