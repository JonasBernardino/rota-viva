import L from 'leaflet';
import 'leaflet.heat';
import 'leaflet/dist/leaflet.css';

const DEFAULT_CENTER = [
    -7.1195,
    -34.8450,
];

const DEFAULT_ZOOM = 12;

function normalizePoints(points) {
    const max = Math.max(
        ...points.map(
            (point) =>
                Number(
                    point.intensity
                ) || 0,
        ),
        1,
    );

    return {
        max,

        heat: points.map(
            (point) => [
                Number(
                    point.latitude
                ),

                Number(
                    point.longitude
                ),

                Number(
                    point.intensity
                ),
            ],
        ),
    };
}

function initializeHeatmap() {
    const container =
        document.querySelector(
            '[data-dashboard-heatmap]'
        );

    const dataElement =
        document.querySelector(
            '#dashboard-heatmap-data'
        );

    if (
        !container
        || !dataElement
    ) {
        return;
    }

    let datasets;

    try {
        datasets =
            JSON.parse(
                dataElement.textContent
            );
    } catch (error) {
        console.error(
            'Rota Viva: dados inválidos do mapa de calor.',
            error,
        );

        return;
    }

    const map = L.map(
        container,
        {
            scrollWheelZoom: false,
        }
    ).setView(
        DEFAULT_CENTER,
        DEFAULT_ZOOM
    );

    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,

            attribution:
                '&copy; OpenStreetMap contributors',
        }
    ).addTo(map);

    let heatLayer = null;

    function render(
        type
    ) {
        const points =
            datasets[type] ?? [];

        if (heatLayer) {
            map.removeLayer(
                heatLayer
            );

            heatLayer = null;
        }

        if (!points.length) {
            return;
        }

        const normalized =
            normalizePoints(
                points
            );

        heatLayer = L.heatLayer(
            normalized.heat,
            {
                radius: 38,
                blur: 28,
                minOpacity: 0.35,
                max: normalized.max,
            }
        ).addTo(map);

        const bounds =
            L.latLngBounds(
                points.map(
                    (point) => [
                        Number(
                            point.latitude
                        ),

                        Number(
                            point.longitude
                        ),
                    ]
                )
            );

        if (bounds.isValid()) {
            map.fitBounds(
                bounds,
                {
                    padding: [
                        40,
                        40,
                    ],

                    maxZoom: 14,
                }
            );
        }
    }

    document
        .querySelectorAll(
            '[data-heatmap-type]'
        )
        .forEach(
            (button) => {
                button.addEventListener(
                    'click',
                    () => {
                        document
                            .querySelectorAll(
                                '[data-heatmap-type]'
                            )
                            .forEach(
                                (item) =>
                                    item.classList
                                        .remove(
                                            'is-active'
                                        )
                            );

                        button.classList
                            .add(
                                'is-active'
                            );

                        render(
                            button.dataset
                                .heatmapType
                        );
                    }
                );
            }
        );

    render('demand');
}

export {
    initializeHeatmap,
};