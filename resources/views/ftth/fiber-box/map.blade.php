@extends('layouts.header')

@section('title', 'FTTH - Boxes Maps')

@section('content')
    <link rel="stylesheet" href="/css/ftthmaps.css" type='text/css'>
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

    </div>

    {{-- LEAFLET --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>



    <script>
        let boxes = @json($boxes);
        let cables = @json($cables);

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
        | CAMADAS
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

        L.control.layers({
            "🛰️ Satélite": satLayer,
            "🗺️ Mapa": osmLayer
        }, null, {
            position: 'topright',
            collapsed: false
        }).addTo(map);

        /*
        |---------------------------------------
        | ICON GPS
        |---------------------------------------
        */
        function createGpsIcon(color = '#2563eb') {
            return L.divIcon({
                className: '',
                html: `
                <div class="gps-pin" style="--color:${color}">
                    <div class="gps-pin-inner"></div>
                </div>
            `,
                iconSize: [24, 24],
                iconAnchor: [12, 24]
            });
        }

        /*
        |---------------------------------------
        | BOXES
        |---------------------------------------
        */
        boxes.forEach(box => {

            if (!box.coordinates) return;

            let [lat, lng] = box.coordinates.split(',');

            let inputCable = cables.find(c => c.input_fiber_box?.id === box.id);
            let outputCable = cables.find(c => c.output_fiber_box?.id === box.id);

            let boxColor = inputCable?.color ?? outputCable?.color ?? '#2563eb';

            let marker = L.marker([parseFloat(lat), parseFloat(lng)], {
                icon: createGpsIcon(boxColor)
            }).addTo(map);

            /*
            |---------------------------------------
            | TOOLTIP (HOVER FUNCIONANDO)
            |---------------------------------------
            */
            marker.bindTooltip(
                `<strong>${box.number}</strong> - ${box.info ?? ''}`, {
                    direction: 'top',
                    sticky: true,
                    opacity: 0.95
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

            L.polyline([
                [parseFloat(lat1), parseFloat(lng1)],
                [parseFloat(lat2), parseFloat(lng2)]
            ], {
                color: cable.color ?? '#3388ff',
                weight: 4,
                opacity: 0.9
            }).addTo(map);

        });

        /*
        |---------------------------------------
        | CLICK NO MAPA
        |---------------------------------------
        */
        @can('ftth.create')
            map.on('click', function(e) {

                let lat = e.latlng.lat;
                let lng = e.latlng.lng;

                document.querySelector('#modalBox input[name="coordinates"]').value =
                    lat + ',' + lng;

                let modal = new bootstrap.Modal(
                    document.getElementById('modalBox')
                );

                modal.show();

            });
        @endcan
    </script>
    @include('ftth.modals.createbox')
@endsection
