// ===== Hamburger menu (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== Counter jumlah baris =====
function updateCounter() {
    const counter = document.getElementById("row-counter");
    const table = document.querySelector(".table-responsive table");
    if (!counter || !table) return;

    // baris pesan (loading/error) tidak dihitung sebagai data
    const rows = table.querySelectorAll("tbody tr:not(.row-pesan)");
    let tampil = 0;
    rows.forEach(function (row) {
        if (row.style.display !== "none") tampil++;
    });

    const satuan = counter.dataset.satuan || "data";
    counter.textContent = "Menampilkan " + tampil + " dari " + rows.length + " " + satuan;
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
// Jobsheet 6: memakai event delegation di document karena baris tabel sekarang
// dirender dinamis via fetch (lihat buku.js/anggota.js) sehingga
// tombol .btn-hapus belum tentu ada saat DOMContentLoaded.
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        console.log(e.target); // pengamatan event delegation (Ide 8.4 no. 4); hapus setelah selesai mencoba
        const btn = e.target.closest(".btn-hapus");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (yakin && row) {
            row.remove();
            updateCounter();
        }
    });
}

// ===== Filter/pencarian tabel real-time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    const kolom = parseInt(input.dataset.kolom, 10) || 0;
    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr:not(.row-pesan)");
        rows.forEach(function (row) {
            const sel = row.cells[kolom];
            const teks = sel ? sel.textContent.toLowerCase() : "";
            row.style.display = teks.includes(keyword) ? "" : "none";
        });
        updateCounter();
    });
    updateCounter(); // tampilkan counter awal saat halaman dibuka
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

const ATURAN_VALIDASI = [
    {
        // Form Tambah Buku (name="judul") dan Tambah Anggota (name="nama_lengkap")
        selector: "[name='judul'], [name='nama_lengkap']",
        cek: function (nilai) {
            return nilai.trim() === "" ? "Field ini wajib diisi." : null;
        }
    },
    {
        selector: "[name='pengarang']",
        cek: function (nilai) {
            return nilai.trim() === "" ? "Pengarang wajib diisi." : null;
        }
    },
    {
        selector: "[name='email']",
        cek: function (nilai) {
            if (nilai.trim() === "") return "Email wajib diisi.";
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nilai.trim())) {
                return "Format email tidak valid.";
            }
            return null;
        }
    },
    {
        selector: "[name='no_anggota']",
        cek: function (nilai) {
            return nilai.trim() === "" ? "No. Anggota wajib diisi." : null;
        }
    },
    {
        selector: "[name='tanggal_lahir']",
        cek: function (nilai) {
            return nilai === "" ? "Tanggal lahir wajib diisi." : null;
        }
    },
    {
        selector: "[name='tahun']",
        cek: function (nilai) {
            const angka = parseInt(nilai, 10);
            if (isNaN(angka) || angka < 1900 || angka > 2026) {
                return "Tahun harus di antara 1900-2026.";
            }
            return null;
        }
    },
    {
        selector: "[name='stok']",
        cek: function (nilai) {
            const angka = parseInt(nilai, 10);
            if (isNaN(angka) || angka < 0) {
                return "Stok tidak boleh negatif.";
            }
            return null;
        }
    },
    {
        // ISBN tidak wajib, tapi kalau diisi hanya boleh angka dan tanda hubung
        selector: "[name='isbn']",
        cek: function (nilai) {
            const teks = nilai.trim();
            if (teks !== "" && !/^[0-9-]+$/.test(teks)) {
                return "ISBN hanya boleh berisi angka dan tanda hubung (-).";
            }
            return null;
        }
    }
];

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        ATURAN_VALIDASI.forEach(function (aturan) {
            const field = form.querySelector(aturan.selector);
            if (!field) return; // field ini tidak ada di form yang sedang dibuka

            const pesan = aturan.cek(field.value);
            if (pesan) {
                tampilkanError(field, pesan);
                valid = false;
            } else {
                hapusError(field);
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});
