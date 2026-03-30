@extends('layouts.header')
@section('title', 'Galeria')

@section('content')

    @php
        $showDeleted = $showDeleted ?? false;
    @endphp

    <div class="container mb-4 mt-4">
        <h2 class="text-center">
            Galeria - {{ $tower->name ?? 'Torre' }}

            @can('towers.manage')
                <button class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
            @can('administrator.user')
                @if ($showDeleted)
                    <a href="{{ route('tower.gallery.index', $tower->id) }}" class="btn btn-secondary"><i
                            class="bi bi-house"></i></a>
                @else
                    <a href="{{ route('tower.gallery.index', $tower->id) . '?deleted_at=s' }}" class="btn btn-secondary"> <i
                            class="bi bi-trash"></i></a>
                @endif
            @endcan
    </div>
    </div>

    <div class="container">
        <div class="row g-3">
            @forelse($tower->gallery as $image)
                @php $url = route('tower.image.show', $image->id); @endphp
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card shadow-sm border-0" style="cursor:pointer;">
                        <img src="{{ $url }}" class="img-fluid rounded" style="height:180px; object-fit:cover;"
                            data-bs-toggle="modal" data-bs-target="#imageModal"
                            onclick="showImage('{{ $url }}', {{ $image->id }}, {{ $image->trashed() ? 'true' : 'false' }})">
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Nenhuma imagem {{ $showDeleted ? 'excluída' : 'cadastrada' }}.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal de visualização -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body d-flex flex-column align-items-center position-relative p-0">

                    <img id="modalImage" class="img-fluid rounded shadow-lg mb-3"
                        style="max-height:95vh; max-width:95vw; transition: all 0.3s ease;">

                    <div id="modalButtons" class="d-flex gap-3 justify-content-center w-100 mb-3">
                        <form id="deleteForm" method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg shadow">Excluir</button>
                        </form>
                        @can('administrator.user')
                            <form id="restoreForm" method="POST" style="display:none;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg shadow">Restaurar</button>
                            </form>

                            <form id="forceDeleteForm" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg shadow">Excluir Permanentemente</button>
                            </form>
                        @endcan

                        <button id="resetZoomBtn" type="button" class="btn btn-secondary btn-lg shadow">Reset Zoom</button>
                    </div>

                    <button type="button" class="btn btn-light position-absolute top-0 end-0 m-3 shadow"
                        data-bs-dismiss="modal" aria-label="Fechar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('tower.image.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tower_id" value="{{ $tower->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enviar imagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalImage = document.getElementById('modalImage');
        let currentScale = 1;
        const scaleStep = 0.1;
        const minScale = 0.5;
        const maxScale = 3;

        // Zoom com scroll
        modalImage.addEventListener('wheel', function(event) {
            event.preventDefault();
            currentScale += (event.deltaY < 0 ? scaleStep : -scaleStep);
            if (currentScale > maxScale) currentScale = maxScale;
            if (currentScale < minScale) currentScale = minScale;
            modalImage.style.transform = `scale(${currentScale})`;
        });

        // Reset zoom
        document.getElementById('resetZoomBtn').addEventListener('click', function() {
            currentScale = 1;
            modalImage.style.transform = 'scale(1)';
        });

        // Abrir imagem no modal
        function showImage(src, id, trashed, forceDeleted = false) {
            currentScale = 1;
            modalImage.style.transform = 'scale(1)';

            const deleteForm = document.getElementById('deleteForm');
            const restoreForm = document.getElementById('restoreForm');
            const forceDeleteForm = document.getElementById('forceDeleteForm');

            modalImage.src = src;

            if (forceDeleted) {
                deleteForm.style.display = 'none';
                restoreForm.style.display = 'none';
                forceDeleteForm.style.display = 'none';
            } else if (trashed) {
                restoreForm.style.display = 'inline-block';
                restoreForm.action = `/tower/image/${id}/restore`;

                forceDeleteForm.style.display = 'inline-block';
                forceDeleteForm.action = `/tower/image/${id}/force`;

                deleteForm.style.display = 'none';
            } else {
                deleteForm.style.display = 'inline-block';
                deleteForm.action = `/tower/image/${id}`;

                restoreForm.style.display = 'none';
                forceDeleteForm.style.display = 'none';
            }
        }
    </script>

    <style>
        .modal-backdrop.show {
            opacity: 0.85;
        }

        .card img:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        #modalButtons button {
            min-width: 160px;
        }
    </style>

@endsection
