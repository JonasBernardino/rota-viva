import L from "leaflet";
import "leaflet/dist/leaflet.css";

const DEFAULT_ZOOM = 14;

/**
 * Marker das paradas do roteiro.
 */
function createNumberedIcon(number, variant = "default") {
    return L.divIcon({
        className: "route-map-marker-wrapper",
        html: `
            <div class="route-map-marker route-map-marker--${variant}">
                ${number}
            </div>
        `,
        iconSize: [38, 38],
        iconAnchor: [19, 19],
        popupAnchor: [0, -20],
    });
}

/**
 * Marker da localização atual do visitante.
 */
function createUserLocationIcon() {
    return L.divIcon({
        className: "route-map-marker-wrapper",
        html: `
            <div class="route-map-user-location">
                <span class="route-map-user-location__pulse"></span>
                <span class="route-map-user-location__point"></span>
            </div>
        `,
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });
}

/**
 * Normaliza latitude/longitude vindas do Blade.
 */
function normalizeStops(stops) {
    return stops
        .map((stop) => ({
            ...stop,
            latitude: Number(stop.latitude),
            longitude: Number(stop.longitude),
        }))
        .filter(
            (stop) =>
                Number.isFinite(stop.latitude) &&
                Number.isFinite(stop.longitude),
        );
}

/**
 * Link externo para navegação pelo Google Maps.
 *
 * Não informamos origem.
 * O Google Maps utiliza a posição atual do dispositivo
 * quando disponível.
 */
function createGoogleMapsUrl(stop) {
    const destination = `${stop.latitude},${stop.longitude}`;

    return (
        "https://www.google.com/maps/dir/" +
        `?api=1&destination=${encodeURIComponent(destination)}`
    );
}

/**
 * Link externo para navegação pelo Waze.
 */
function createWazeUrl(stop) {
    const coordinates = `${stop.latitude},${stop.longitude}`;

    return (
        "https://waze.com/ul" +
        `?ll=${encodeURIComponent(coordinates)}` +
        "&navigate=yes" +
        "&utm_source=rota_viva"
    );
}

/**
 * Popup de uma parada.
 */
function createPopup(stop) {
    const cost = Number(stop.cost ?? 0).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
    });

    return `
        <div class="route-map-popup">

            <strong>
                ${stop.name}
            </strong>

            ${stop.category ? `<span>${stop.category}</span>` : ""}

            <small>
                ${stop.duration} min · ${cost}
            </small>

            <div class="route-map-popup__actions">

                <a
                    href="${createGoogleMapsUrl(stop)}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Google Maps
                </a>

                <a
                    href="${createWazeUrl(stop)}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Waze
                </a>

            </div>

        </div>
    `;
}

/**
 * Camada visual do OpenStreetMap.
 */
function addTileLayer(map) {
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);
}

/**
 * Busca localização atual do visitante.
 */
function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error("Geolocalização não suportada."));

            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    latitude: position.coords.latitude,

                    longitude: position.coords.longitude,

                    accuracy: position.coords.accuracy,
                });
            },

            (error) => {
                reject(error);
            },

            {
                enableHighAccuracy: true,

                /**
                 * Não deixa o usuário esperando
                 * indefinidamente pelo GPS.
                 */
                timeout: 10000,

                /**
                 * Permite posição recente de até
                 * 1 minuto.
                 */
                maximumAge: 60000,
            },
        );
    });
}

/**
 * Coloca a posição atual no mapa.
 */
function renderUserLocation(map, location) {
    const coordinates = [location.latitude, location.longitude];

    const marker = L.marker(coordinates, {
        icon: createUserLocationIcon(),

        zIndexOffset: 1000,
    }).addTo(map).bindPopup(`
            <div class="route-map-popup">

                <strong>
                    Você está aqui
                </strong>

                <span>
                    Ponto de partida
                    da sua experiência
                </span>

            </div>
        `);

    /**
     * Representa aproximadamente a precisão
     * fornecida pelo navegador.
     */
    if (Number.isFinite(location.accuracy) && location.accuracy < 1000) {
        L.circle(coordinates, {
            radius: location.accuracy,

            weight: 1,

            opacity: 0.25,

            fillOpacity: 0.06,
        }).addTo(map);
    }

    return marker;
}

/**
 * Renderização da rota original.
 *
 * Não desenhamos linha.
 */
function renderStandardRoute(map, stops) {
    const coordinates = [];

    stops.forEach((stop, index) => {
        const coordinate = [stop.latitude, stop.longitude];

        coordinates.push(coordinate);

        L.marker(coordinate, {
            icon: createNumberedIcon(index + 1),
        })
            .addTo(map)
            .bindPopup(createPopup(stop));
    });

    return coordinates;
}

/**
 * Renderização da rota adaptada.
 */
function renderAdaptedRoute(map, stops) {
    const activeStops = stops
        .filter((stop) => stop.action !== "REMOVED")
        .sort((a, b) => Number(a.position ?? 0) - Number(b.position ?? 0));

    const coordinates = [];

    activeStops.forEach((stop, index) => {
        const coordinate = [stop.latitude, stop.longitude];

        coordinates.push(coordinate);

        const variant = stop.action === "ADDED" ? "added" : "kept";

        L.marker(coordinate, {
            icon: createNumberedIcon(index + 1, variant),
        })
            .addTo(map)
            .bindPopup(createPopup(stop));
    });

    /**
     * Também mostramos os locais removidos,
     * apenas como contexto.
     */
    stops
        .filter((stop) => stop.action === "REMOVED")
        .forEach((stop) => {
            L.marker([stop.latitude, stop.longitude], {
                icon: createNumberedIcon("×", "removed"),
            }).addTo(map).bindPopup(`
                    <div class="route-map-popup">

                        <strong>
                            ${stop.name}
                        </strong>

                        <span>
                            Removido da rota
                        </span>

                        <small>
                            Incompatível com
                            a condição atual.
                        </small>

                    </div>
                `);
        });

    return coordinates;
}

/**
 * Ajusta automaticamente a visualização.
 *
 * Considera:
 *
 * - localização atual;
 * - pontos do roteiro.
 */
function fitMap(map, routeCoordinates, userLocation) {
    const coordinates = [...routeCoordinates];

    if (userLocation) {
        coordinates.push([userLocation.latitude, userLocation.longitude]);
    }

    if (!coordinates.length) {
        return;
    }

    if (coordinates.length === 1) {
        map.setView(coordinates[0], DEFAULT_ZOOM);

        return;
    }

    map.fitBounds(L.latLngBounds(coordinates), {
        padding: [50, 50],

        /**
         * Evita zoom exagerado
         * caso os pontos estejam próximos.
         */
        maxZoom: DEFAULT_ZOOM,
    });
}

/**
 * Inicializa um mapa.
 */
async function renderMap(element) {
    const rawStops = element.dataset.stops;

    if (!rawStops) {
        return;
    }

    let stops;

    try {
        stops = JSON.parse(rawStops);
    } catch (error) {
        throw new Error("Dados inválidos para renderização do mapa.", {
            cause: error,
        });
    }

    stops = normalizeStops(stops);

    if (!stops.length) {
        element.innerHTML = `
            <div class="route-map-empty">
                Localização indisponível
                para esta rota.
            </div>
        `;

        return;
    }

    const map = L.map(element, {
        zoomControl: true,

        /**
         * Evita zoom acidental
         * enquanto o usuário rola
         * a página.
         */
        scrollWheelZoom: false,
    });

    addTileLayer(map);

    const type = element.dataset.mapType ?? "route";

    const routeCoordinates =
        type === "adaptation"
            ? renderAdaptedRoute(map, stops)
            : renderStandardRoute(map, stops);

    let userLocation = null;

    /**
     * O mapa continua funcionando
     * mesmo que o usuário negue
     * a permissão.
     */
    try {
        userLocation = await getCurrentPosition();

        renderUserLocation(map, userLocation);
    } catch (error) {
        console.info("Rota Viva: localização atual não disponível.", error);

        showLocationWarning(element);
    }

    fitMap(map, routeCoordinates, userLocation);
}

export function initializeRouteMaps() {
    document.querySelectorAll("[data-route-map]").forEach((element) => {
        initializeMap(element);
    });
}

function showLocationWarning(element) {
    const wrapper = element.closest(".route-map-wrapper");

    if (!wrapper) {
        return;
    }

    const warning = wrapper.querySelector("[data-location-warning]");

    if (warning) {
        warning.hidden = false;
    }
}

async function initializeMap(element) {
    try {
        await renderMap(element);
    } catch (error) {
        console.error("Rota Viva: erro ao carregar mapa.", error);

        showMapFallback(element);
    }
}

function showMapFallback(element) {
    const wrapper = element.closest(".route-map-wrapper");

    if (!wrapper) {
        return;
    }

    element.hidden = true;

    const fallback = wrapper.querySelector("[data-map-fallback]");

    if (fallback) {
        fallback.hidden = false;
    }
}
