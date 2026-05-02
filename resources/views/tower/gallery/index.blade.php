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

    <div class="container">
        <div class="row g-3">
            @forelse($tower->gallery as $image)
                @php $url = route('tower.image.show', $image->id); @endphp
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card shadow-sm border-0" style="cursor:pointer;">
                        @php
                            $isFile = $image->title && str_starts_with($image->title, 'file:');
                            $fileName = $isFile ? explode('file:', $image->title)[1] : null;
                        @endphp

                        @if ($isFile)
                            <div class="d-flex align-items-center justify-content-center bg-light rounded position-relative"
                                style="height:180px;">

                                <a href="{{ $url }}" download class="text-center text-decoration-none">
                                    <i class="bi bi-file-earmark-arrow-down" style="font-size:40px;"></i>
                                    <div class="small mt-2 px-2 text-truncate" style="max-width:100%;">
                                        {{ $fileName }}
                                    </div>
                                </a>

                                {{-- Botão excluir (somente arquivos) --}}
                                @can('towers.manage')
                                    @if (!$image->trashed())
                                        <form action="/tower/image/{{ $image->id }}" method="POST"
                                            class="position-absolute top-0 end-0 m-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                            </div>
                        @else
                            <img src="{{ $url }}" class="img-fluid rounded" style="height:180px; object-fit:cover;"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="showImage('{{ $url }}', {{ $image->id }}, {{ $image->trashed() ? 'true' : 'false' }})">
                        @endif
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

                    <!-- BOTÕES NO TOPO -->
                    <div class="position-absolute top-0 end-0 m-3 d-flex gap-2 z-3">
                        <button id="resetZoomBtn" type="button" class="btn btn-secondary shadow">
                            <i class="bi bi-zoom-out"></i>
                        </button>

                        <form id="deleteFormTop" method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger shadow">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                        @can('administrator.user')
                            <form id="restoreFormTop" method="POST" style="display:none;">
                                @csrf
                                <button type="submit" class="btn btn-success shadow">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>

                            <form id="forceDeleteFormTop" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger shadow">
                                    <i class="bi bi-x-octagon"></i>
                                </button>
                            </form>
                        @endcan

                        <button type="button" class="btn btn-light shadow" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>

                    </div>

                    <!-- IMAGEM -->
                    <img id="modalImage" class="img-fluid rounded shadow-lg"
                        style="max-height:95vh; max-width:95vw; cursor: zoom-in;">

                </div>
            </div>
        </div>
    </div>

    <!-- Modal de upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('tower.image.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enviar imagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="file" name="images[]" class="form-control" multiple>
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
        let posX = 0,
            posY = 0;
        let isDragging = false;
        const scaleStep = 0.15;
        const minScale = 0.5;
        const maxScale = 4;

        // Zoom com scroll
        modalImage.addEventListener('wheel', function(event) {
            event.preventDefault();

            const rect = modalImage.getBoundingClientRect();

            const offsetX = ((event.clientX - rect.left) / rect.width) * 100;
            const offsetY = ((event.clientY - rect.top) / rect.height) * 100;

            modalImage.style.transformOrigin = `${offsetX}% ${offsetY}%`;

            currentScale += (event.deltaY < 0 ? scaleStep : -scaleStep);

            if (currentScale > maxScale) currentScale = maxScale;
            if (currentScale < minScale) currentScale = minScale;

            modalImage.style.transform = `scale(${currentScale})`;
        });

        // Reset zoom
        document.getElementById('resetZoomBtn').addEventListener('click', function() {
            currentScale = 1;
            posX = 0;
            posY = 0;
            modalImage.style.transform = 'scale(1)';
        });

        // Função de abrir imagem no modal
        function showImage(src, id, trashed, forceDeleted = false) {
            currentScale = 1;
            posX = 0;
            posY = 0;
            modalImage.style.transform = 'scale(1)';
            modalImage.src = src;

            const deleteForm = document.getElementById('deleteFormTop');
            const restoreForm = document.getElementById('restoreFormTop');
            const forceDeleteForm = document.getElementById('forceDeleteFormTop');

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

        #modalImage {
            transition: transform 0.05s linear;
            cursor: zoom-in;
        }

        .hover-card {
            transition: 0.2s;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
    </style>

@endsection
