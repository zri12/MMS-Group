import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

const DEFAULT_CENTER = [-6.9175, 107.6191];
const DEFAULT_ZOOM = 12;

function parseDatasetJson(value, fallback = []) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch {
        return fallback;
    }
}

function popupContent(marker) {
    const container = document.createElement('div');
    container.className = 'space-y-1 text-sm';

    const title = document.createElement('p');
    title.className = 'font-semibold';
    title.textContent = marker.label ?? 'Marketing';
    container.appendChild(title);

    const status = document.createElement('p');
    status.textContent = marker.status ?? '-';
    container.appendChild(status);

    if (marker.updated_at) {
        const updated = document.createElement('p');
        updated.textContent = `Update: ${marker.updated_at}`;
        container.appendChild(updated);
    }

    if (marker.url) {
        const link = document.createElement('a');
        link.href = marker.url;
        link.textContent = 'Detail Tracking';
        link.className = 'font-semibold underline';
        container.appendChild(link);
    }

    return container;
}

function escapeAttribute(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('"', '&quot;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;');
}

function avatarMarkerIcon(marker) {
    const initial = String(marker.label ?? 'P')
        .replace(/^.*-\s*/, '')
        .trim()
        .charAt(0)
        .toUpperCase() || 'P';
    const photo = marker.photo_url
        ? `<img src="${escapeAttribute(marker.photo_url)}" alt="" onerror="this.remove()">`
        : '';

    return L.divIcon({
        className: '',
        html: `<span class="real-map-avatar">${photo}<span class="real-map-avatar-fallback">${escapeAttribute(initial)}</span></span>`,
        iconSize: [44, 44],
        iconAnchor: [22, 22],
        popupAnchor: [0, -22],
    });
}

function fitMap(map, bounds) {
    if (!bounds || !bounds.isValid()) {
        map.setView(DEFAULT_CENTER, DEFAULT_ZOOM);
        return;
    }

    map.fitBounds(bounds, {
        maxZoom: 17,
        padding: [32, 32],
    });
}

function initializeTrackingMap(element) {
    if (element.dataset.initialized === 'true') {
        return;
    }

    element.dataset.initialized = 'true';

    const markers = parseDatasetJson(element.dataset.markers);
    const path = parseDatasetJson(element.dataset.path);
    const map = L.map(element, {
        scrollWheelZoom: true,
    }).setView(DEFAULT_CENTER, DEFAULT_ZOOM);

    map.attributionControl.setPrefix(false);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    const bounds = L.latLngBounds();
    const markerLayer = L.layerGroup().addTo(map);

    if (Array.isArray(path) && path.length > 0) {
        const route = path.map((point) => [point.lat, point.lng]);
        L.polyline(route, {
            color: '#d4af37',
            weight: 4,
            opacity: 0.86,
        }).addTo(map);
        route.forEach((point) => bounds.extend(point));
    }

    function renderMarkers(nextMarkers) {
        const nextBounds = L.latLngBounds();

        markerLayer.clearLayers();

        nextMarkers.forEach((marker) => {
            if (marker.lat === null || marker.lng === null) {
                return;
            }

            const latLng = [marker.lat, marker.lng];
            L.marker(latLng, {
                icon: avatarMarkerIcon(marker),
            }).addTo(markerLayer).bindPopup(popupContent(marker));
            nextBounds.extend(latLng);
        });

        element._mmsBounds = nextBounds.isValid() ? nextBounds : bounds;

        return nextBounds;
    }

    markers.forEach((marker) => {
        if (marker.lat === null || marker.lng === null) {
            return;
        }

        bounds.extend([marker.lat, marker.lng]);
    });

    renderMarkers(markers);

    element._mmsMap = map;
    element._mmsBounds = bounds;
    element._mmsRefreshMarkers = (nextMarkers) => {
        // Keep the administrator's chosen zoom and map position while new GPS
        // points arrive. The Fit control remains available when recentering is wanted.
        renderMarkers(nextMarkers);
    };
    fitMap(map, bounds);

    window.setTimeout(() => map.invalidateSize(), 100);
}

function initializeTrackingControls() {
    document.querySelectorAll('[data-tracking-map-fit]').forEach((button) => {
        if (button.dataset.initialized === 'true') {
            return;
        }

        button.dataset.initialized = 'true';
        button.addEventListener('click', () => {
            const mapElement = document.getElementById(button.dataset.trackingMapFit);
            const map = mapElement?._mmsMap;

            if (!map) {
                return;
            }

            map.invalidateSize();
            fitMap(map, mapElement._mmsBounds);
        });
    });

    document.querySelectorAll('[data-tracking-map-fullscreen]').forEach((button) => {
        if (button.dataset.initialized === 'true') {
            return;
        }

        button.dataset.initialized = 'true';
        button.addEventListener('click', () => {
            const shell = document.getElementById(button.dataset.trackingMapFullscreen);
            shell?.requestFullscreen?.();
        });
    });
}

function initializeTrackingPolling() {
    const pollingElement = document.querySelector('[data-tracking-map][data-poll-seconds]');

    if (window.__mmsTrackingPollingTimer) {
        window.clearInterval(window.__mmsTrackingPollingTimer);
        window.__mmsTrackingPollingTimer = null;
    }

    if (!pollingElement) {
        return;
    }

    const seconds = Number(pollingElement.dataset.pollSeconds || 0);

    if (!Number.isFinite(seconds) || seconds <= 0) {
        return;
    }

    window.__mmsTrackingPollingTimer = window.setInterval(() => {
        if (document.hidden || !pollingElement.dataset.feedUrl || !pollingElement._mmsRefreshMarkers) {
            return;
        }

        fetch(pollingElement.dataset.feedUrl, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => (response.ok ? response.json() : null))
            .then((payload) => {
                const markers = payload?.data?.markers;

                if (Array.isArray(markers)) {
                    pollingElement._mmsRefreshMarkers(markers);
                }

                const updatedAt = payload?.data?.updated_at;
                const updatedLabel = document.querySelector('[data-tracking-updated-at]');

                if (updatedAt && updatedLabel) {
                    updatedLabel.textContent = `Pembaruan terakhir: ${updatedAt}`;
                }
            })
            .catch(() => {});
    }, seconds * 1000);
}

export function initializeMapFoundation() {
    document.querySelectorAll('[data-tracking-map]').forEach(initializeTrackingMap);
    initializeTrackingControls();
    initializeTrackingPolling();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMapFoundation);
} else {
    initializeMapFoundation();
}

document.addEventListener('mms:navigated', initializeMapFoundation);
