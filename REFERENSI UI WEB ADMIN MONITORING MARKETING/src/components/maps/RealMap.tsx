import { useEffect, useMemo, useRef, useState } from "react";
import { createPortal } from "react-dom";
import L from "leaflet";
import { LocateFixed, Maximize2, Minimize2 } from "lucide-react";
import { MapContainer, Marker, Popup, Polyline, TileLayer, useMap } from "react-leaflet";
import "leaflet/dist/leaflet.css";

export interface RealMapMarker {
  id: string;
  code?: string;
  name: string;
  latitude: number;
  longitude: number;
  address?: string;
  status?: string;
  time?: string;
  photo?: string;
  onClick?: () => void;
}

interface RealMapProps {
  markers: RealMapMarker[];
  route?: Array<[number, number]>;
  loading?: boolean;
  error?: string | null;
  onRetry?: () => void;
  className?: string;
  zoom?: number;
  enableFullscreen?: boolean;
  fullscreenTitle?: string;
  fullscreenSubtitle?: string;
}

interface MapCanvasProps {
  markers: RealMapMarker[];
  route: Array<[number, number]>;
  center: [number, number];
  zoom: number;
  retryKey: number;
  fullscreenKey: number;
  fitAllKey: number;
  onTileReady: () => void;
  onTileError: () => void;
}

function statusClass(status?: string) {
  if (status === "Aktif" || status === "Sedang Berjalan") return "active";
  if (status === "Offline") return "offline";
  if (status === "GPS Tidak Aktif") return "warning";
  return "idle";
}

function isValidCoordinate(latitude: number, longitude: number) {
  return Number.isFinite(latitude) && Number.isFinite(longitude)
    && latitude >= -90 && latitude <= 90 && longitude >= -180 && longitude <= 180;
}

function escapeHtml(value: string) {
  return value.replace(/[&<>"']/g, character => ({
    "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;",
  })[character] ?? character);
}

function getMapPoints(markers: RealMapMarker[], route: Array<[number, number]>) {
  return [
    ...markers.map(marker => L.latLng(marker.latitude, marker.longitude)),
    ...route.map(([lat, lng]) => L.latLng(lat, lng)),
  ];
}

function ResizeMap({ fullscreenKey }: { fullscreenKey: number }) {
  const map = useMap();

  useEffect(() => {
    let cancelled = false;
    const invalidate = () => {
      window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
          if (cancelled) return;
          try {
            map.invalidateSize();
          } catch {
            // Layout can be incomplete for a frame while an overlay or viewport changes.
          }
        });
      });
    };
    const timer = window.setTimeout(invalidate, 100);
    const container = map.getContainer();
    const observer = typeof ResizeObserver !== "undefined" ? new ResizeObserver(invalidate) : null;
    observer?.observe(container);
    if (container.parentElement) observer?.observe(container.parentElement);
    window.addEventListener("resize", invalidate);
    window.addEventListener("app-layout-change", invalidate);

    return () => {
      cancelled = true;
      window.clearTimeout(timer);
      window.removeEventListener("resize", invalidate);
      window.removeEventListener("app-layout-change", invalidate);
      observer?.disconnect();
    };
  }, [map, fullscreenKey]);

  return null;
}

function FitMap({ markers, route, fitAllKey }: {
  markers: RealMapMarker[];
  route: Array<[number, number]>;
  fitAllKey: number;
}) {
  const map = useMap();

  useEffect(() => {
    const points = getMapPoints(markers, route);
    if (points.length > 1) {
      map.fitBounds(L.latLngBounds(points), { padding: [42, 42], maxZoom: 14 });
    } else if (points[0]) {
      map.setView(points[0], 15);
    }
  }, [map, markers, route, fitAllKey]);

  return null;
}

function MapCanvas({
  markers, route, center, zoom, retryKey, fullscreenKey, fitAllKey, onTileReady, onTileError,
}: MapCanvasProps) {
  return (
    <MapContainer
      center={center}
      zoom={zoom}
      scrollWheelZoom
      zoomControl
      attributionControl={false}
      style={{ height:"100%", width:"100%" }}
    >
      <TileLayer
        key={retryKey}
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        eventHandlers={{
          tileload: onTileReady,
          tileerror: onTileError,
        }}
      />
      {route.length > 1 && (
        <>
          <Polyline positions={route} pathOptions={{ color:"#D4AF37", weight:5, opacity:.88 }}/>
          <Polyline positions={route} pathOptions={{ color:"#FFF4B8", weight:1.5, opacity:.8, dashArray:"7 10" }}/>
        </>
      )}
      {markers.map(marker => {
        const code = escapeHtml(marker.code ?? marker.name.slice(0, 2).toUpperCase());
        const photo = marker.photo ? escapeHtml(marker.photo) : "";
        const icon = L.divIcon({
          className:"real-map-icon",
          html:`<div class="real-map-avatar ${statusClass(marker.status)}"><span class="real-map-photo-fallback">${code}</span>${photo?`<img src="${photo}" alt="" onerror="this.remove()" />`:""}<span class="real-map-code">${code}</span></div>`,
          iconSize:[48,56],
          iconAnchor:[24,52],
          popupAnchor:[0,-50],
        });

        return (
          <Marker key={marker.id} position={[marker.latitude, marker.longitude]} icon={icon}>
            <Popup maxWidth={260} minWidth={210}>
              <div className="map-popup-profile">
                <div className="map-popup-avatar">
                  <span>{marker.code ?? marker.name.slice(0,2).toUpperCase()}</span>
                  {marker.photo && <img src={marker.photo} alt="" onError={event=>event.currentTarget.remove()}/>}
                </div>
                <div className="min-w-0">
                  <strong>{marker.code ? `${marker.code} · ` : ""}{marker.name}</strong>
                  {marker.status && <span>{marker.status}</span>}
                  {marker.address && <small>{marker.address}</small>}
                  {marker.time && <small>Terakhir aktif {marker.time}</small>}
                  {marker.onClick && <button type="button" onClick={marker.onClick}>Lihat detail tracking</button>}
                </div>
              </div>
            </Popup>
          </Marker>
        );
      })}
      <FitMap markers={markers} route={route} fitAllKey={fitAllKey}/>
      <ResizeMap fullscreenKey={fullscreenKey}/>
    </MapContainer>
  );
}

function MapState({ children, className }: { children: React.ReactNode; className: string }) {
  return (
    <div className={`real-map-shell grid min-h-[320px] w-full min-w-0 max-w-full place-items-center overflow-hidden rounded-[18px] border border-white/[0.07] bg-[#111720] p-6 text-center shadow-[0_8px_24px_rgba(0,0,0,.16)] ${className}`}>
      {children}
    </div>
  );
}

export function RealMap({
  markers,
  route = [],
  loading = false,
  error = null,
  onRetry,
  className = "h-[340px]",
  zoom = 13,
  enableFullscreen = true,
  fullscreenTitle = "Tracking Lokasi Marketing",
  fullscreenSubtitle,
}: RealMapProps) {
  const [tileReady, setTileReady] = useState(false);
  const [tileError, setTileError] = useState(false);
  const [retryKey, setRetryKey] = useState(0);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const [fullscreenKey, setFullscreenKey] = useState(0);
  const [fitAllKey, setFitAllKey] = useState(0);
  const fullscreenButtonRef = useRef<HTMLButtonElement>(null);
  const closeButtonRef = useRef<HTMLButtonElement>(null);

  const validMarkers = useMemo(() => markers
    .map(marker => ({ ...marker, latitude:Number(marker.latitude), longitude:Number(marker.longitude) }))
    .filter(marker => isValidCoordinate(marker.latitude, marker.longitude)), [markers]);
  const validRoute = useMemo(() => route
    .map(([lat, lng]): [number, number] => [Number(lat), Number(lng)])
    .filter(([lat, lng]) => isValidCoordinate(lat, lng)), [route]);

  const center = useMemo<[number, number]>(() => {
    if (validMarkers.length) {
      return [
        validMarkers.reduce((sum, marker) => sum + marker.latitude, 0) / validMarkers.length,
        validMarkers.reduce((sum, marker) => sum + marker.longitude, 0) / validMarkers.length,
      ];
    }
    return validRoute[0] ?? [-6.9175, 107.6191];
  }, [validMarkers, validRoute]);

  useEffect(() => {
    setTileReady(false);
    setTileError(false);
  }, [retryKey]);

  useEffect(() => {
    if (!isFullscreen) return;
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const close = (event: KeyboardEvent) => {
      if (event.key === "Escape") setIsFullscreen(false);
    };
    window.addEventListener("keydown", close);
    const focusTimer = window.setTimeout(() => closeButtonRef.current?.focus(), 0);
    setFullscreenKey(value => value + 1);

    return () => {
      window.clearTimeout(focusTimer);
      window.removeEventListener("keydown", close);
      document.body.style.overflow = previousOverflow;
      window.setTimeout(() => {
        setFullscreenKey(value => value + 1);
        window.dispatchEvent(new Event("app-layout-change"));
        fullscreenButtonRef.current?.focus();
      }, 0);
    };
  }, [isFullscreen]);

  if (loading) {
    return (
      <MapState className={className}>
        <div>
          <span className="mx-auto block size-8 animate-spin rounded-full border-2 border-[#D4AF37]/25 border-t-[#D4AF37]"/>
          <p className="mt-3 text-[13px] font-bold text-[#F8FAFC]">Memuat lokasi marketing...</p>
        </div>
      </MapState>
    );
  }

  if (error) {
    return (
      <MapState className={className}>
        <div>
          <p className="text-[14px] font-extrabold text-[#F8FAFC]">Peta tidak dapat dimuat.</p>
          <p className="mt-1 text-[12px] text-[#8F98A5]">{error}</p>
          {onRetry && (
            <button type="button" onClick={onRetry}
              className="mt-4 min-h-11 rounded-xl bg-[#D4AF37] px-4 text-[12px] font-extrabold text-[#07090C] transition hover:bg-[#E8C65A]">
              Coba Lagi
            </button>
          )}
        </div>
      </MapState>
    );
  }

  if (!validMarkers.length && !validRoute.length) {
    return (
      <MapState className={className}>
        <div>
          <span className="mx-auto grid size-11 place-items-center rounded-full bg-white/[0.05] text-xl text-[#7E8794]">⌖</span>
          <p className="mt-3 text-[13px] font-bold text-[#F8FAFC]">Belum ada data lokasi untuk hari ini.</p>
        </div>
      </MapState>
    );
  }

  const mapCanvas = (key: string, currentFullscreenKey: number, currentFitAllKey: number) => (
    <MapCanvas
      key={key}
      markers={validMarkers}
      route={validRoute}
      center={center}
      zoom={zoom}
      retryKey={retryKey}
      fullscreenKey={currentFullscreenKey}
      fitAllKey={currentFitAllKey}
      onTileReady={()=>{setTileReady(true);setTileError(false);}}
      onTileError={()=>setTileError(true)}
    />
  );

  return (
    <>
      <div className={`real-map-shell relative min-h-[320px] w-full min-w-0 max-w-full overflow-hidden rounded-[18px] border border-white/[0.07] bg-[#111720] shadow-[0_8px_24px_rgba(0,0,0,.16)] ${className}`}>
        {mapCanvas("normal", fullscreenKey, 0)}
        {enableFullscreen && (
          <button
            ref={fullscreenButtonRef}
            type="button"
            onClick={()=>setIsFullscreen(true)}
            aria-label="Perbesar peta"
            title="Perbesar peta"
            className="absolute right-3 top-3 z-40 grid size-11 place-items-center rounded-[13px] border border-white/[0.08] bg-[#111720]/95 text-[#E6C45A] shadow-[0_8px_24px_rgba(0,0,0,.2)] transition hover:bg-[#1A222D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]"
          >
            <Maximize2 size={19}/>
          </button>
        )}

        {!tileReady && !tileError && (
          <div className="pointer-events-none absolute inset-0 z-20 grid place-items-center bg-[#0c1217]/80 backdrop-blur-[2px]">
            <div className="text-center">
              <span className="mx-auto block size-7 animate-spin rounded-full border-2 border-[#D4AF37]/25 border-t-[#D4AF37]"/>
              <p className="mt-2 text-[12px] font-bold text-[#F8FAFC]">Memuat lokasi marketing...</p>
            </div>
          </div>
        )}
        {tileError && !tileReady && (
          <div className="absolute inset-0 z-30 grid place-items-center bg-[#0c1217]/95 p-6 text-center">
            <div>
              <p className="text-[14px] font-extrabold text-[#F8FAFC]">Peta tidak dapat dimuat.</p>
              <p className="mt-1 text-[12px] text-[#8F98A5]">Periksa koneksi internet.</p>
              <button type="button" onClick={()=>setRetryKey(value=>value+1)}
                className="mt-4 min-h-11 rounded-xl bg-[#D4AF37] px-4 text-[12px] font-extrabold text-[#07090C] transition hover:bg-[#E8C65A]">
                Coba Lagi
              </button>
            </div>
          </div>
        )}
        <div className="pointer-events-none absolute bottom-2 left-2 z-10 rounded-lg border border-white/10 bg-[#0b1118]/90 px-2.5 py-1.5 text-[10px] font-bold text-[#d5dbe4] shadow-lg backdrop-blur">
          Lokasi Operasional Marketing
        </div>
      </div>
      <a
        href="https://www.openstreetmap.org/copyright"
        target="_blank"
        rel="noreferrer"
        className="mt-1.5 block w-fit max-w-full text-[9px] font-medium text-[#687382] transition hover:text-[#A7AFBA] focus-visible:text-[#E6C45A]"
      >
        © OpenStreetMap contributors
      </a>

      {isFullscreen && createPortal(
        <div
          className="fixed inset-0 z-[1300] flex w-full min-w-0 max-w-full flex-col overflow-x-hidden bg-[#07090C]"
          role="dialog"
          aria-modal="true"
          aria-labelledby="fullscreen-map-title"
        >
          <div className="flex min-h-16 shrink-0 items-center gap-3 border-b border-white/[0.07] bg-[#0A0F15] px-3 py-2 sm:min-h-[68px] sm:px-5">
            <div className="min-w-0 flex-1">
              <h2 id="fullscreen-map-title" className="truncate text-[14px] font-semibold text-[#F3F5F7] sm:text-[16px]">{fullscreenTitle}</h2>
              {fullscreenSubtitle && <p className="mt-0.5 truncate text-[11px] font-medium text-[#9CA5B3] sm:text-[12px]">{fullscreenSubtitle}</p>}
            </div>
            <button
              type="button"
              onClick={()=>setFitAllKey(value=>value+1)}
              aria-label="Lihat semua marker"
              className="flex min-h-11 shrink-0 items-center gap-2 rounded-xl border border-white/10 bg-[#111821] px-3 text-[11px] font-bold text-[#E8C65A] transition hover:border-[#D4AF37]/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]"
            >
              <LocateFixed size={17}/><span className="hidden min-[390px]:inline">Lihat Semua Marker</span>
            </button>
            <button
              ref={closeButtonRef}
              type="button"
              onClick={()=>setIsFullscreen(false)}
              aria-label="Tutup tampilan peta penuh"
              title="Tutup"
              className="grid size-11 shrink-0 place-items-center rounded-xl border border-white/10 bg-[#111821] text-[#F8FAFC] transition hover:border-[#D4AF37]/40 hover:text-[#E8C65A] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]"
            >
              <Minimize2 size={19}/>
            </button>
          </div>
          <div className="real-map-shell min-h-0 min-w-0 max-w-full flex-1 rounded-none border-0">
            {mapCanvas("fullscreen", fullscreenKey, fitAllKey)}
            <a
              href="https://www.openstreetmap.org/copyright"
              target="_blank"
              rel="noreferrer"
              className="absolute bottom-1.5 right-2 z-[500] text-[9px] font-medium text-[#56616E] transition hover:text-[#A7AFBA] focus-visible:text-[#E6C45A]"
            >
              © OpenStreetMap contributors
            </a>
          </div>
        </div>,
        document.body,
      )}
    </>
  );
}
