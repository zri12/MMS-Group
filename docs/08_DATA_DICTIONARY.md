---
title: "Data Dictionary"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.1.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Konvensi umum

- Label UI berbahasa Indonesia.
- API dan database memakai `snake_case`.
- PHP/Livewire/Dart model boleh memakai `camelCase` bila sesuai, dengan serialization eksplisit.
- Nominal adalah integer rupiah.
- Tanggal API memakai `YYYY-MM-DD`.
- Waktu memakai `HH:mm:ss`.
- Timestamp memakai ISO 8601.
- Koordinat memakai decimal.
- Field `local_uuid` dibuat di Flutter untuk idempotency.

# 1. Marketing

| Label UI | API | Database | Tipe | Wajib | Catatan |
|---|---|---|---|---:|---|
| ID | `id` | `marketing_profiles.id` | integer | Ya | Server |
| Kode | `code` | `code` | string | Ya | Unik, contoh M01 |
| Nama | `name` | `users.name` | string | Ya | |
| Username | `username` | `users.username` | string | Ya | Unik |
| Email | `email` | `users.email` | string/null | Tidak | |
| Nomor HP | `phone` | `marketing_profiles.phone` | string/null | Tidak | |
| Area/Resort | `area` | `marketing_profiles.area` | string | Ya | |
| Foto Profil | `profile_photo_url` | `profile_photo_path` | string/null | Tidak | URL pada response |
| Status Akun | `account_status` | `users.is_active` | active/inactive | Ya | Mapping boolean |
| Hari Kerja | `work_days` | relasi | string[] | Ya | Senin–Sabtu |

# 2. Jadwal

| Label UI | API | Database | Tipe | Wajib |
|---|---|---|---|---:|
| ID Jadwal | `id` | `id` | integer | Ya |
| Marketing | `marketing_id` | `marketing_profile_id` | integer | Ya |
| Prospek | `prospect_id` | `prospect_id` | integer/null | Tidak |
| Hari | `day` | `day_name` | DayName | Ya |
| Tanggal | `date` | `schedule_date` | date | Ya |
| Jam Mulai | `start_time` | `start_time` | time | Ya |
| Jam Selesai | `end_time` | `end_time` | time/null | Tidak |
| Konsumen | `consumer_name` | `consumer_name_snapshot` | string/null | Tidak |
| Agenda | `agenda` | `agenda` | string | Ya |
| Area | `area` | `area` | string | Ya |
| Resort | `resort` | `resort` | string/null | Tidak |
| Tujuan/Lokasi | `destination` | `destination` | string/null | Tidak |
| Catatan | `note` | `note` | string/null | Tidak |
| Status | `status` | `status` | ScheduleStatus | Ya |

# 3. Prospek

| Label UI | API | Database | Tipe | Wajib |
|---|---|---|---|---:|
| UUID Lokal | `local_uuid` | `local_uuid` | UUID/null | Ya untuk offline |
| Nama Konsumen | `name` | `name` | string | Ya |
| Nomor HP | `phone` | `phone` | string | Ya |
| Alamat | `address` | `address` | string | Ya |
| Usaha/Pekerjaan | `business` | `business` | string | Ya |
| Status | `status` | `status` | ProspectStatus | Ya |
| Hasil Awal | `initial_visit_result` | `initial_visit_result` | string | Ya |
| Catatan | `notes` | `notes` | string/null | Tidak |
| Resort | `resort` | `resort` | string | Ya |
| Tanggal Input | `input_date` | `input_date` | date | Ya |
| Waktu Input | `input_time` | `input_time` | time | Ya |
| Latitude | `latitude` | `latitude` | decimal/null | Tidak |
| Longitude | `longitude` | `longitude` | decimal/null | Tidak |
| Alamat Lokasi | `location_address` | `location_address` | string/null | Tidak |
| Marketing | response `marketing` | `marketing_profile_id` | relation | Ya |
| Anggota Terkait | `linked_member_id` | derived dari `members.source_prospect_id` | integer/null | Tidak |
| Sinkronisasi | `sync_status` | `sync_status` | SyncStatus | Ya |

# 4. Anggota

| Label UI | API | Database | Tipe | Wajib |
|---|---|---|---|---:|
| UUID Lokal | `local_uuid` | `local_uuid` | UUID/null | Ya untuk offline |
| Resort | `resort` | `resort` | string | Ya |
| Tanggal | `date` | `input_date` | date | Ya |
| Waktu | `time` | `input_time` | time | Ya |
| Nama Anggota | `name` | `name` | string | Ya |
| Nomor Anggota | `member_number` | `member_number` | string | Ya |
| Nomor Pinjaman | `loan_number` | `loan_number` | string | Ya |
| Alamat | `address` | `address` | string | Ya |
| Nomor HP | `phone` | `phone` | string | Ya |
| Jenis Usaha | `business` | `business` | string | Ya |
| Pinjaman | `loan_amount` | `loan_amount` | integer | Ya |
| Angsuran | `installment_amount` | `installment_amount` | integer | Ya |
| Asuransi | `insurance_amount` | `insurance_amount` | integer | Ya |
| Jaminan | `collateral` | `collateral` | string | Ya |
| Status | `approval_status` | `approval_status` | MemberApprovalStatus | Ya |
| Foto Anggota | `member_photo` upload / `member_photo_url` response | `member_photo_path` | file/string | Ya sesuai UI |
| Latitude | `latitude` | `latitude` | decimal/null | Tidak |
| Longitude | `longitude` | `longitude` | decimal/null | Tidak |
| Alamat Lokasi | `location_address` | `location_address` | string/null | Tidak |
| Prospek Asal | `source_prospect_id` | `source_prospect_id` | integer/null | Tidak |
| Sinkronisasi | `sync_status` | `sync_status` | SyncStatus | Ya |

Status awal selalu `Menunggu`.

# 5. Laporan Operasional Harian

| Label UI | API | Database | Tipe | Wajib |
|---|---|---|---|---:|
| UUID Lokal | `local_uuid` | `local_uuid` | UUID | Ya untuk offline |
| Tanggal | `date` | `report_date` | date | Ya |
| Waktu | `time` | `report_time` | time | Ya |
| Hari | `day` | `day_name` | DayName | Ya |
| Resort | `resort` | `resort` | string | Ya |
| Storting | `storting` | `storting` | integer | Ya |
| Asuransi | `insurance_amount` | `insurance_amount` | integer | Ya |
| Drop | `drop` | `drop_amount` | integer | Ya |
| Tabungan Keluar | `withdrawal_saving` | `withdrawal_saving` | integer | Ya |
| Target Lama Nominal | `previous_target_amount` | sama | integer | Ya |
| Target Lama Orang | `previous_target_people` | sama | integer | Ya |
| Target Masuk Nominal | `incoming_target_amount` | sama | integer | Ya |
| Target Masuk Orang | `incoming_target_people` | sama | integer | Ya |
| Target Keluar Nominal | `outgoing_target_amount` | sama | integer | Ya |
| Target Keluar Orang | `outgoing_target_people` | sama | integer | Ya |
| Jumlah Target Nominal | `total_target_amount` | sama | integer | Ya |
| Jumlah Target Orang | `total_target_people` | sama | integer | Ya |
| Drop Baru | `new_drop` | `new_drop` | integer | Ya |
| Drop Lanjut | `continued_drop` | `continued_drop` | integer | Ya |
| Catatan | `notes` | `notes` | string/null | Tidak |
| Lampiran Operasional | `attachments` | relasi `operational_report_attachments` | array | Tidak | Maksimum dua lampiran dengan kategori berbeda |
| Jenis Lampiran | `attachments[].type` | `operational_report_attachments.type` | `disbursement`/`transfer_proof` | Ya bila ada lampiran | Satu Foto Pencairan dan/atau satu Foto Bukti Transfer |
| Foto Lampiran | `attachments[].photo` | `operational_report_attachments.photo_path` | file/string | Ya bila ada lampiran | Upload multipart, URL tersedia pada response |
| Keterangan Lampiran | `attachments[].caption` | `operational_report_attachments.caption` | string/null | Tidak | |
| Marketing | response `marketing` | `marketing_profile_id` | relation | Ya |
| Sinkronisasi | `sync_status` | `sync_status` | SyncStatus | Ya |

Tidak ada field Lain-lain, foto anggota, atau lokasi setoran. Foto Pencairan dan
Foto Bukti Transfer hanya tersedia sebagai lampiran berkategori pada laporan ini,
bukan sebagai modul terpisah.

# 6. Laporan Kunjungan

| Label UI | API | Database | Tipe | Wajib |
|---|---|---|---|---:|
| UUID Lokal | `local_uuid` | `local_uuid` | UUID | Ya untuk offline |
| Prospek | `prospect_id` | `prospect_id` | integer | Ya |
| Tujuan | `visit_purpose` | `visit_purpose` | string | Ya |
| Hasil | `visit_result` | `visit_result` | VisitResult | Ya |
| Status Prospek | `prospect_status` | `prospect_status` | ProspectStatus | Ya |
| Catatan | `notes` | `notes` | string/null | Tidak |
| Tanggal Follow-up | `follow_up_date` | `follow_up_date` | date/null | Tidak |
| Foto | `photo` upload / `photo_url` response | `photo_path` | file/string | Sesuai form |
| Keterangan Foto | `photo_caption` | `photo_caption` | string/null | Tidak |
| Resort | `resort` | `resort` | string | Ya |
| Hari | `day` | `day_name` | DayName | Ya |
| Tanggal | `date` | `visit_date` | date | Ya |
| Waktu | `time` | `visit_time` | time | Ya |
| Latitude | `latitude` | `latitude` | decimal/null | Tidak |
| Longitude | `longitude` | `longitude` | decimal/null | Tidak |
| Lokasi | `location_address` | `location_address` | string/null | Tidak |
| Sinkronisasi | `sync_status` | `sync_status` | SyncStatus | Ya |

# 7. Tracking Session

| Label/Field | API | Database | Tipe |
|---|---|---|---|
| UUID Lokal | `local_uuid` | `local_uuid` | UUID |
| ID Sesi | `id` | `id` | integer |
| Jadwal | `schedule_id` | `schedule_id` | integer/null |
| Mulai | `started_at` | `started_at` | ISO timestamp |
| Selesai | `ended_at` | `ended_at` | ISO timestamp/null |
| Status | `status` | `status` | string |
| Jarak Meter | `distance_meters` | `distance_meters` | integer/null |
| Jumlah Kunjungan | `visit_count` | `visit_count` | integer |

# 8. Tracking Point

| Field | API | Database | Tipe | Wajib |
|---|---|---|---|---:|
| UUID | `local_uuid` | `local_uuid` | UUID | Ya |
| Latitude | `latitude` | `latitude` | decimal | Ya |
| Longitude | `longitude` | `longitude` | decimal | Ya |
| Akurasi | `accuracy_meters` | `accuracy_meters` | decimal/null | Tidak |
| Kecepatan | `speed_mps` | `speed_mps` | decimal/null | Tidak |
| Arah | `heading` | `heading` | decimal/null | Tidak |
| Ketinggian | `altitude_meters` | `altitude_meters` | decimal/null | Tidak |
| Alamat | `address` | `address` | string/null | Tidak |
| Tipe Titik | `point_type` | `point_type` | TrackingPointType | Ya |
| Waktu Rekam | `recorded_at` | `recorded_at` | ISO timestamp | Ya |

# 9. Status terpusat

## DayName

- Senin
- Selasa
- Rabu
- Kamis
- Jumat
- Sabtu

## MemberApprovalStatus

- Menunggu
- Disetujui
- Ditolak

## ProspectStatus

- Baru
- Tertarik
- Perlu Follow Up
- Tidak Tertarik
- Selesai

## VisitResult

- Berhasil Bertemu
- Tidak Bertemu
- Transaksi Selesai

## ScheduleStatus

- Belum Dikunjungi
- Berlangsung
- Selesai
- Dibatalkan

## TrackingStatus

- Aktif
- Offline
- Belum Mulai
- GPS Tidak Aktif
- Tidak Dijadwalkan

## TrackingPointType

- Mulai
- Perjalanan
- Kunjungan
- Selesai

## SyncStatus

- Tersinkronisasi
- Menunggu Sinkronisasi
- Gagal
