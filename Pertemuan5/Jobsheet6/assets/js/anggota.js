// Daftar Anggota: konfigurasi untuk muatTabelJSON (lihat tabel.js)
function muatDaftarAnggota() {
    return muatTabelJSON({
        url: "../data/anggota.json",
        kolom: [
            "no_anggota",
            "nama",
            "alamat",
            "no_hp",
            function (a) { return a.jenis_kelamin === "P" ? "v" : ""; },
            function (a) { return a.jenis_kelamin === "L" ? "v" : ""; },
            function () { return "<button type=\"button\" class=\"btn-edit\">Edit</button>"; },
            function () { return "<button type=\"button\" class=\"btn-hapus\">Hapus</button>"; }
        ]
    });
}

document.addEventListener("DOMContentLoaded", muatDaftarAnggota);
