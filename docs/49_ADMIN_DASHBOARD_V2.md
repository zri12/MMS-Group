# Admin Dashboard V2

Layout order:

1. Drop, Storting, Sirkulasi.
2. Target Masuk, Target Keluar, Total Target.
3. Anggota Masuk, Anggota Keluar, Total Anggota.
4. Foto Pencairan and Foto Bukti Transfer.
5. PDL cards linking to Detail PDL.
6. Tracking PDL action.

The dashboard uses aggregate queries for reports and recap rows. Sirkulasi is the sum of `operational_recap_rows.current_circulation` for the selected recap date; it is not calculated in the view. At mobile widths metrics stack to retain readable labels and values; at desktop widths they use three columns.
