# MMS Marketing Mobile

Aplikasi Flutter untuk marketing yang berkomunikasi hanya melalui layanan
Laravel pada `/api/v1`.

## Menjalankan Lokal

```bash
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

Gunakan `10.0.2.2` dari Android Emulator. Pada perangkat fisik, ganti dengan
alamat IP LAN komputer server, misalnya
`http://192.168.1.10:8000/api/v1`.

## Build Rilis

Gunakan domain HTTPS yang sudah dideploy pada Laravel:

```bash
flutter build apk --release --dart-define=API_BASE_URL=https://domain-anda/api/v1
```

Sebelum rilis, buat `android/key.properties` dari
`android/key.properties.example` dan isi dengan lokasi serta kredensial
keystore Android yang disimpan aman di luar Git.

## Verifikasi

```bash
flutter analyze
flutter test
flutter build apk --release --dart-define=API_BASE_URL=https://domain-anda/api/v1
```
