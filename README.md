# Community Waste Collection API

Take Home Test — Backend Developer, PT Inosoft Trans Sistem.

API untuk sistem pengelolaan sampah komunitas: data rumah tangga (household), permintaan pickup sampah (dengan inheritance per jenis sampah), dan pembayaran layanan.

> Status: **Work in progress** — dikerjakan bertahap selama masa pengerjaan test. Lihat progress terbaru di riwayat commit.

## Tech Stack

- **Framework**: Laravel 12.x (PHP 8.3)
- **Database**: MongoDB 7 (`mongodb/laravel-mongodb`)
- **Arsitektur**: Service–Repository Pattern
- **Environment**: Docker (Laravel + MongoDB)

## Prasyarat

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) sudah terinstall dan **jalan** (daemon aktif)
- Tidak perlu install PHP, Composer, atau MongoDB secara lokal — semuanya jalan di dalam container

## Setup & Menjalankan Project

1. Clone repository ini
2. Salin `.env.example` menjadi `.env` (atau pakai `.env` yang sudah disediakan kalau ada)
3. Build & jalankan container:

   ```bash
   docker compose up --build
   ```

   Build pertama kali memakan waktu beberapa menit (download image, install ekstensi PHP). Setelah itu, `docker compose up` biasa jauh lebih cepat.

4. Container `app` otomatis menjalankan **migration** dan **seeding** setiap kali start (lihat `docker/entrypoint.sh`) — jadi begitu container jalan, database sudah siap dipakai, tidak perlu langkah manual tambahan.

5. API bisa diakses di `http://localhost:8000`, MongoDB di `localhost:27017` (bisa dibuka lewat MongoDB Compass kalau mau lihat data langsung).

Jalankan di background dengan `docker compose up -d`. Untuk berhenti: `docker compose down`. **Note:** Jangan pakai `docker compose down -v` kecuali memang sengaja mau reset semua data dari nol.

## Migration & Seeding Manual

Migration dan seeding sudah otomatis jalan tiap container start, tapi kalau perlu dijalankan manual:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

Atau reset total (hapus semua data lalu migrate + seed ulang):

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Seeder aman dijalankan berkali-kali — hanya mengisi data kalau collection terkait masih kosong, tidak akan menimpa data yang sudah ada/diubah.

## API Documentation

Postman collection tersedia di [`postman/Community-Waste-Collection.postman_collection.json`](postman/Community-Waste-Collection.postman_collection.json) — import ke Postman untuk mencoba semua endpoint beserta contoh request/response.

Struktur collection: folder **Household**, **Waste Pickup** (dengan subfolder per tipe sampah), **Payment** itu semuanya CRUD/lifecycle dasar, sedangkan folder **Business Rules** di paling akhir khusus berisi skenario yang membuktikan business rule benar-benar bekerja:
- **Rule #1** — household diblokir bikin pickup baru selagi ada payment belum lunas, dan bisa lagi setelah lunas
- **Rule #2** — pickup cuma bisa di-schedule dari status `pending`, dan cuma bisa di-complete dari status `scheduled`
- **Rule #3** — payment otomatis ke-generate begitu pickup di-complete, amount sesuai tipe waste

Jalankan folder secara berurutan dari atas ke bawah karena saling bergantung state.

### Endpoint yang sudah tersedia

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/api/households` | Buat household baru |
| GET | `/api/households` | List household (search, filter `block`/`no`, paginate) |
| GET | `/api/households/{id}` | Detail household |
| PUT | `/api/households/{id}` | Update household |
| DELETE | `/api/households/{id}` | Hapus household |
| POST | `/api/pickups` | Buat waste pickup request baru (`type`: organic/plastic/paper/electronic) |
| GET | `/api/pickups` | List pickup (filter `status`/`type`/`household_id`, paginate) |
| PUT | `/api/pickups/{id}/schedule` | Jadwalkan pickup (electronic butuh `safety_confirmed: true` di body) |
| PUT | `/api/pickups/{id}/complete` | Tandai pickup selesai (hanya dari status `scheduled`) |
| PUT | `/api/pickups/{id}/cancel` | Batalkan pickup |
| POST | `/api/payments` | Buat payment manual (`waste_id` opsional untuk link ke pickup tertentu) |
| GET | `/api/payments` | List payment (filter `status`/`household_id`/`from`/`to`, paginate) |
| PUT | `/api/payments/{id}/confirm` | Konfirmasi payment lunas |

Endpoint Reporting menyusul di commit berikutnya.

### Auto-cancel Waste Organic

Waste `organic` yang masih `pending` (belum dijadwalkan) lebih dari **3 hari** otomatis dibatalkan sistem. Ini dijalankan oleh command `wastes:auto-cancel-organic`, dijadwalkan harian lewat Laravel Scheduler (`routes/console.php`).

Di Docker, penjadwalan ini dijalankan oleh service `scheduler` terpisah di `docker-compose.yml` (loop yang manggil `php artisan schedule:run` tiap 60 detik yang sama atau setara dengan cron `* * * * *`, tanpa perlu setup cron daemon di dalam container). Otomatis jalan begitu eksekusi `docker compose up`, sehingga tidak perlu langkah manual tambahan.

## Struktur Arsitektur

```
Controller → Service (business rule) → Repository (akses data) → Model (MongoDB)
```

- `app/Http/Controllers/Api/` — controller REST, tipis, tidak ada business logic
- `app/Services/` — business rule & orkestrasi
- `app/Repositories/Contracts/` + `app/Repositories/Eloquent/` — abstraksi akses data
- `app/Models/` — model Eloquent untuk MongoDB
- `app/Http/Requests/` — validasi input (Form Request)
- `app/Http/Resources/` — format response JSON

Response API konsisten dengan format:
```json
{ "success": true, "message": "...", "data": { ... } }
```
