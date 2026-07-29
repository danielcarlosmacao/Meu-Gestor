@extends('layouts.header')

@section('title', 'FTTH - Boxes Maps')

@section('content')

    <link rel="stylesheet" href="/css/ftthmaps.css" type="text/css">

    <style>
        /*
            |--------------------------------------------------------------------------
            | CURSOR DOS CABOS
            |--------------------------------------------------------------------------
            */

        .leaflet-interactive.cable-line {
            cursor: pointer;
        }
    </style>

    <div class="container-fluid">

        <div class="container mb-1 mb-md-2 mt-1 mt-md-4">

            <h2 class="text-center">

                {{ $pon->info }}

                <a href="{{ route('pon.index') }}" class="btn dcm-btn-primary" title="Voltar para PONs">
                    <i class="bi bi-house"></i>
                </a>

                <a href="{{ route('fiberbox.index', ['pon' => $pon->id]) }}" class="btn dcm-btn-primary"
                    title="Visualizar lista">
                    <i class="bi bi-list"></i>
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

            /*
            |--------------------------------------------------------------------------
            | DADOS
            |--------------------------------------------------------------------------
            */

            const boxes = @json($boxes);
            const cables = @json($cables);

            /*
             * Modelo da URL de edição do trajeto.
             *
             * O ID temporário é substituído pelo ID real do cabo.
             */
            const cableRouteEditUrlTemplate = @json(route('cable.route.edit', ['cableId' => '__CABLE_ID__']));

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
                    .map(function(item) {
                        return Number(item.trim());
                    });

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

            function getCableRouteEditUrl(cableId) {

                return cableRouteEditUrlTemplate.replace(
                    '__CABLE_ID__',
                    String(cableId)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CENTRO DO MAPA
            |--------------------------------------------------------------------------
            */

            const firstBox = boxes.find(function(box) {
                return parseCoordinates(box.coordinates);
            });

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
            | CAMADAS
            |--------------------------------------------------------------------------
            */

            const satLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles © Esri',

                    /*
                     * Evita o erro "Map data not yet available".
                     */
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
                layers: [
                    satLayer
                ]
            });

            L.control.layers({
                    '🛰️ Satélite': satLayer,
                    '🗺️ Mapa': osmLayer
                },
                null, {
                    position: 'topright',
                    collapsed: false
                }
            ).addTo(map);

            /*
            |--------------------------------------------------------------------------
            | ÍCONE GPS
            |--------------------------------------------------------------------------
            */

            function createGpsIcon(color = '#2563eb') {

                return L.divIcon({
                    className: '',

                    html: `
                        <div
                            class="gps-pin"
                            style="--color:${color}"
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
                });
            }

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

                /*
                 * Procura um cabo ligado à box para definir a cor do marcador.
                 */
                const inputCable = cables.find(function(cable) {

                    return Number(cable.input_fiber_box?.id) ===
                        Number(box.id);
                });

                const outputCable = cables.find(function(cable) {

                    return Number(cable.output_fiber_box?.id) ===
                        Number(box.id);
                });

                let boxColor =
                    inputCable?.color ??
                    outputCable?.color ??
                    '#2563eb';

                /*
                 * CEO permanece preta.
                 */
                if (
                    box.info &&
                    String(box.info).toLowerCase().includes('ceo')
                ) {
                    boxColor = '#000000';
                }

                const marker = L.marker(
                    coordinates, {
                        icon: createGpsIcon(boxColor)
                    }
                ).addTo(map);

                marker.bindTooltip(
                    `
                        <strong>
                            ${escapeHtml(box.number)}
                        </strong>
                        -
                        ${escapeHtml(box.info ?? '')}
                    `, {
                        direction: 'top',
                        sticky: true,
                        opacity: 0.95
                    }
                );

                /*
                 * Abre a box.
                 */
                marker.on('click', function(event) {

                    if (event?.originalEvent) {
                        L.DomEvent.stopPropagation(event.originalEvent);
                    }

                    window.location.href =
                        '/ftth/fiber-box/' + box.id;
                });
            });

            /*
            |--------------------------------------------------------------------------
            | PONTOS DO TRAJETO SALVO
            |--------------------------------------------------------------------------
            */

            function getSavedRoutePoints(cable) {

                if (
                    !Array.isArray(cable.route_points) ||
                    cable.route_points.length < 2
                ) {
                    return [];
                }

                return [...cable.route_points]

                    /*
                     * Garante que os pontos estejam na ordem certa.
                     */
                    .sort(function(firstPoint, secondPoint) {

                        return (
                            Number(firstPoint.position) -
                            Number(secondPoint.position)
                        );
                    })

                    /*
                     * Converte latitude e longitude para números.
                     */
                    .map(function(point) {

                        return [
                            Number(point.latitude),
                            Number(point.longitude)
                        ];
                    })

                    /*
                     * Remove coordenadas inválidas.
                     */
                    .filter(function(point) {

                        return (
                            Number.isFinite(point[0]) &&
                            Number.isFinite(point[1])
                        );
                    });
            }

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

                /*
                 * Primeiro tenta carregar o trajeto editado.
                 */
                let cablePoints = getSavedRoutePoints(cable);

                /*
                 * Se não existir trajeto salvo, desenha uma linha reta.
                 */
                if (cablePoints.length < 2) {
                    cablePoints = [
                        inputCoordinates,
                        outputCoordinates
                    ];
                }

                const cableColor = cable.color ?? '#3388ff';

                /*
                 * Linha visível.
                 */
                const line = L.polyline(
                    cablePoints, {
                        color: cableColor,
                        weight: 5,
                        opacity: 0.9,
                        className: 'cable-line',
                        interactive: true
                    }
                ).addTo(map);

                /*
                 * Linha transparente mais larga.
                 *
                 * Ela facilita o clique no cabo sem alterar a aparência.
                 */
                const clickableLine = L.polyline(
                    cablePoints, {
                        color: cableColor,
                        weight: 20,
                        opacity: 0,
                        interactive: true,
                        className: 'cable-line'
                    }
                ).addTo(map);

                /*
                 * Informa se o cabo possui rota personalizada.
                 */
                const routeDescription =
                    Array.isArray(cable.route_points) &&
                    cable.route_points.length >= 2 ?
                    'Trajeto editado' :
                    'Trajeto em linha reta';

                const tooltipContent = `
                    <strong>
                        CABO ${escapeHtml(cable.id)}
                    </strong>

                    <br>

                    ${escapeHtml(cable.info ?? '')}

                    <br>

                    ${escapeHtml(cable.number_fiber ?? '')} fibras

                    <br>

                    <small>
                        ${escapeHtml(routeDescription)}
                    </small>

                    <br>

                    <small>
                        Clique para editar o trajeto
                    </small>
                `;

                line.bindTooltip(
                    tooltipContent, {
                        sticky: true,
                        opacity: 0.95
                    }
                );

                clickableLine.bindTooltip(
                    tooltipContent, {
                        sticky: true,
                        opacity: 0.95
                    }
                );

                /*
                 * Redireciona para a tela de edição do trajeto.
                 */
                function openCableRouteEditor(event) {

                    /*
                     * Impede que o clique continue para o mapa.
                     *
                     * Sem isso, ao clicar no cabo, o modal de criação
                     * de box poderia ser aberto junto.
                     */
                    if (event?.originalEvent) {
                        L.DomEvent.stopPropagation(
                            event.originalEvent
                        );

                        L.DomEvent.preventDefault(
                            event.originalEvent
                        );
                    }

                    window.location.href =
                        getCableRouteEditUrl(cable.id);
                }

                line.on(
                    'click',
                    openCableRouteEditor
                );

                clickableLine.on(
                    'click',
                    openCableRouteEditor
                );

                /*
                 * Mantém a linha visível acima da linha transparente.
                 */
                line.bringToFront();
            });

            /*
            |--------------------------------------------------------------------------
            | CLIQUE NO MAPA PARA CRIAR BOX
            |--------------------------------------------------------------------------
            */

            @can('ftth.create')

                map.on('click', function(event) {

                    const latitude = event.latlng.lat;
                    const longitude = event.latlng.lng;

                    const coordinateInput = document.querySelector(
                        '#modalBox input[name="coordinates"]'
                    );

                    if (!coordinateInput) {
                        console.error(
                            'Campo de coordenadas do modal não encontrado.'
                        );

                        return;
                    }

                    coordinateInput.value =
                        latitude + ',' + longitude;

                    const modalElement = document.getElementById(
                        'modalBox'
                    );

                    if (!modalElement) {
                        console.error(
                            'Modal de criação da box não encontrado.'
                        );

                        return;
                    }

                    const modal = bootstrap.Modal.getOrCreateInstance(
                        modalElement
                    );

                    modal.show();
                });
            @endcan

            /*
            |--------------------------------------------------------------------------
            | CORREÇÃO DO TAMANHO DO MAPA
            |--------------------------------------------------------------------------
            */

            setTimeout(function() {
                map.invalidateSize();
            }, 250);

        });
    </script>

    @include('ftth.modals.createbox')

@endsection
