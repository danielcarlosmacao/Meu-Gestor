@extends('layouts.header')

@section('title', 'FTTH - Boxes Maps')

@section('content')

    <div class="container-fluid">
        <div class="container mb-1 mb-md-2 mt-1 mt-md-4">
            <h2 class="text-center">
                {{ $pon->info }}
                <a href="{{ route('pon.index') }}" class="btn dcm-btn-primary ">
                    <i class="bi bi-house"></i>
                </a>
                <a href="{{ route('fiberbox.index', ['pon' => $pon->id]) }}" class="btn dcm-btn-primary ">
                    <i class="bi bi-list"></i>
                </a>

            </h2>
        </div>
        <div class="container-fluid">



            <div id="map" style="height: 80vh; border-radius:10px;"></div>

        </div>

        {{-- LEAFLET --}}
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            let boxes = @json($boxes);
            let cables = @json($cables);

            // fallback de centro do mapa
            let firstBox = boxes.find(b => b.coordinates);

            let defaultLat = -11.199777;
            let defaultLng = -61.516942;

            if (firstBox) {
                let coords = firstBox.coordinates.split(',');
                defaultLat = parseFloat(coords[0]);
                defaultLng = parseFloat(coords[1]);
            }

            let map = L.map('map').setView([defaultLat, defaultLng], 17);

            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles © Esri',
                maxZoom: 19 // evita "map data not available"
            }).addTo(map);

            /*
            |---------------------------------------
            | FUNÇÃO ICON GPS
            |---------------------------------------
            */
            function createGpsIcon(box, color = '#2563eb') {
                return L.divIcon({
                    className: '',
                    html: `
        <div style="display:flex; flex-direction:column; align-items:center;">
            
            <!-- LABEL -->
            <div style="
                background:${color};
                color:#fff;
                padding:3px 8px;
                border-radius:6px;
                font-size:11px;
                font-weight:bold;
                margin-bottom:2px;
                white-space:nowrap;
                box-shadow:0 2px 6px rgba(0,0,0,0.3);
            ">
                <strong>${box.number}</strong> - ${box.info ?? ''}
            </div>

            <!-- PIN -->
            <div style="
                width:18px;
                height:18px;
                background:${color};
                border-radius:50% 50% 50% 0;
                transform:rotate(-45deg);
                border:2px solid #fff;
                box-shadow:0 0 6px rgba(0,0,0,0.5);
            "></div>

        </div>
        `,
                    iconSize: [30, 40],
                    iconAnchor: [15, 30]
                });
            }

            /*
            |---------------------------------------
            | PLOTAR CTOs
            |---------------------------------------
            */
            boxes.forEach(box => {

                if (!box.coordinates) return;

                let [lat, lng] = box.coordinates.split(',');

                // pegar o cabo de entrada
                let inputCable = cables.find(c => c.input_fiber_box?.id === box.id);
                let outputCable = cables.find(c => c.output_fiber_box?.id === box.id);

                let boxColor = inputCable?.color ?? outputCable?.color ?? '#2563eb'; // fallback azul

                let marker = L.marker(
                    [parseFloat(lat), parseFloat(lng)], {
                        icon: createGpsIcon(box, boxColor)
                    }
                ).addTo(map);

                marker.on('click', function() {
                    window.location.href = '/ftth/fiber-box/' + box.id;
                });

            });
            /*
            |---------------------------------------
            | DESENHAR CABOS
            |---------------------------------------
            */
            cables.forEach(cable => {

                if (!cable.input_fiber_box || !cable.output_fiber_box) return;

                let inputCoords = cable.input_fiber_box.coordinates;
                let outputCoords = cable.output_fiber_box.coordinates;

                if (!inputCoords || !outputCoords) return;

                let [lat1, lng1] = inputCoords.split(',');
                let [lat2, lng2] = outputCoords.split(',');

                if (!lat1 || !lat2) return;

                let line = L.polyline([
                    [parseFloat(lat1), parseFloat(lng1)],
                    [parseFloat(lat2), parseFloat(lng2)]
                ], {
                    color: cable.color ?? '#3388ff',
                    weight: 4,
                    opacity: 0.9
                }).addTo(map);

                //tooltip no cabo
                line.bindTooltip(
                    `CABO ${cable.id} (${cable.number_fiber} fibras)`, {
                        sticky: true
                    }
                );

            });

            /*
            |---------------------------------------
            | CLICK NO MAPA → NOVA CTO
            |---------------------------------------
            */
            @can('ftth.create')
                map.on('click', function(e) {

                    let lat = e.latlng.lat;
                    let lng = e.latlng.lng;

                    document.getElementById('coordinates').value = lat + ',' + lng;

                    let modal = new bootstrap.Modal(document.getElementById('modalCreateBox'));
                    modal.show();

                });
            @endcan
        </script>

        {{-- ================= MODAL NOVA CTO ================= --}}
        <div class="modal fade" id="modalCreateBox">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('fiberbox.store') }}">
                    @csrf


                    <input type="hidden" name="pon_id" value="{{ $pon->id }}">

                    <div class="modal-content">

                        {{-- HEADER --}}
                        <div class="modal-header bgc-primary text-white">
                            <h5 class="modal-title fw-bold">Nova Caixa</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            {{-- NUMERO --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Número</label>
                                <span class="badge bg-success">
                                    Maior: {{ $nextnumber }}
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    <input name="number" type="number" class="form-control shadow-sm" step="1"
                                        min="1" required>


                                </div>
                            </div>

                            {{-- DESCRIÇÃO --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Descrição</label>
                                <input name="info" class="form-control shadow-sm">
                            </div>


                            <div class="mb-2">
                                <label>Coordenadas</label>
                                <input id="coordinates" name="coordinates" class="form-control" readonly>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary">Salvar</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    @endsection
