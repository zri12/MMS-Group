---
title: "Design Tokens"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.2.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Warna

Implementasi Blade Design System memakai token `--mms-*` pada `apps/web-admin-api/resources/css/app.css`. Alias lama di bawah tetap dipertahankan selama transisi agar halaman existing tidak patah.

```css
:root {
  --bg-primary: #080B10;
  --bg-secondary: #0D1219;

  --surface-primary: #111720;
  --surface-secondary: #151C25;
  --surface-tertiary: #1A222D;
  --surface-hover: #1E2834;

  --border-subtle: rgba(255, 255, 255, 0.07);
  --border-medium: rgba(255, 255, 255, 0.11);
  --border-gold: rgba(212, 175, 55, 0.28);

  --gold-primary: #D4AF37;
  --gold-light: #E6C45A;
  --gold-soft: rgba(212, 175, 55, 0.12);

  --text-primary: #F3F5F7;
  --text-secondary: #A7AFBA;
  --text-muted: #747E8B;

  --success: #22C55E;
  --success-soft: rgba(34, 197, 94, 0.12);
  --warning: #F59E0B;
  --warning-soft: rgba(245, 158, 11, 0.12);
  --danger: #EF4444;
  --danger-soft: rgba(239, 68, 68, 0.12);
  --info: #3B82F6;
  --info-soft: rgba(59, 130, 246, 0.12);
}
```

# Typography

Font stack:

```css
font-family:
  "Manrope",
  "Inter",
  system-ui,
  -apple-system,
  "Segoe UI",
  sans-serif;
```

| Penggunaan | Mobile | Desktop | Weight |
|---|---:|---:|---:|
| Page title | 21–23 px | 26–30 px | 700 |
| Section title | 16–18 px | 17–19 px | 600–700 |
| Card title | 13–15 px | 14–16 px | 600 |
| Body | 13–14 px | 13–15 px | 400–500 |
| Caption | 11–12 px | 12 px | 500 |
| Summary value | 20–24 px | 24–30 px | 700 |

Hindari weight 800/900 kecuali logo atau kebutuhan sangat terbatas.

# Spacing

Gunakan skala:

- 4 px
- 8 px
- 12 px
- 16 px
- 20 px
- 24 px
- 32 px
- 40 px

Mobile:

- page padding: 16 px
- section gap: 24 px
- card gap: 10–12 px

Desktop:

- page padding: 24–32 px
- section gap: 28–32 px

# Radius

- Input/button: 12–14 px
- Card: 14–18 px
- Modal: 20–24 px
- Bottom sheet top: 24 px
- Map: 18 px

# Control size

- Button: 42–46 px
- Icon button: 44×44 px
- Input: 42–46 px
- Bottom nav: 72–78 px
- Active nav circle: 34–38 px

# Shadow

Card biasa menggunakan border subtle tanpa shadow. Shadow hanya untuk:

- modal;
- bottom sheet;
- drawer;
- bottom navigation;
- dropdown;
- card utama tertentu.

```css
box-shadow: 0 8px 24px rgba(0, 0, 0, 0.16);
```

Tidak ada shadow gold.

# Status mapping

| Status | Warna |
|---|---|
| Aktif/Tersinkronisasi/Disetujui/Selesai | Success |
| Menunggu/Belum Mulai/Belum Dikunjungi | Warning atau neutral |
| Offline/Ditolak/Gagal/Dibatalkan | Danger |
| GPS Tidak Aktif | Warning |
| Tidak Dijadwalkan | Text muted |
| Berlangsung | Info atau success |
