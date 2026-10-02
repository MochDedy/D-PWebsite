const DELAY_SIMULASI_MS = 3000;

// opsi.url   : alamat file JSON
// opsi.kolom : daftar kolom, urutannya sama dengan <th>. Isinya bisa
//              - string   -> nama kunci objek, mis. "judul"
//              - function -> menerima satu objek data, mengembalikan teks HTML sel
async function muatTabelJSON(opsi) {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody || !loading) return;

    // kosongkan kolom pencarian supaya tidak bertentangan dengan data yang baru dimuat
    const inputCari = document.getElementById("search-input");
    if (inputCari) inputCari.value = "";

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, DELAY_SIMULASI_MS));

        const res = await fetch(opsi.url);
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const daftar = await res.json();

        daftar.forEach(function (item) {
            const tr = document.createElement("tr");
            tr.innerHTML = opsi.kolom.map(function (kolom) {
                const isi = typeof kolom === "function" ? kolom(item) : item[kolom];
                return "<td>" + isi + "</td>";
            }).join("");
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML =
            "<tr class=\"row-pesan\"><td colspan=\"" + opsi.kolom.length + "\">Gagal memuat data: " +
            err.message + "</td></tr>";
    } finally {
        loading.style.display = "none";
        updateCounter();
    }
}
