@extends('layouts.header')
@section('title', 'Galeria')

@section('content')

    <div class="container mb-4 mt-4">
        <h2 class="text-center fw-bold">
            Galeria

            @can('towers.manage')
                <button class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>
    </div>

    <div class="container">
        <div class="row g-4">
            @forelse($images as $image)
                @php $url = route('tower.image.show', $image->id); @endphp
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card shadow-sm border-0 h-100 hover-card" style="cursor:pointer;">

                        <div class="position-relative">
                            <img src="{{ $url }}" class="img-fluid rounded-top"
                                style="height:180px; object-fit:cover;" data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="showImage('{{ $url }}', {{ $image->id }}, {{ $image->trashed() ? 'true' : 'false' }})">

                            @if ($image->trashed())
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    Excluída
                                </span>
                            @endif
                        </div>

                        <div class="card-body p-2 text-center">
                            <small class="fw-semibold text-dark text-truncate d-block">
                                <a href="{{ route('tower.gallery.index',$image->tower?->id)}}" class="text-decoration-none text-black">{{ $image->tower?->name ?? 'Sem torre' }}</a>
                            </small>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Nenhuma imagem {{ $showDeleted ? 'excluída' : 'cadastrada' }}.</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $images->links() }}
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
                                <button type="submit" class="btn btn-danger btn-lg shadow">
                                    Excluir Permanentemente
                                </button>
                            </form>
                        @endcan

                        <button id="resetZoomBtn" type="button" class="btn btn-secondary btn-lg shadow">
                            Reset Zoom
                        </button>
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
                <input type="hidden" name="tower_id" value="1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enviar imagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_info" class="form-label">Torre</label>
                            <select name="tower_id" id="tower_id" class="form-select mb-3 shadow-sm">
                                @foreach ($towes as $towe)
                                <option value="{{ $towe->id }}">{{ $towe->name }}</option>
                        
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_info" class="form-label">Imagem</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        </div>
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

        modalImage.addEventListener('wheel', function(event) {
            event.preventDefault();
            currentScale += (event.deltaY < 0 ? scaleStep : -scaleStep);
            if (currentScale > maxScale) currentScale = maxScale;
            if (currentScale < minScale) currentScale = minScale;
            modalImage.style.transform = `scale(${currentScale})`;
        });

        document.getElementById('resetZoomBtn').addEventListener('click', function() {
            currentScale = 1;
            modalImage.style.transform = 'scale(1)';
        });

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

        .card img {
            transition: transform 0.3s ease;
        }

        .card img:hover {
            transform: scale(1.05);
        }

        .hover-card {
            transition: all 0.2s ease-in-out;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        #modalButtons button {
            min-width: 160px;
        }
    </style>

@endsection
