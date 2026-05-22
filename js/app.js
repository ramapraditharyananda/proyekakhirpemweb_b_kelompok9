function hapusBaris(btn) {
  if (confirm('Yakin hapus data ini?')) {
    btn.closest('tr').remove();
    if (document.getElementById('tabelKategori')) renumber();
  }
}

function renumber() {
  document.querySelectorAll('#tabelKategori tbody tr').forEach(function(tr, i) {
    tr.cells[0].textContent = i + 1;
  });
}

function tambahKategori() {
  var input = document.getElementById('inputKategori');
  var ikonInput = document.getElementById('inputIkon');
  var nama = input.value.trim();
  var ikon = ikonInput ? ikonInput.value.trim() : '📦';
  if (!nama) { alert('Nama kategori tidak boleh kosong.'); return; }
  if (!ikon) ikon = '📦';
  var tbody = document.querySelector('#tabelKategori tbody');
  var n = tbody.rows.length + 1;
  var tr = document.createElement('tr');
  tr.innerHTML =
    '<td style="color:var(--text-light);font-size:12px;">' + n + '</td>' +
    '<td style="font-size:22px;text-align:center;">' + ikon + '</td>' +
    '<td><span style="font-weight:700;color:var(--text-dark);">' + nama + '</span></td>' +
    '<td><span class="adm-pill adm-pill-acc">0 produk</span></td>' +
    '<td><div class="adm-act-btns">' +
      '<button class="adm-btn-edit" onclick="editKategori(this)">Edit</button>' +
      '<button class="adm-btn-hapus" onclick="hapusBaris(this)">Hapus</button>' +
    '</div></td>';
  tbody.appendChild(tr);
  input.value = '';
  if (ikonInput) ikonInput.value = '';
}

function editKategori(btn) {
  var row = btn.closest('tr');
  var ikonCell = row.cells[1];
  var namaCell = row.cells[2];
  var currentIkon = ikonCell.textContent.trim();
  var currentNama = namaCell.querySelector('span') ? namaCell.querySelector('span').textContent : namaCell.textContent.trim();
  row.cells[4].innerHTML =
    '<div class="adm-act-btns">' +
      '<button class="adm-btn-acc" onclick="simpanEditKategori(this)">Simpan</button>' +
      '<button class="adm-btn-hapus" onclick="batalEditKategori(this,\'' + currentIkon + '\',\'' + currentNama + '\')">Batal</button>' +
    '</div>';
  ikonCell.innerHTML = '<input type="text" class="adm-input-inline" value="' + currentIkon + '" maxlength="4" style="width:54px;text-align:center;">';
  namaCell.innerHTML = '<input type="text" class="adm-input-inline" value="' + currentNama + '">';
}

function simpanEditKategori(btn) {
  var row = btn.closest('tr');
  var ikonInput = row.cells[1].querySelector('input');
  var namaInput = row.cells[2].querySelector('input');
  var ikon = ikonInput.value.trim() || '📦';
  var nama = namaInput.value.trim();
  if (!nama) { alert('Nama kategori tidak boleh kosong.'); return; }
  row.cells[1].innerHTML = ikon;
  row.cells[1].style.fontSize = '22px';
  row.cells[1].style.textAlign = 'center';
  row.cells[2].innerHTML = '<span style="font-weight:700;color:var(--text-dark);">' + nama + '</span>';
  row.cells[4].innerHTML =
    '<div class="adm-act-btns">' +
      '<button class="adm-btn-edit" onclick="editKategori(this)">Edit</button>' +
      '<button class="adm-btn-hapus" onclick="hapusBaris(this)">Hapus</button>' +
    '</div>';
}

function batalEditKategori(btn, ikon, nama) {
  var row = btn.closest('tr');
  row.cells[1].innerHTML = ikon;
  row.cells[1].style.fontSize = '22px';
  row.cells[1].style.textAlign = 'center';
  row.cells[2].innerHTML = '<span style="font-weight:700;color:var(--text-dark);">' + nama + '</span>';
  row.cells[4].innerHTML =
    '<div class="adm-act-btns">' +
      '<button class="adm-btn-edit" onclick="editKategori(this)">Edit</button>' +
      '<button class="adm-btn-hapus" onclick="hapusBaris(this)">Hapus</button>' +
    '</div>';
}

function setStatus(btn, type) {
  var row = btn.closest('tr');
  var newStatus = type === 'acc' ? 'Disetujui' : 'Ditolak';
  row.dataset.status = newStatus;
  row.cells[4].innerHTML = type === 'acc'
    ? '<span class="adm-pill adm-pill-acc">Disetujui</span>'
    : '<span class="adm-pill adm-pill-tolak">Ditolak</span>';
  row.cells[5].innerHTML = '<span style="color:var(--text-light);font-size:13px;">—</span>';
}

function filterPersetujuan() {
  var q = document.getElementById('searchPersetujuan') ? document.getElementById('searchPersetujuan').value.toLowerCase() : '';
  var kat = document.getElementById('filterKatPersetujuan') ? document.getElementById('filterKatPersetujuan').value : '';
  var status = document.getElementById('filterStatusPersetujuan') ? document.getElementById('filterStatusPersetujuan').value : '';
  document.querySelectorAll('#tabelPersetujuan tr').forEach(function(row) {
    var matchQ = !q || (row.dataset.nama || '').toLowerCase().includes(q) || (row.dataset.umkm || '').toLowerCase().includes(q);
    var matchKat = !kat || (row.dataset.kategori || '') === kat;
    var matchStatus = !status || (row.dataset.status || '') === status;
    row.style.display = (matchQ && matchKat && matchStatus) ? '' : 'none';
  });
}

function filterTabelProduk() {
  var q = document.getElementById('searchProduk') ? document.getElementById('searchProduk').value.toLowerCase() : '';
  var kat = document.getElementById('filterKategori') ? document.getElementById('filterKategori').value : '';
  var status = document.getElementById('filterStatus') ? document.getElementById('filterStatus').value : '';
  document.querySelectorAll('#tabelProduk tbody tr').forEach(function(row) {
    var matchQ = !q || (row.dataset.nama || '').toLowerCase().includes(q) || (row.dataset.umkm || '').toLowerCase().includes(q);
    var matchKat = !kat || (row.dataset.kategori || '') === kat;
    var matchStatus = !status || (row.dataset.status || '') === status;
    row.style.display = (matchQ && matchKat && matchStatus) ? '' : 'none';
  });
}

function filterTabelUMKM() {
  var q = document.getElementById('searchUMKM') ? document.getElementById('searchUMKM').value.toLowerCase() : '';
  var kat = document.getElementById('filterKategoriUMKM') ? document.getElementById('filterKategoriUMKM').value : '';
  var status = document.getElementById('filterStatusUMKM') ? document.getElementById('filterStatusUMKM').value : '';
  document.querySelectorAll('#tabelUMKM tbody tr').forEach(function(row) {
    var matchQ = !q || (row.dataset.nama || '').toLowerCase().includes(q) || (row.dataset.pemilik || '').toLowerCase().includes(q);
    var matchKat = !kat || (row.dataset.kategori || '') === kat;
    var matchStatus = !status || (row.dataset.status || '') === status;
    row.style.display = (matchQ && matchKat && matchStatus) ? '' : 'none';
  });
}

function filterTabelPengguna() {
  var q = document.getElementById('searchPengguna') ? document.getElementById('searchPengguna').value.toLowerCase() : '';
  var role = document.getElementById('filterRole') ? document.getElementById('filterRole').value : '';
  document.querySelectorAll('#tabelPengguna tbody tr').forEach(function(row) {
    var matchQ = !q || (row.dataset.nama || '').toLowerCase().includes(q) || (row.dataset.email || '').toLowerCase().includes(q);
    var matchRole = !role || (row.dataset.role || '') === role;
    row.style.display = (matchQ && matchRole) ? '' : 'none';
  });
}

function editPengguna(btn) {
  var row = btn.closest('tr');
  var namaCell = row.cells[0];
  var emailCell = row.cells[1];
  var roleCell = row.cells[2];
  var currentNama = namaCell.querySelector('strong') ? namaCell.querySelector('strong').textContent : namaCell.textContent.trim();
  var currentEmail = emailCell.textContent.trim();
  var currentRole = row.dataset.role || '';
  namaCell.innerHTML = '<input type="text" class="adm-input-inline" value="' + currentNama + '">';
  emailCell.innerHTML = '<input type="text" class="adm-input-inline" value="' + currentEmail + '">';
  roleCell.innerHTML =
    '<select class="adm-input-inline">' +
      '<option value="Admin"' + (currentRole === 'Admin' ? ' selected' : '') + '>Admin</option>' +
      '<option value="Pemilik UMKM"' + (currentRole === 'Pemilik UMKM' ? ' selected' : '') + '>Pemilik UMKM</option>' +
      '<option value="Pengunjung"' + (currentRole === 'Pengunjung' ? ' selected' : '') + '>Pengunjung</option>' +
    '</select>';
  row.cells[4].innerHTML =
    '<div class="adm-act-btns">' +
      '<button class="adm-btn-acc" onclick="simpanEditPengguna(this)">Simpan</button>' +
      '<button class="adm-btn-hapus" onclick="batalEditPengguna(this,\'' + currentNama + '\',\'' + currentEmail + '\',\'' + currentRole + '\')">Batal</button>' +
    '</div>';
}

function simpanEditPengguna(btn) {
  var row = btn.closest('tr');
  var nama = row.cells[0].querySelector('input').value.trim();
  var email = row.cells[1].querySelector('input').value.trim();
  var roleSelect = row.cells[2].querySelector('select');
  var role = roleSelect.value;
  if (!nama || !email) { alert('Nama dan email tidak boleh kosong.'); return; }
  var pilClass = role === 'Admin' ? 'adm-pill' : (role === 'Pemilik UMKM' ? 'adm-pill adm-pill-acc' : 'adm-pill adm-pill-pending');
  row.dataset.nama = nama;
  row.dataset.email = email;
  row.dataset.role = role;
  row.cells[0].innerHTML = '<strong>' + nama + '</strong>';
  row.cells[1].textContent = email;
  row.cells[2].innerHTML = '<span class="' + pilClass + '" style="' + (role === 'Admin' ? 'background:#eeedf8;color:var(--purple);' : '') + '">' + role + '</span>';
  if (role === 'Admin') {
    row.cells[4].innerHTML = '<span style="color:var(--text-light);font-size:13px;">—</span>';
  } else {
    row.cells[4].innerHTML =
      '<div class="adm-act-btns">' +
        '<button class="adm-btn-edit" onclick="editPengguna(this)">Edit</button>' +
        '<button class="adm-btn-hapus" onclick="hapusBaris(this)">Hapus</button>' +
      '</div>';
  }
}

function batalEditPengguna(btn, nama, email, role) {
  var row = btn.closest('tr');
  var pilClass = role === 'Admin' ? 'adm-pill' : (role === 'Pemilik UMKM' ? 'adm-pill adm-pill-acc' : 'adm-pill adm-pill-pending');
  row.cells[0].innerHTML = '<strong>' + nama + '</strong>';
  row.cells[1].textContent = email;
  row.cells[2].innerHTML = '<span class="' + pilClass + '" style="' + (role === 'Admin' ? 'background:#eeedf8;color:var(--purple);' : '') + '">' + role + '</span>';
  if (role === 'Admin') {
    row.cells[4].innerHTML = '<span style="color:var(--text-light);font-size:13px;">—</span>';
  } else {
    row.cells[4].innerHTML =
      '<div class="adm-act-btns">' +
        '<button class="adm-btn-edit" onclick="editPengguna(this)">Edit</button>' +
        '<button class="adm-btn-hapus" onclick="hapusBaris(this)">Hapus</button>' +
      '</div>';
  }
}
