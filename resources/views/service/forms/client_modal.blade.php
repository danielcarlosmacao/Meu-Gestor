@if (is_null($client))
    {{-- ============================================================
        MODAL NOVO CLIENTE
    ============================================================= --}}

    <div class="modal fade service-modal" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <form action="{{ route('service.clients.store') }}" method="POST" class="needs-validation" novalidate>

                @csrf

                <div class="modal-content">

                    <div class="modal-header">

                        <div class="service-modal-heading">

                            <span class="service-modal-icon">
                                <i class="bi bi-person-plus"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="addClientModalLabel">

                                    Novo Cliente

                                </h5>

                                <p class="service-modal-subtitle">
                                    Cadastre um novo cliente para utilização no módulo de serviços.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        @include('service.forms.client_fields')

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            <i class="bi bi-x-lg"></i>

                            Cancelar

                        </button>

                        <button type="submit" class="btn dcm-btn-primary">

                            <i class="bi bi-check-lg"></i>

                            Salvar Cliente

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>
@else
    {{-- ============================================================
        MODAL EDITAR CLIENTE
    ============================================================= --}}

    <div class="modal fade service-modal" id="editClientModal{{ $client->id }}" tabindex="-1"
        aria-labelledby="editClientModalLabel{{ $client->id }}" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <form action="{{ route('service.clients.update', $client->id) }}" method="POST" class="needs-validation"
                novalidate>

                @csrf
                @method('PUT')

                <div class="modal-content">

                    <div class="modal-header">

                        <div class="service-modal-heading">

                            <span class="service-modal-icon warning">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="editClientModalLabel{{ $client->id }}">

                                    Editar Cliente

                                </h5>

                                <p class="service-modal-subtitle">
                                    Atualize as informações do cliente.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        @include('service.forms.client_fields', [
                            'client' => $client,
                        ])

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            <i class="bi bi-x-lg"></i>

                            Cancelar

                        </button>

                        <button type="submit" class="btn dcm-btn-primary">

                            <i class="bi bi-check-lg"></i>

                            Atualizar

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>
@endif
