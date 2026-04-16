@extends('layouts.header')

@section('title', 'FTTH - Boxes V2')

@section('content')

    <div class="container-fluid">

        <div class="container mb-1 mb-md-2 mt-1 mt-md-4">
            <h2 class="text-center">
                {{ $olt }}
                <a href="{{ route('pon.index') }}" class="btn dcm-btn-primary">
                    <i class="bi bi-house"></i>
                </a>
            </h2>
        </div>

        <div class="container-fluid">
            <div id="map" style="height: 80vh; border-radius:10px;"></div>
        </div>

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
            maxZoom: 19
        }).addTo(map);

        function createGpsIcon(box, color = '#2563eb') {
            return L.divIcon({
                className: '',
                html: `
            <div style="display:flex; flex-direction:column; align-items:center;">
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
                    CTO ${box.number} - ${box.info ?? ''}
                </div>

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

        // -------------------------
        // BOXES
        boxes.forEach(box => {

            if (!box.coordinates) return;

            let [lat, lng] = box.coordinates.split(',');

            // pegar cabo de entrada e saída
            let inputCable = cables.find(c => c.input_fiber_box?.id === box.id);
            let outputCable = cables.find(c => c.output_fiber_box?.id === box.id);

            // cor do box baseada no cabo
            let boxColor = inputCable?.color ?? outputCable?.color ?? '#2563eb';

            let marker = L.marker([parseFloat(lat), parseFloat(lng)], {
                icon: createGpsIcon(box, boxColor)
            }).addTo(map);

            marker.on('click', function() {
                window.location.href = '/ftth/fiber-box/' + box.id;
            });

        });

        // -------------------------
        // CABOS
        // -------------------------
        cables.forEach(cable => {

            if (!cable.input_fiber_box || !cable.output_fiber_box) return;

            let inputCoords = cable.input_fiber_box.coordinates;
            let outputCoords = cable.output_fiber_box.coordinates;

            if (!inputCoords || !outputCoords) return;

            let [lat1, lng1] = inputCoords.split(',');
            let [lat2, lng2] = outputCoords.split(',');

            let line = L.polyline([
                [parseFloat(lat1), parseFloat(lng1)],
                [parseFloat(lat2), parseFloat(lng2)]
            ], {
                color: cable.color ?? '#3388ff',
                weight: 4,
                opacity: 0.9
            }).addTo(map);

            line.bindTooltip(
                `CABO ${cable.id} (${cable.number_fiber} fibras)`, {
                    sticky: true
                }
            );

        });

        // -------------------------
        // CLICK MAPA (NOVA BOX)
        // -------------------------
        /*
        map.on('click', function(e) {

            document.getElementById('coordinates').value =
                e.latlng.lat + ',' + e.latlng.lng;

            let modal = new bootstrap.Modal(
                document.getElementById('modalCreateBox')
            );

            modal.show();

        });*/
    </script>

    {{-- MODAL --}}
    <div class="modal fade" id="modalCreateBox">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('fiberbox.store') }}">
                @csrf

                <input type="hidden" name="olt" value="{{ $olt }}">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Nova CTO</h5>
                    </div>

                    <div class="modal-body">

                        <div class="mb-2">
                            <label>Número</label>
                            <input name="number" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Info</label>
                            <input name="info" class="form-control">
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
