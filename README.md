# Sistem Master Data & Transaksi Toko

Project ini adalah aplikasi berbasis **Laravel** untuk mengelola **master data toko, transaksi, area sales, dan sales person** dengan alur yang **terkontrol dan saling bergantung antar tabel** (tidak CRUD sembarangan).

---

## Struktur Database & Relasi

### 1. Table A – Mapping Kode Toko
**Fungsi:**  
Sebagai master utama untuk mendefinisikan **kode toko baru** dan opsional **kode toko lama**.

**Kolom:**
- `kode_toko_baru` (PK)
- `kode_toko_lama` (nullable)

**Catatan penting:**
- Semua data toko **harus terdaftar di Table A terlebih dahulu**
- Table A adalah **fondasi** untuk tabel lain

---

### 2. Table B – Transaksi
**Fungsi:**  
Menyimpan data transaksi per toko.

**Kolom:**
- `kode_toko`
- `nominal_transaksi`

**Aturan:**
- `kode_toko` **HARUS SUDAH ADA di Table A**
- Tidak boleh create/edit sembarangan
- Input transaksi **hanya lewat menu Transaksi**
- CRUD langsung Table B dibatasi (defensive route)

---

### 3. Table C – Area Sales
**Fungsi:**  
Menentukan area sales untuk tiap toko baru.

**Kolom:**
- `kode_toko`
- `area_sales`

**Aturan:**
- `kode_toko` **HARUS ADA di Table A**
- Satu toko = satu area
- Tidak bisa input area untuk toko yang belum terdaftar

---

### 4. Table D – Sales
**Fungsi:**  
Master data sales / salesman.

**Kolom:**
- `kode_sales` (PK)
- `nama_sales`

**Aturan:**
- Berdiri sendiri (tidak tergantung tabel lain)
- Kode sales harus unik
- Bisa input manual atau import CSV

---

## Alur Kerja Aplikasi (WAJIB URUT)

### 1️⃣ Input Master Toko (Table A)
- Tambah mapping toko baru
- Bisa manual atau import CSV
- Validasi:
  - `kode_toko_baru` unik
  - CSV harus sesuai header

➡️ **Tanpa Table A, tabel lain tidak bisa jalan**

---

### 2️⃣ Input Area Sales (Table C)
- Pilih `kode_toko` yang **sudah ada di Table A**
- Sistem menolak jika kode toko tidak ditemukan
- Bisa manual / import CSV

---

### 3️⃣ Input Transaksi (Table B)
- Input hanya lewat menu **Transaksi**
- Sistem memastikan:
  - `kode_toko` valid (ada di Table A)
  - Tidak duplikat transaksi untuk kode yang sama (jika diset unique)
- Tidak bisa edit transaksi mentah langsung dari Table B

---

### 4️⃣ Input Sales (Table D)
- Input bebas, tidak tergantung tabel lain
- Digunakan untuk keperluan reporting / analisis

---

## Import CSV – Mekanisme Aman

Setiap import CSV memiliki 2 tahap:

1. **Preview**
   - Validasi header
   - Validasi data per baris
   - Tampilkan error per baris (tidak silent)
2. **Confirm Import**
   - Hanya data **tanpa error** yang disimpan
   - Jika masih ada error → tombol confirm disabled

Contoh error yang ditampilkan:
- Header tidak sesuai
- Kode sudah ada
- Kode toko belum terdaftar di Table A
- Format data salah

---

## Defensive Design (Kenapa Tidak CRUD Bebas?)

Alasan:
- Menghindari data yatim (orphan data)
- Menjaga konsistensi laporan KPI
- Simulasi sistem real di perusahaan

Contoh:
- Table B tidak boleh edit langsung
- Create Table B diarahkan ke Transaksi
- Route yang “nyasar” diamankan dengan redirect

---

## Teknologi
- Laravel
- Blade Component
- Alpine.js
- Tailwind CSS
- DomPDF (Export PDF)
- CSV Import (custom validation)

---

## Catatan Akhir
Urutan data **WAJIB**:
> Table A → Table C → Transaksi (Table B) → Table D

Jika urutan dilanggar, sistem akan:
- Menolak input
- Menampilkan warning
- Tidak menyimpan data diam-diam

Ini **disengaja**, bukan bug.

---

