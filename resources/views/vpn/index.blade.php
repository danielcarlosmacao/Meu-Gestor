@extends('layouts.header')

@section('content')
    <div class="container mt-4">

        <h2 class="mb-4">Gerenciar VPN</h2>


        <div class="card mb-4">
            <div class="card-header">
                Criar nova VPN
                <a href="{{ config('services.wireguard.url') }}"><i class="bi bi-shield-lock"></i></a>
            </div>

            <div class="card-body">

                <form method="POST" action="{{ route('api.vpn.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nome do cliente</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <button class="btn dcm-btn-primary">
                        Criar VPN
                    </button>

                </form>

            </div>
        </div>

        <div class="card">

            <div class="card-header">
                Clientes VPN
            </div>

            <div class="card-body p-0">

                <table class="table table-striped mb-0">

                    <thead class="table-dark">
                        <tr>
                            <th>Nome</th>
                            <th>IP</th>
                            <th style="width:120px">QRCode</th>
                            <th style="width:120px">Ação</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($clients as $client)
                            <tr>

                                <td>{{ $client['name'] }}</td>

                                <td>{{ $client['address'] }}</td>

                                <td>

                                    <button class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#qrModal"
                                        onclick="showQr('{{ $client['id'] }}','{{ $client['name'] }}')">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                </td>

                                <td>
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        onclick="setDeleteId('{{ $client['id'] }}','{{ $client['name'] }}')">
                                        Excluir
                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center">
                                    Nenhuma VPN criada
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- Modal QR Code -->

    <div class="modal fade" id="qrModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="vpnName">
                        VPN
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body text-center">

                    <img id="qrImage" width="280">

                </div>

                <div class="modal-footer justify-content-center">

                    <a id="downloadBtn" class="btn btn-success">
                        Baixar Configuração
                    </a>

                </div>

            </div>

        </div>

    </div>
    <!-- Modal confirmar exclusão -->

    <div class="modal fade" id="deleteModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST" id="deleteForm">

                    @csrf
                    @method('DELETE')

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Confirmar exclusão
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <p id="deleteText"></p>

                        <label class="form-label">Digite sua senha</label>

                        <input type="password" name="password" class="form-control" required>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-danger">
                            Excluir
                        </button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <script>
        function showQr(id, name) {

            let qrUrl = "/vpn/qrcode/" + id;

            let downloadUrl = "/vpn/download/" + id;

            document.getElementById('qrImage').src = qrUrl;

            document.getElementById('vpnName').innerText = "VPN: " + name;

            document.getElementById('downloadBtn').href = downloadUrl;

        }

        function setDeleteId(id, name) {

            let form = document.getElementById('deleteForm');

            let url = "{{ route('api.vpn.destroy', ':id') }}";
            url = url.replace(':id', id);

            form.action = url;

            document.getElementById('deleteText').innerHTML =
                "Tem certeza que deseja excluir a VPN: <strong class='text-danger'>" + name + "</strong>?";

        }
    </script>
@endsection
