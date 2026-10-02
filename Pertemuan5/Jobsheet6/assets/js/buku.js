// Daftar Buku: konfigurasi untuk muatTabelJSON (lihat tabel.js)
function muatDaftarBuku() {
    return muatTabelJSON({
        url: "../data/buku.json",
        kolom: [
            "judul",
            "pengarang",
            "tahun",
            "stok",
            "kategori",
            function () {
                return "<button type=\"button\" class=\"btn-edit\">Edit</button> " +
                       "<button type=\"button\" class=\"btn-detail\">Detail</button> " +
                       "<button type=\"button\" class=\"btn-hapus\">Hapus</button>";
            }
        ]
    });
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();

    // Tombol "Muat Ulang": dinonaktifkan selama memuat agar tidak terpanggil dobel
    const btnMuatUlang = document.getElementById("btn-muat-ulang");
    if (btnMuatUlang) {
        btnMuatUlang.addEventListener("click", async function () {
            btnMuatUlang.disabled = true;
            await muatDaftarBuku();
            btnMuatUlang.disabled = false;
        });
    }
});
