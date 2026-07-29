@extends('layouts.header')

@section('title', 'FTTH - Mapa da OLT')

@section('content')

    <link rel="stylesheet" href="/css/ftthmaps.css" type="text/css">

    <style>
        .leaflet-interactive.cable-line {
            cursor: pointer;
        }
    </style>

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

            <div id="map" style="height: 80vh; border-radius: 10px;"></div>

        </div>

    </div>

    {{-- LEAFLET --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const boxes = @json($boxes);
            const cables = @json($cables);

            const cableEditUrlTemplate = @json(route('cable.route.edit', ['cableId' => '__CABLE_ID__']));

            /*
            |--------------------------------------------------------------------------
            | CENTRO DO MAPA
            |--------------------------------------------------------------------------
            */

            const firstBox = boxes.find(box => box.coordinates);

            let defaultLat = -11.199777;
            let defaultLng = -61.516942;

            if (firstBox) {
                const coordinates = parseCoordinates(
                    firstBox.coordinates
                );

                if (coordinates) {
                    defaultLat = coordinates[0];
                    defaultLng = coordinates[1];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FUNÇÕES AUXILIARES
            |--------------------------------------------------------------------------
            */

            function parseCoordinates(value) {

                if (!value) {
                    return null;
                }

                const parts = String(value)
                    .split(',')
                    .map(item => Number(item.trim()));

                if (
                    parts.length !== 2 ||
                    !Number.isFinite(parts[0]) ||
                    !Number.isFinite(parts[1])
                ) {
                    return null;
                }

                return [
                    parts[0],
                    parts[1]
                ];
            }

            function escapeHtml(value) {

                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function getCableEditUrl(cableId) {

                return cableEditUrlTemplate.replace(
                    '__CABLE_ID__',
                    String(cableId)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CAMADAS
            |--------------------------------------------------------------------------
            */

            const satLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles © Esri',
                    maxNativeZoom: 18,
                    maxZoom: 19
                }
            );

            const osmLayer = L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxNativeZoom: 19,
                    maxZoom: 19
                }
            );

            /*
            |--------------------------------------------------------------------------
            | MAPA
            |--------------------------------------------------------------------------
            */

            const map = L.map('map', {
                center: [
                    defaultLat,
                    defaultLng
                ],
                zoom: 17,
                minZoom: 3,
                maxZoom: 19,
                layers: [satLayer]
            });

            L.control.layers({
                    'Satélite': satLayer,
                    'Mapa': osmLayer
                },
                null, {
                    position: 'topright',
                    collapsed: false
                }
            ).addTo(map);

            /*
            |--------------------------------------------------------------------------
            | BOXES
            |--------------------------------------------------------------------------
            */

            boxes.forEach(function(box) {

                const coordinates = parseCoordinates(
                    box.coordinates
                );

                if (!coordinates) {
                    return;
                }

                const inputCable = cables.find(function(cable) {
                    return cable.input_fiber_box?.id === box.id;
                });

                const outputCable = cables.find(function(cable) {
                    return cable.output_fiber_box?.id === box.id;
                });

                let boxColor =
                    inputCable?.color ??
                    outputCable?.color ??
                    '#2563eb';

                if (
                    box.info &&
                    box.info.toLowerCase().includes('ceo')
                ) {
                    boxColor = '#000000';
                }

                const marker = L.marker(
                    coordinates, {
                        icon: L.divIcon({
                            className: '',

                            html: `
                                <div
                                    class="gps-pin"
                                    style="--color:${boxColor}"
                                >
                                    <div class="gps-pin-inner"></div>
                                </div>
                            `,

                            iconSize: [
                                24,
                                24
                            ],

                            iconAnchor: [
                                12,
                                24
                            ]
                        })
                    }
                ).addTo(map);

                const ponInfo = box.pon?.info ?? '';
                const boxNumber = box.number ?? '';
                const boxInfo = box.info ?? '';

                marker.bindTooltip(
                    `
                        ${escapeHtml(ponInfo)}
                        ${escapeHtml(boxNumber)}
                        -
                        ${escapeHtml(boxInfo)}
                    `, {
                        permanent: false,
                        direction: 'top',
                        sticky: true
                    }
                );

                marker.on('click', function() {
                    window.location.href =
                        '/ftth/fiber-box/' + box.id;
                });
            });

            /*
            |--------------------------------------------------------------------------
            | CABOS
            |--------------------------------------------------------------------------
            */

            cables.forEach(function(cable) {

                if (
                    !cable.input_fiber_box ||
                    !cable.output_fiber_box
                ) {
                    return;
                }

                const inputCoordinates = parseCoordinates(
                    cable.input_fiber_box.coordinates
                );

                const outputCoordinates = parseCoordinates(
                    cable.output_fiber_box.coordinates
                );

                if (
                    !inputCoordinates ||
                    !outputCoordinates
                ) {
                    return;
                }

                let cablePoints = [];

                /*
                 * Quando existe um trajeto editado, usa os pontos salvos.
                 */
                if (
                    Array.isArray(cable.route_points) &&
                    cable.route_points.length >= 2
                ) {
                    cablePoints = [...cable.route_points]
                        .sort(function(first, second) {
                            return (
                                Number(first.position) -
                                Number(second.position)
                            );
                        })
                        .map(function(point) {
                            return [
                                Number(point.latitude),
                                Number(point.longitude)
                            ];
                        })
                        .filter(function(point) {
                            return (
                                Number.isFinite(point[0]) &&
                                Number.isFinite(point[1])
                            );
                        });
                }

                /*
                 * Sem trajeto salvo, usa uma linha reta.
                 */
                if (cablePoints.length < 2) {
                    cablePoints = [
                        inputCoordinates,
                        outputCoordinates
                    ];
                }

                const line = L.polyline(
                    cablePoints, {
                        color: cable.color ?? '#3388ff',
                        weight: 5,
                        opacity: 0.9,
                        className: 'cable-line'
                    }
                ).addTo(map);

                /*
                 * Aumenta a área clicável sem deixar uma linha larga visível.
                 */
                const clickLine = L.polyline(
                    cablePoints, {
                        color: '#000000',
                        weight: 18,
                        opacity: 0,
                        interactive: true
                    }
                ).addTo(map);

                const tooltipContent = `
                    <strong>
                        CABO ${escapeHtml(cable.id)}
                    </strong>
                    <br>
                    ${escapeHtml(cable.info ?? '')}
                    <br>
                    ${escapeHtml(cable.number_fiber ?? '')} fibras
                    <br>
                    <small>Clique para editar o trajeto</small>
                `;

                line.bindTooltip(
                    tooltipContent, {
                        sticky: true,
                        opacity: 0.95
                    }
                );

                clickLine.bindTooltip(
                    tooltipContent, {
                        sticky: true,
                        opacity: 0.95
                    }
                );

                function redirectToCableEdit() {
                    window.location.href = getCableEditUrl(
                        cable.id
                    );
                }

                line.on(
                    'click',
                    redirectToCableEdit
                );

                clickLine.on(
                    'click',
                    redirectToCableEdit
                );

                /*
                 * Mantém a linha visível acima da área transparente.
                 */
                line.bringToFront();
            });

            /*
            |--------------------------------------------------------------------------
            | CORRIGIR TAMANHO
            |--------------------------------------------------------------------------
            */

            setTimeout(function() {
                map.invalidateSize();
            }, 250);

        });
    </script>

@endsection
