function hapusBaris(btn) {
  if (confirm('Yakin hapus data ini?')) btn.closest('tr').remove();
}

function renumber() {
  document.querySelectorAll('#tabelKategori tbody tr').forEach(function(tr, i) {
    tr.cells[0].textContent = i + 1;
  });
}

function tambahKategori() {
  var input = document.getElementById('inputKategori');
  var nama = input.value.trim();
  if (!nama) { alert('Nama kategori tidak boleh kosong.'); return; }
  var tbody = document.querySelector('#tabelKategori tbody');
  var n = tbody.rows.length + 1;
  var tr = document.createElement('tr');
  tr.innerHTML = '<td>' + n + '</td><td style="font-size:22px;text-align:center;">📦</td><td><span style="font-weight:700;color:var(--text-dark);">' + nama + '</span></td><td><span class="adm-pill adm-pill-acc">0 produk</span></td><td><div class="adm-act-btns"><button class="adm-btn-edit">Edit</button><button class="adm-btn-hapus" onclick="hapusBaris(this)">Hapus</button></div></td>';
  tbody.appendChild(tr);
  input.value = '';
}

function setStatus(btn, type) {
  var row = btn.closest('tr');
  row.cells[4].innerHTML = type === 'acc'
    ? '<span class="adm-pill adm-pill-acc">Disetujui</span>'
    : '<span class="adm-pill adm-pill-tolak">Ditolak</span>';
  row.cells[5].innerHTML = '<span style="color:var(--text-light);font-size:13px;">—</span>';
}
