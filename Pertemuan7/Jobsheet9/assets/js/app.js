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

// ===== Konfirmasi hapus (Jobsheet 9: form POST sungguhan ke hapus.php) =====
// Tombol Hapus berada di dalam <form class="form-hapus" method="post">.
// Konfirmasi dilakukan pada event "submit" supaya bisa dibatalkan
// (preventDefault) sebelum request terkirim ke server.
function initHapusConfirm() {
    document.addEventListener("submit", function (e) {
        const form = e.target;
        if (!form.classList.contains("form-hapus")) return;

        const row = form.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (!yakin) {
            e.preventDefault();
        }
    });
}

// ===== Latihan 9.1: konfirmasi sebelum Update =====
// Berbeda dari Hapus, Update tidak destruktif (data masih bisa diubah lagi),
// jadi konfirmasinya hanya ringan. Dipasang di fase bubbling (document), sehingga
// listener validasi di form berjalan lebih dulu; kalau validasi sudah
// membatalkan submit (defaultPrevented), konfirmasi tidak ditampilkan.
function initEditConfirm() {
    document.addEventListener("submit", function (e) {
        const form = e.target;
        if (!form.classList.contains("form-edit")) return;
        if (e.defaultPrevented) return;

        if (!confirm("Simpan perubahan data ini?")) {
            e.preventDefault();
        }
    });
}

// ===== Filter/pencarian tabel real-time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    // data-kolom boleh berisi beberapa indeks, mis. "0,1" (cocok jika salah satu kolom memuat kata kunci)
    const kolom = (input.dataset.kolom || "0").split(",").map(function (k) {
        return parseInt(k, 10) || 0;
    });
    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr:not(.row-pesan)");
        rows.forEach(function (row) {
            const cocok = kolom.some(function (k) {
                const sel = row.cells[k];
                return sel && sel.textContent.toLowerCase().includes(keyword);
            });
            row.style.display = cocok ? "" : "none";
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
    initEditConfirm();
    initTableFilter();
    initValidasiForm();
});
