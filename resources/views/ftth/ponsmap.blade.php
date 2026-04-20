@extends('layouts.header')

@section('title', 'FTTH - Mapa da OLT')

@section('content')
    <link rel="stylesheet" href="/css/ftthmaps.css" type='text/css'>
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

        /*
        |---------------------------------------
        | CENTRO DO MAPA
        |---------------------------------------
        */
        let firstBox = boxes.find(b => b.coordinates);

        let defaultLat = -11.199777;
        let defaultLng = -61.516942;

        if (firstBox) {
            let coords = firstBox.coordinates.split(',');
            defaultLat = parseFloat(coords[0]);
            defaultLng = parseFloat(coords[1]);
        }

        /*
        |---------------------------------------
        | CAMADAS (SATÉLITE + MAPA)
        |---------------------------------------
        */
        let satLayer = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles © Esri',
                maxZoom: 19
            }
        );

        let osmLayer = L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }
        );

        /*
        |---------------------------------------
        | MAPA
        |---------------------------------------
        */
        let map = L.map('map', {
            center: [defaultLat, defaultLng],
            zoom: 17,
            layers: [satLayer]
        });

        /*
        |---------------------------------------
        | BOTÃO DE TROCA DE MAPA
        |---------------------------------------
        */
        let baseMaps = {
            "Satélite": satLayer,
            "Mapa": osmLayer
        };

        L.control.layers(baseMaps, null, {
            position: 'topright',
            collapsed: false
        }).addTo(map);

        /*
        |---------------------------------------
        | BOXES (PONTOS SIMPLES)
        |---------------------------------------
        */
        boxes.forEach(box => {

            if (!box.coordinates) return;

            let [lat, lng] = box.coordinates.split(',');

            let inputCable = cables.find(c => c.input_fiber_box?.id === box.id);
            let outputCable = cables.find(c => c.output_fiber_box?.id === box.id);

            let boxColor = inputCable?.color ?? outputCable?.color ?? '#2563eb';

            let marker = L.marker([parseFloat(lat), parseFloat(lng)], {
                icon: L.divIcon({
                    className: '',
                    html: `
            <div class="gps-pin" style="--color:${boxColor}">
                <div class="gps-pin-inner"></div>
            </div>
        `,
                    iconSize: [24, 24],
                    iconAnchor: [12, 24]
                })
            }).addTo(map);

            /*
            |---------------------------------------
            | TOOLTIP (HOVER INFO)
            |---------------------------------------
            */
            marker.bindTooltip(
                `${box.number} - ${box.info ?? ''}`, {
                    permanent: false,
                    direction: 'top',
                    sticky: true
                }
            );

            marker.on('click', function() {
                window.location.href = '/ftth/fiber-box/' + box.id;
            });

        });

        /*
        |---------------------------------------
        | CABOS
        |---------------------------------------
        */
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
    </script>

@endsection
