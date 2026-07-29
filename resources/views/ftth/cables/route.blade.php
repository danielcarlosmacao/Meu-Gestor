@extends('layouts.header')

@section('title', 'Trajeto do cabo')

@section('content')

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    {{-- Leaflet Draw --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        #routeMap {
            width: 100%;
            height: 70vh;
            min-height: 520px;
            border-radius: 10px;
            background: #d1d5db;
            overflow: hidden;
        }

        .route-info-box {
            height: 100%;
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
        }

        .route-status {
            min-height: 24px;
        }

        .route-pin {
            position: relative;
            width: 24px;
            height: 24px;
            transform: rotate(-45deg);
            border: 2px solid #fff;
            border-radius: 50% 50% 50% 0;
            background: var(--pin-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.45);
        }

        .route-pin::after {
            content: '';
            position: absolute;
            top: 6px;
            left: 6px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
        }

        .leaflet-container {
            font-family: inherit;
        }
    </style>

    <div class="container-fluid mt-3 mb-4">

        <div class="card shadow-sm border-0">

            <div class="card-header bgc-primary d-flex justify-content-between align-items-center">

                <div>
                    <strong>Trajeto do cabo:</strong>
                    {{ $cable->info }}
                </div>

                <a href="/ftth/ponsmap?olt=REDE" class="btn btn-sm dcm-btn-primary">
                    <i class="bi bi-arrow-left"></i>
                </a>

            </div>

            <div class="row mb-3">

                <div class="col-md-6 mb-2 mb-md-0">

                    <div class="route-info-box">

                        <small class="text-muted d-block">
                            CTO de origem
                        </small>

                        <strong>
                            {{ $cable->inputFiberBox->number ?? '' }}

                            @if ($cable->inputFiberBox?->number)
                                -
                            @endif

                            {{ $cable->inputFiberBox->info ?? 'Não informada' }}
                        </strong>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="route-info-box">

                        <small class="text-muted d-block">
                            CTO de destino
                        </small>

                        <strong>
                            {{ $cable->outputFiberBox->number ?? '' }}

                            @if ($cable->outputFiberBox?->number)
                                -
                            @endif

                            {{ $cable->outputFiberBox->info ?? 'Não informada' }}
                        </strong>

                    </div>

                </div>

            </div>

            <div id="routeMap"></div>

            <div class="route-status mt-2">
                <small id="routeStatus" class="text-muted"></small>
            </div>

            <form id="routeForm" method="POST" action="{{ route('cable.route.update', ['cableId' => $cable->id]) }}"
                class="mt-3">
                @csrf
                @method('PUT')

                <div id="routeInputs"></div>

                <div class="d-flex flex-wrap gap-2">

                    <button type="submit" class="btn btn-success" id="saveRouteButton">
                        <i class="bi bi-check-lg"></i>
                        Salvar trajeto
                    </button>

                    <button type="button" class="btn btn-outline-secondary" id="resetRouteButton">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Restaurar linha reta
                    </button>

                    @if ($cable->routePoints->isNotEmpty())
                        <button type="button" class="btn btn-outline-danger" id="removeSavedRouteButton">
                            <i class="bi bi-trash"></i>
                            Remover trajeto salvo
                        </button>
                    @endif

                </div>

            </form>

            <form id="deleteRouteForm" method="POST" action="{{ route('cable.route.destroy', ['cableId' => $cable->id]) }}"
                class="d-none">
                @csrf
                @method('DELETE')
            </form>

        </div>

    </div>

    </div>

    {{-- Leaflet --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- Leaflet Draw --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DADOS DO CABO
            |--------------------------------------------------------------------------
            */

            const inputBox = @json($cable->inputFiberBox);
            const outputBox = @json($cable->outputFiberBox);

            const savedPoints = @json(
                $cable->routePoints->sortBy('position')->map(function ($point) {
                        return [
                            'lat' => (float) $point->latitude,
                            'lng' => (float) $point->longitude,
                        ];
                    })->values());

            const cableColor = @json($cable->color ?? '#3388ff');

            /*
            |--------------------------------------------------------------------------
            | ELEMENTOS DO HTML
            |--------------------------------------------------------------------------
            */

            const routeForm = document.getElementById('routeForm');
            const routeInputs = document.getElementById('routeInputs');
            const routeStatus = document.getElementById('routeStatus');
            const resetRouteButton = document.getElementById('resetRouteButton');
            const saveRouteButton = document.getElementById('saveRouteButton');

            const removeSavedRouteButton = document.getElementById(
                'removeSavedRouteButton'
            );

            const deleteRouteForm = document.getElementById(
                'deleteRouteForm'
            );

            /*
            |--------------------------------------------------------------------------
            | CONFIGURAÇÃO DO ZOOM
            |--------------------------------------------------------------------------
            |
            | O mapa sempre inicia no zoom 17.
            | O fitBounds não será usado na abertura.
            | Isso evita o mapa abrir excessivamente aproximado.
            |
            */

            const INITIAL_ZOOM = 17;
            const MAX_MAP_ZOOM = 19;
            const MAX_NATIVE_SATELLITE_ZOOM = 18;

            /*
            |--------------------------------------------------------------------------
            | COORDENADAS
            |--------------------------------------------------------------------------
            */

            function parseCoordinates(box) {

                if (!box || !box.coordinates) {
                    return null;
                }

                const coordinates = String(box.coordinates)
                    .split(',')
                    .map(value => Number(value.trim()));

                if (
                    coordinates.length !== 2 ||
                    !Number.isFinite(coordinates[0]) ||
                    !Number.isFinite(coordinates[1])
                ) {
                    return null;
                }

                return [
                    coordinates[0],
                    coordinates[1]
                ];
            }

            const inputCoordinates = parseCoordinates(inputBox);
            const outputCoordinates = parseCoordinates(outputBox);

            const defaultCoordinates = [
                -11.199777,
                -61.516942
            ];

            /*
            |--------------------------------------------------------------------------
            | CENTRO DO MAPA
            |--------------------------------------------------------------------------
            */

            function calculateCenter() {

                if (inputCoordinates && outputCoordinates) {

                    return [
                        (
                            inputCoordinates[0] +
                            outputCoordinates[0]
                        ) / 2,

                        (
                            inputCoordinates[1] +
                            outputCoordinates[1]
                        ) / 2
                    ];
                }

                if (inputCoordinates) {
                    return inputCoordinates;
                }

                if (outputCoordinates) {
                    return outputCoordinates;
                }

                return defaultCoordinates;
            }

            const mapCenter = calculateCenter();

            /*
            |--------------------------------------------------------------------------
            | CAMADAS
            |--------------------------------------------------------------------------
            */

            const satelliteLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles © Esri',

                    /*
                     * A Esri possui imagens nativas até aproximadamente o zoom 18
                     * nessa região.
                     *
                     * No zoom 19 o Leaflet reaproveita o zoom 18 ampliado,
                     * evitando "Map data not yet available".
                     */
                    maxNativeZoom: MAX_NATIVE_SATELLITE_ZOOM,
                    maxZoom: MAX_MAP_ZOOM
                }
            );

            const osmLayer = L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxNativeZoom: 19,
                    maxZoom: MAX_MAP_ZOOM
                }
            );

            /*
            |--------------------------------------------------------------------------
            | MAPA
            |--------------------------------------------------------------------------
            */

            const map = L.map('routeMap', {
                center: mapCenter,
                zoom: INITIAL_ZOOM,
                minZoom: 3,
                maxZoom: MAX_MAP_ZOOM,
                layers: [satelliteLayer],
                zoomControl: true
            });

            /*
             * Força o zoom inicial.
             *
             * Não usamos fitBounds aqui porque duas CTOs próximas fazem
             * o Leaflet aproximar demais.
             */
            map.setView(
                mapCenter,
                INITIAL_ZOOM, {
                    animate: false
                }
            );

            L.control.layers({
                    '🛰️ Satélite': satelliteLayer,
                    '🗺️ Mapa': osmLayer
                },
                null, {
                    position: 'topright',
                    collapsed: false
                }
            ).addTo(map);

            /*
            |--------------------------------------------------------------------------
            | ÍCONES DAS CTOS
            |--------------------------------------------------------------------------
            */

            function createRouteIcon(color) {

                return L.divIcon({
                    className: '',

                    html: `
                        <div
                            class="route-pin"
                            style="--pin-color:${color}"
                        ></div>
                    `,

                    iconSize: [
                        24,
                        24
                    ],

                    iconAnchor: [
                        12,
                        24
                    ]
                });
            }

            function escapeHtml(value) {

                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function addBoxMarker(
                coordinates,
                box,
                color,
                prefix
            ) {
                if (!coordinates) {
                    return;
                }

                const number = box?.number ?? '';
                const info = box?.info ?? '';

                const description = [
                        number,
                        info
                    ]
                    .filter(Boolean)
                    .join(' - ');

                const tooltip = `
                    <strong>${escapeHtml(prefix)}</strong>
                    <br>
                    ${escapeHtml(description || 'CTO')}
                `;

                L.marker(
                        coordinates, {
                            icon: createRouteIcon(color)
                        }
                    )
                    .addTo(map)
                    .bindTooltip(
                        tooltip, {
                            direction: 'top',
                            sticky: true,
                            opacity: 0.95
                        }
                    );
            }

            addBoxMarker(
                inputCoordinates,
                inputBox,
                '#198754',
                'Origem'
            );

            addBoxMarker(
                outputCoordinates,
                outputBox,
                '#dc3545',
                'Destino'
            );

            /*
            |--------------------------------------------------------------------------
            | CAMADA EDITÁVEL
            |--------------------------------------------------------------------------
            */

            const drawnItems = new L.FeatureGroup();

            map.addLayer(drawnItems);

            let cableLine = null;

            /*
            |--------------------------------------------------------------------------
            | PONTOS DO TRAJETO
            |--------------------------------------------------------------------------
            */

            function getStraightRoutePoints() {

                const points = [];

                if (inputCoordinates) {
                    points.push(inputCoordinates);
                }

                if (outputCoordinates) {
                    points.push(outputCoordinates);
                }

                return points;
            }

            function getInitialRoutePoints() {

                if (
                    Array.isArray(savedPoints) &&
                    savedPoints.length >= 2
                ) {
                    return savedPoints
                        .map(point => [
                            Number(point.lat),
                            Number(point.lng)
                        ])
                        .filter(point => {
                            return (
                                Number.isFinite(point[0]) &&
                                Number.isFinite(point[1])
                            );
                        });
                }

                return getStraightRoutePoints();
            }

            /*
            |--------------------------------------------------------------------------
            | LINHA DO CABO
            |--------------------------------------------------------------------------
            */

            function createCableLine(points) {

                if (
                    !Array.isArray(points) ||
                    points.length < 2
                ) {
                    return null;
                }

                return L.polyline(
                    points, {
                        color: cableColor,
                        weight: 5,
                        opacity: 0.95
                    }
                );
            }

            function replaceCableLine(points) {

                drawnItems.clearLayers();

                cableLine = createCableLine(points);

                if (!cableLine) {
                    clearRouteInputs();
                    updateStatus(0);
                    return;
                }

                drawnItems.addLayer(cableLine);

                updateRouteInputs(cableLine);

                /*
                 * Apenas centraliza no meio da linha.
                 * Mantém o zoom fixo em 17.
                 */
                centerMapOnLine(cableLine);
            }

            /*
            |--------------------------------------------------------------------------
            | CENTRALIZAR SEM ESTOURAR O ZOOM
            |--------------------------------------------------------------------------
            */

            function centerMapOnLine(line) {

                if (!line) {
                    map.setView(
                        mapCenter,
                        INITIAL_ZOOM, {
                            animate: false
                        }
                    );

                    return;
                }

                const bounds = line.getBounds();

                if (!bounds || !bounds.isValid()) {
                    map.setView(
                        mapCenter,
                        INITIAL_ZOOM, {
                            animate: false
                        }
                    );

                    return;
                }

                /*
                 * Usa apenas o centro do trajeto.
                 * Não usa fitBounds.
                 */
                map.setView(
                    bounds.getCenter(),
                    INITIAL_ZOOM, {
                        animate: false
                    }
                );
            }

            replaceCableLine(
                getInitialRoutePoints()
            );

            /*
            |--------------------------------------------------------------------------
            | CONTROLE DE DESENHO
            |--------------------------------------------------------------------------
            */

            const drawControl = new L.Control.Draw({
                position: 'topright',

                draw: {
                    polygon: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,

                    polyline: {
                        allowIntersection: true,

                        shapeOptions: {
                            color: cableColor,
                            weight: 5,
                            opacity: 0.95
                        }
                    }
                },

                edit: {
                    featureGroup: drawnItems,
                    edit: true,
                    remove: true
                }
            });

            map.addControl(drawControl);

            /*
            |--------------------------------------------------------------------------
            | NOVA LINHA
            |--------------------------------------------------------------------------
            */

            map.on(
                L.Draw.Event.CREATED,
                function(event) {

                    drawnItems.clearLayers();

                    cableLine = event.layer;

                    cableLine.setStyle({
                        color: cableColor,
                        weight: 5,
                        opacity: 0.95
                    });

                    drawnItems.addLayer(cableLine);

                    updateRouteInputs(cableLine);

                    /*
                     * Centraliza mantendo o zoom 17.
                     */
                    centerMapOnLine(cableLine);
                }
            );

            /*
            |--------------------------------------------------------------------------
            | LINHA EDITADA
            |--------------------------------------------------------------------------
            */

            map.on(
                L.Draw.Event.EDITED,
                function(event) {

                    event.layers.eachLayer(function(layer) {

                        cableLine = layer;

                        updateRouteInputs(layer);

                    });
                }
            );

            /*
            |--------------------------------------------------------------------------
            | LINHA EXCLUÍDA DO MAPA
            |--------------------------------------------------------------------------
            */

            map.on(
                L.Draw.Event.DELETED,
                function() {

                    cableLine = null;

                    clearRouteInputs();

                    updateStatus(0);
                }
            );

            /*
            |--------------------------------------------------------------------------
            | EXTRAIR PONTOS DA LINHA
            |--------------------------------------------------------------------------
            */

            function getLinePoints(layer) {

                if (
                    !layer ||
                    typeof layer.getLatLngs !== 'function'
                ) {
                    return [];
                }

                const result = [];

                function collect(items) {

                    if (!Array.isArray(items)) {
                        return;
                    }

                    items.forEach(function(item) {

                        if (Array.isArray(item)) {
                            collect(item);
                            return;
                        }

                        const latitude = Number(item?.lat);
                        const longitude = Number(item?.lng);

                        if (
                            Number.isFinite(latitude) &&
                            Number.isFinite(longitude)
                        ) {
                            result.push({
                                lat: latitude,
                                lng: longitude
                            });
                        }
                    });
                }

                collect(
                    layer.getLatLngs()
                );

                return result;
            }

            /*
            |--------------------------------------------------------------------------
            | CAMPOS OCULTOS
            |--------------------------------------------------------------------------
            */

            function clearRouteInputs() {
                routeInputs.innerHTML = '';
            }

            function createHiddenInput(
                name,
                value
            ) {
                const input = document.createElement('input');

                input.type = 'hidden';
                input.name = name;
                input.value = value;

                return input;
            }

            function updateRouteInputs(layer) {

                clearRouteInputs();

                const points = getLinePoints(layer);

                points.forEach(function(point, index) {

                    routeInputs.appendChild(
                        createHiddenInput(
                            `points[${index}][lat]`,
                            point.lat.toFixed(7)
                        )
                    );

                    routeInputs.appendChild(
                        createHiddenInput(
                            `points[${index}][lng]`,
                            point.lng.toFixed(7)
                        )
                    );
                });

                updateStatus(points.length);

                console.log(
                    'Pontos preparados para salvar:',
                    points
                );
            }

            function updateStatus(totalPoints) {

                if (totalPoints <= 0) {
                    routeStatus.textContent =
                        'Nenhum trajeto definido.';

                    return;
                }

                routeStatus.textContent =
                    `${totalPoints} ponto(s) preparados para salvar.`;
            }

            /*
            |--------------------------------------------------------------------------
            | RESTAURAR LINHA RETA
            |--------------------------------------------------------------------------
            */

            resetRouteButton.addEventListener(
                'click',
                function() {

                    const straightPoints = getStraightRoutePoints();

                    if (straightPoints.length < 2) {
                        alert(
                            'As CTOs de origem e destino precisam ter coordenadas válidas.'
                        );

                        return;
                    }

                    replaceCableLine(straightPoints);
                }
            );

            /*
            |--------------------------------------------------------------------------
            | SALVAR TRAJETO
            |--------------------------------------------------------------------------
            */

            routeForm.addEventListener(
                'submit',
                function(event) {

                    if (!cableLine) {
                        event.preventDefault();

                        alert(
                            'Desenhe ou mantenha um trajeto antes de salvar.'
                        );

                        return;
                    }

                    updateRouteInputs(cableLine);

                    const points = getLinePoints(cableLine);

                    if (points.length < 2) {
                        event.preventDefault();

                        alert(
                            'O trajeto precisa ter pelo menos dois pontos.'
                        );

                        return;
                    }

                    const latitudeInputs = routeInputs.querySelectorAll(
                        'input[name$="[lat]"]'
                    );

                    const longitudeInputs = routeInputs.querySelectorAll(
                        'input[name$="[lng]"]'
                    );

                    if (
                        latitudeInputs.length !== points.length ||
                        longitudeInputs.length !== points.length
                    ) {
                        event.preventDefault();

                        alert(
                            'Não foi possível preparar os pontos para o envio.'
                        );

                        return;
                    }

                    saveRouteButton.disabled = true;

                    saveRouteButton.innerHTML = `
                        <span
                            class="spinner-border spinner-border-sm me-1"
                        ></span>
                        Salvando...
                    `;
                }
            );

            /*
            |--------------------------------------------------------------------------
            | REMOVER TRAJETO SALVO
            |--------------------------------------------------------------------------
            */

            if (
                removeSavedRouteButton &&
                deleteRouteForm
            ) {
                removeSavedRouteButton.addEventListener(
                    'click',
                    function() {

                        const confirmed = confirm(
                            'Deseja remover o trajeto salvo deste cabo?'
                        );

                        if (!confirmed) {
                            return;
                        }

                        deleteRouteForm.submit();
                    }
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CORRIGIR TAMANHO DO MAPA
            |--------------------------------------------------------------------------
            */

            setTimeout(function() {

                map.invalidateSize();

                /*
                 * Reforça o zoom depois que o mapa termina de carregar.
                 */
                if (cableLine) {
                    centerMapOnLine(cableLine);
                } else {
                    map.setView(
                        mapCenter,
                        INITIAL_ZOOM, {
                            animate: false
                        }
                    );
                }

            }, 400);

            window.addEventListener(
                'resize',
                function() {
                    map.invalidateSize();
                }
            );

        });
    </script>

@endsection
