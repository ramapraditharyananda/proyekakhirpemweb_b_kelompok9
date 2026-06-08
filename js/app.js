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

  if (!nama) {
    alert('Nama kategori tidak boleh kosong.');
    return;
  }

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

  if (!nama) {
    alert('Nama kategori tidak boleh kosong.');
    return;
  }

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

  if (!nama || !email) {
    alert('Nama dan email tidak boleh kosong.');
    return;
  }

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

function lihatDetailUMKM(btn) {
  var row = btn.closest('tr');
  var nama = row.dataset.nama || '';
  var pemilik = row.dataset.pemilik || '';
  var kategori = row.dataset.kategori || '';
  var status = row.dataset.status || '';
  var alamat = row.dataset.alamat || '';
  var wa = row.dataset.wa || '';
  var produkCell = row.cells[3].querySelector('.adm-pill') ? row.cells[3].querySelector('.adm-pill').textContent : '-';

  document.getElementById('detailNamaToko').textContent = nama || '—';
  document.getElementById('detailPemilik').textContent = pemilik || '—';
  document.getElementById('detailKategori').textContent = kategori || '—';
  document.getElementById('detailAlamat').textContent = alamat || '—';
  document.getElementById('detailWa').textContent = wa || '—';
  document.getElementById('detailStatus').innerHTML = status === 'Aktif'
    ? '<span class="adm-pill adm-pill-acc">Aktif</span>'
    : '<span class="adm-pill adm-pill-tolak">Nonaktif</span>';
  document.getElementById('detailProduk').textContent = produkCell || '—';
  document.getElementById('modalDetailUMKM').classList.add('show');
}

function tutupModalUMKM() {
  document.getElementById('modalDetailUMKM').classList.remove('show');
}

function previewGambar(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      document.getElementById('previewImg').src = e.target.result;
      document.getElementById('previewWrap').style.display = 'block';
    };

    reader.readAsDataURL(input.files[0]);
  }
}

function submitForm() {
  var valid = true;
  var fields = [
    { id: 'namaProduk', err: 'errNama', check: function(v) { return v.trim() !== ''; } },
    { id: 'kategori', err: 'errKategori', check: function(v) { return v !== ''; } },
    { id: 'harga', err: 'errHarga', check: function(v) { return v !== '' && Number(v) >= 0; } },
    { id: 'deskripsi', err: 'errDeskripsi', check: function(v) { return v.trim() !== ''; } }
  ];

  fields.forEach(function(f) {
    var el = document.getElementById(f.id);
    var err = document.getElementById(f.err);

    if (el && !f.check(el.value)) {
      err.style.display = 'block';
      valid = false;
    } else if (err) {
      err.style.display = 'none';
    }
  });

  if (valid) {
    document.getElementById('formTambahProdukSubmit').submit();
  }
}

var selectedRole = '';

function pilihRole(role) {
  selectedRole = role;

  document.querySelectorAll('.role-option').forEach(function(el) {
    el.classList.remove('active');
  });

  var el = document.getElementById('role-' + role);
  if (el) el.classList.add('active');

  var hidden = document.getElementById('hiddenRole');
  if (hidden) hidden.value = role;
}

function showFieldError(inputId, msgId, message) {
  var input = document.getElementById(inputId);
  var msg = document.getElementById(msgId);

  if (input) {
    input.classList.add('input-error');
    input.style.animation = 'none';
    input.offsetHeight;
    input.style.animation = '';

    input.addEventListener('input', function clearOnType() {
      clearFieldError(inputId, msgId);
      input.removeEventListener('input', clearOnType);
    });
  }

  if (msg) {
    if (message) msg.textContent = '\u26a0 ' + message;
    msg.classList.add('show');
  }
}

function clearFieldError(inputId, msgId) {
  var input = document.getElementById(inputId);
  var msg = document.getElementById(msgId);

  if (input) input.classList.remove('input-error');
  if (msg) msg.classList.remove('show');
}

function doLogin() {
  var email = document.getElementById('email').value.trim();
  var password = document.getElementById('password').value.trim();
  var notif = document.getElementById('notifMsg');

  if (notif) {
    notif.style.display = 'none';
    notif.textContent = '';
  }

  if (!email && !password) {
    if (notif) {
      notif.textContent = 'Email dan password wajib diisi.';
      notif.style.display = 'block';
    }
    return;
  }

  if (!email) {
    if (notif) {
      notif.textContent = 'Email wajib diisi.';
      notif.style.display = 'block';
    }
    return;
  }

  if (!password) {
    if (notif) {
      notif.textContent = 'Password wajib diisi.';
      notif.style.display = 'block';
    }
    return;
  }

  document.getElementById('loginForm').submit();
}

function doRegister() {
  var nama = document.getElementById('nama') ? document.getElementById('nama').value.trim() : '';
  var email = document.getElementById('email').value.trim();
  var pass = document.getElementById('password').value;
  var konfirm = document.getElementById('konfirmasi') ? document.getElementById('konfirmasi').value : '';
  var terms = document.getElementById('terms') ? document.getElementById('terms').checked : true;
  var notif = document.getElementById('notifMsg');

  if (notif) {
    notif.style.display = 'none';
    notif.textContent = '';
  }

  if (!nama) {
    if (notif) {
      notif.textContent = 'Nama lengkap wajib diisi.';
      notif.style.display = 'block';
    }
    document.getElementById('nama') && document.getElementById('nama').focus();
    return;
  }

  if (!email) {
    if (notif) {
      notif.textContent = 'Email wajib diisi.';
      notif.style.display = 'block';
    }
    document.getElementById('email').focus();
    return;
  }

  if (!pass) {
    if (notif) {
      notif.textContent = 'Password wajib diisi.';
      notif.style.display = 'block';
    }
    document.getElementById('password').focus();
    return;
  }

  if (pass.length < 8) {
    if (notif) {
      notif.textContent = 'Password minimal 8 karakter.';
      notif.style.display = 'block';
    }
    document.getElementById('password').focus();
    return;
  }

  if (!konfirm) {
    if (notif) {
      notif.textContent = 'Konfirmasi password wajib diisi.';
      notif.style.display = 'block';
    }
    document.getElementById('konfirmasi') && document.getElementById('konfirmasi').focus();
    return;
  }

  if (pass !== konfirm) {
    if (notif) {
      notif.textContent = 'Password dan konfirmasi password tidak cocok.';
      notif.style.display = 'block';
    }
    document.getElementById('konfirmasi') && document.getElementById('konfirmasi').focus();
    return;
  }

  if (!terms) {
    if (notif) {
      notif.textContent = 'Setujui syarat & ketentuan terlebih dahulu.';
      notif.style.display = 'block';
    }
    return;
  }

  if (!selectedRole) {
    if (notif) {
      notif.textContent = 'Pilih peran terlebih dahulu (Pengunjung atau Pemilik UMKM).';
      notif.style.display = 'block';
    }
    return;
  }

  document.getElementById('registerForm').submit();
}

(function() {
  var params = new URLSearchParams(window.location.search);
  var errorMsg = params.get('error');
  var successMsg = params.get('success');
  var notifEl = document.getElementById('notifMsg');

  if (notifEl) {
    if (errorMsg) {
      notifEl.textContent = errorMsg;
      notifEl.style.color = 'red';
      notifEl.style.display = 'block';
    }

    if (successMsg) {
      notifEl.textContent = successMsg;
      notifEl.style.color = 'green';
      notifEl.style.display = 'block';
    }
  }
})();

var statusPillKartu = {
  tayang: '<span class="adm-pill adm-pill-acc" style="font-size:10px;">Tayang</span>',
  menunggu: '<span class="adm-pill adm-pill-pending" style="font-size:10px;">Menunggu</span>',
  ditolak: '<span class="adm-pill adm-pill-tolak" style="font-size:10px;">Ditolak</span>'
};

var statusPillModal = {
  tayang: '<span class="adm-pill adm-pill-acc">Tayang</span>',
  menunggu: '<span class="adm-pill adm-pill-pending">Menunggu Persetujuan</span>',
  ditolak: '<span class="adm-pill adm-pill-tolak">Ditolak</span>'
};

function formatRupiah(angka) {
  return 'Rp ' + parseInt(angka || 0).toLocaleString('id-ID');
}

function renderProduk() {
  var grid = document.getElementById('gridProduk');

  if (!grid) return;

  grid.innerHTML = '';

  if (!dataProduk || dataProduk.length === 0) {
    grid.innerHTML = '<p style="color:var(--text-light);font-size:14px;">Belum ada produk. Silakan tambah produk baru.</p>';
    return;
  }

  dataProduk.forEach(function(p, i) {
    var card = document.createElement('div');
    card.className = 'adm-card';
    card.style.cursor = 'pointer';
    card.onclick = (function(idx) {
      return function() {
        bukaDetail(idx);
      };
    })(i);

    var fotoSrc = p.foto ? '../../../images/' + p.foto : '../../../images/placeholder.jpg';

    card.innerHTML =
      '<img src="' + fotoSrc + '" alt="' + p.nama + '">' +
      '<div class="adm-overlay">' +
        '<div class="adm-cat-label">' + (p.nama_kategori || '-') + '</div>' +
        '<div class="adm-card-bottom">' +
          '<div>' +
            '<h3>' + p.nama + '</h3>' +
            '<div style="color:rgba(255,255,255,0.6);font-size:11px;">' + formatRupiah(p.harga) + '</div>' +
          '</div>' +
          '<div class="adm-card-actions">' +
            (statusPillKartu[p.status] || '') +
            '<span style="color:rgba(255,255,255,0.55);font-size:10px;margin-top:3px;">🔍 Lihat Detail</span>' +
          '</div>' +
        '</div>' +
      '</div>';

    grid.appendChild(card);
  });
}

var indexAktif = -1;

function bukaDetail(i) {
  indexAktif = i;

  var p = dataProduk[i];
  var fotoSrc = p.foto ? '../../../images/' + p.foto : '../../../images/placeholder.jpg';

  document.getElementById('modalNama').textContent = p.nama;
  document.getElementById('modalKategori').textContent = p.nama_kategori || '-';
  document.getElementById('modalHarga').textContent = formatRupiah(p.harga);
  document.getElementById('modalDeskripsi').textContent = p.deskripsi;
  document.getElementById('modalStatusTayang').innerHTML = statusPillModal[p.status] || p.status;
  document.getElementById('modalGambar').src = fotoSrc;
  document.getElementById('modalGambar').alt = p.nama;

  var toggle = document.getElementById('toggleStok');

  toggle.checked = (p.status === 'tayang' || p.status === 'tersedia');
  document.getElementById('labelStok').textContent = toggle.checked ? 'Tersedia' : 'Stok Habis';
  document.getElementById('modalDetail').classList.add('show');
}

function ubahStatusStok(checkbox) {
  document.getElementById('labelStok').textContent = checkbox.checked ? 'Tersedia' : 'Stok Habis';
}

function simpanStatusStok() {
  if (indexAktif < 0) return;

  var tersedia = document.getElementById('toggleStok').checked;
  var p = dataProduk[indexAktif];
  var stokBaru = tersedia ? 'tersedia' : 'habis';
  var form = document.createElement('form');

  form.method = 'POST';
  form.action = 'dashboard-pemilik.php';

  var inputAksi = document.createElement('input');
  inputAksi.type = 'hidden';
  inputAksi.name = 'aksi';
  inputAksi.value = 'ubah_stok';

  var inputId = document.createElement('input');
  inputId.type = 'hidden';
  inputId.name = 'id_produk';
  inputId.value = p.id;

  var inputStok = document.createElement('input');
  inputStok.type = 'hidden';
  inputStok.name = 'stok';
  inputStok.value = stokBaru;

  form.appendChild(inputAksi);
  form.appendChild(inputId);
  form.appendChild(inputStok);
  document.body.appendChild(form);
  form.submit();
}

function updateModalWishlistBtn(id) {
  var inWish = wishlist.includes(id);
  var btn = document.getElementById('mdWishlistBtn');
  var icon = document.getElementById('mdWishlistIcon');

  if (icon) icon.textContent = inWish ? '❤️' : '🤍';
  if (btn) btn.classList.toggle('active', inWish);
}

function toggleWishlistFromModal() {
  if (!currentProdukId) return;

  toggleWishlistItem(currentProdukId);
  updateModalWishlistBtn(currentProdukId);
}

function toggleWishlistItem(id, btn) {
  var idx = wishlist.indexOf(id);
  var p = produkData.find(function(x) {
    return x.id == id;
  });

  if (!p) return;

  if (idx === -1) {
    wishlist.push(id);
    showToast('❤️ ' + p.nama + ' ditambahkan ke wishlist');
  } else {
    wishlist.splice(idx, 1);
    showToast('🤍 ' + p.nama + ' dihapus dari wishlist');
  }

  updateWishlistCount();
  updateWishlistPanel();

  if (btn) {
    btn.textContent = wishlist.includes(id) ? '❤️' : '🤍';
    btn.classList.toggle('active', wishlist.includes(id));
  }
}

function updateWishlistCount() {
  var cnt = document.getElementById('wishlistCount');

  if (!cnt) return;

  cnt.textContent = wishlist.length;
  cnt.style.display = wishlist.length > 0 ? 'flex' : 'none';
}

function tutupModalDetail(e) {
  if (e.target === document.getElementById('modalDetail')) {
    document.getElementById('modalDetail').classList.remove('show');
  }
}

function bukaModal() {
  document.getElementById('modalProfil').classList.add('show');
}

function tutupModal() {
  document.getElementById('modalProfil').classList.remove('show');
}

function tutupModalLuar(e) {
  if (e.target === document.getElementById('modalProfil')) tutupModal();
}

function simpanProfil(e) {
  e.preventDefault();
  tutupModal();
}

if (document.getElementById('gridProduk')) {
  renderProduk();
}

var activeKategoriPengunjung = '';
var activeViewMode = 'grid';
var wishlist = [];
var currentProdukId = null;
var currentUmkmName = '';
var currentUmkmWa = '';

function formatRupiahPengunjung(angka) {
  return 'Rp ' + parseInt(angka || 0).toLocaleString('id-ID');
}

function getFotoProduk(p) {
  if (p && p.foto_url && String(p.foto_url).trim() !== '') {
    return p.foto_url;
  }

  if (p && p.foto && String(p.foto).trim() !== '') {
    return '../../images/' + p.foto;
  }

  return '../../images/UMKMify_Logo_Color.png';
}

function fallbackFoto(el) {
  el.onerror = null;
  el.src = '../../images/UMKMify_Logo_Color.png';
}

function renderProdukBaru() {
  var container = document.getElementById('produkBaruScroll');

  if (!container || typeof produkBaruData === 'undefined' || !produkBaruData) return;

  container.innerHTML = '';

  produkBaruData.forEach(function(p) {
    var fotoSrc = getFotoProduk(p);
    var card = document.createElement('div');

    card.className = 'produk-baru-card';
    card.onclick = function() {
      openProdukDetail(p.id);
    };

    card.innerHTML =
      '<img class="produk-baru-img" src="' + fotoSrc + '" alt="' + (p.nama || '') + '" onerror="fallbackFoto(this)">' +
      '<div class="produk-baru-body">' +
        '<div class="produk-baru-kat">' + (p.kategori_ikon || '') + ' ' + (p.kategori || '-') + '</div>' +
        '<div class="produk-baru-nama">' + (p.nama || '-') + '</div>' +
        '<div class="produk-baru-toko">' + (p.nama_toko || '-') + '</div>' +
        '<div class="produk-baru-harga">' + formatRupiahPengunjung(p.harga) + '</div>' +
      '</div>';

    container.appendChild(card);
  });
}

function renderProdukPengunjung(data) {
  var grid = document.getElementById('produkGrid');
  var noRes = document.getElementById('noResults');

  if (!grid) return;

  grid.querySelectorAll('.produk-card').forEach(function(c) {
    c.remove();
  });

  if (!data || data.length === 0) {
    if (noRes) noRes.style.display = 'block';
    return;
  }

  if (noRes) noRes.style.display = 'none';

  data.forEach(function(p) {
    var inWish = wishlist.includes(p.id);
    var fotoSrc = getFotoProduk(p);
    var hargaStr = formatRupiahPengunjung(p.harga);
    var card = document.createElement('div');

    card.className = 'produk-card' + (activeViewMode === 'list' ? ' produk-card-list' : '');
    card.dataset.kategori = p.kategori || '';
    card.dataset.nama = p.nama || '';
    card.dataset.toko = p.nama_toko || '';
    card.dataset.harga = p.harga || 0;

    card.innerHTML =
      '<div class="produk-img-wrap">' +
        '<img src="' + fotoSrc + '" alt="' + (p.nama || '') + '" onerror="fallbackFoto(this)">' +
        '<span class="produk-cat-badge">' + (p.kategori || '-') + '</span>' +
        '<button class="produk-wishlist-btn ' + (inWish ? 'active' : '') + '" onclick="event.stopPropagation(); toggleWishlistItem(' + p.id + ', this)" title="' + (inWish ? 'Hapus dari wishlist' : 'Simpan ke wishlist') + '">' +
          (inWish ? '❤️' : '🤍') +
        '</button>' +
      '</div>' +
      '<div class="produk-body" onclick="openProdukDetail(' + p.id + ')">' +
        '<div class="produk-toko">' + (p.nama_toko || '-') + '</div>' +
        '<div class="produk-nama">' + (p.nama || '-') + '</div>' +
        '<div class="produk-desc">' + ((p.deskripsi || '').substring(0, 70)) + ((p.deskripsi || '').length > 70 ? '...' : '') + '</div>' +
        '<div class="produk-footer">' +
          '<span class="produk-harga">' + hargaStr + '</span>' +
          '<button class="produk-detail-btn" onclick="event.stopPropagation(); openProdukDetail(' + p.id + ')">Lihat Detail</button>' +
        '</div>' +
      '</div>';

    grid.insertBefore(card, noRes);
  });
}

function renderUMKM() {
  var grid = document.getElementById('umkmGrid');

  if (!grid || !tokoData) return;

  var q = document.getElementById('umkmSearchInput') ? document.getElementById('umkmSearchInput').value.toLowerCase() : '';
  var katFilter = document.getElementById('umkmKategoriFilter') ? document.getElementById('umkmKategoriFilter').value : '';

  var filtered = tokoData.filter(function(u) {
    var matchQ = !q || (u.nama_toko || '').toLowerCase().includes(q) || (u.pemilik || '').toLowerCase().includes(q);
    var matchKat = !katFilter || (u.kategori || '') === katFilter;

    return matchQ && matchKat;
  });

  grid.innerHTML = '';

  if (filtered.length === 0) {
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-light);font-size:14px;">Tidak ada UMKM ditemukan.</div>';
    return;
  }

  filtered.forEach(function(u) {
    var realIdx = tokoData.indexOf(u);
    var card = document.createElement('div');
    var statusBadge = (u.status === 'aktif' || u.status === 'Aktif')
      ? '<span class="umkm-status-badge aktif">● Aktif</span>'
      : '<span class="umkm-status-badge nonaktif">● Nonaktif</span>';

    card.className = 'umkm-card';
    var reportNama = String(u.nama_toko || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    card.onclick = function() { openUmkmDetail(realIdx); };
    card.innerHTML =
      '<div class="umkm-icon">🏪</div>' +
      '<div class="umkm-info">' +
        '<div class="umkm-name">' + (u.nama_toko || '-') + '</div>' +
        statusBadge +
        '<div class="umkm-cat">' + (u.kategori || '-') + '</div>' +
        '<div class="umkm-count">🏷️ ' + (u.jumlah_produk || 0) + ' produk</div>' +
        '<div class="umkm-location">📍 ' + (u.alamat || '-') + '</div>' +
      '</div>' +
      '<div class="umkm-actions">' +
        '<button class="umkm-detail-btn" onclick="event.stopPropagation(); openUmkmDetail(' + realIdx + ')">Lihat Detail</button>' +
        '<button class="umkm-report-btn" onclick="event.stopPropagation(); openReport(\'' + reportNama + '\')" title="Laporkan UMKM">🚩</button>' +
      '</div>';

    grid.appendChild(card);
  });
}

function filterUMKM() {
  renderUMKM();
}

function setView(mode) {
  activeViewMode = mode;

  var btnGrid = document.getElementById('viewGrid');
  var btnList = document.getElementById('viewList');

  if (btnGrid) btnGrid.classList.toggle('active', mode === 'grid');
  if (btnList) btnList.classList.toggle('active', mode === 'list');

  var grid = document.getElementById('produkGrid');

  if (grid) {
    grid.classList.toggle('produk-grid-list', mode === 'list');
  }

  document.querySelectorAll('.produk-card').forEach(function(card) {
    card.classList.toggle('produk-card-list', mode === 'list');
  });
}

function sortProduk(val) {
  if (!produkData) return;

  var sorted = getFilteredProduk();

  if (val === 'harga-asc') {
    sorted.sort(function(a, b) {
      return a.harga - b.harga;
    });
  } else if (val === 'harga-desc') {
    sorted.sort(function(a, b) {
      return b.harga - a.harga;
    });
  } else if (val === 'nama-asc') {
    sorted.sort(function(a, b) {
      return String(a.nama || '').localeCompare(String(b.nama || ''));
    });
  }

  renderProdukPengunjung(sorted);
}

function getFilteredProduk() {
  if (!produkData) return [];

  var q = document.getElementById('searchInput') ? document.getElementById('searchInput').value.toLowerCase() : '';

  return produkData.filter(function(p) {
    var matchKat = !activeKategoriPengunjung || (p.kategori || '') === activeKategoriPengunjung;
    var matchQ = !q || (p.nama || '').toLowerCase().includes(q) || (p.nama_toko || '').toLowerCase().includes(q);

    return matchKat && matchQ;
  });
}

function setFilter(el, kat) {
  activeKategoriPengunjung = kat;

  document.querySelectorAll('.filter-chip').forEach(function(c) {
    c.classList.remove('active');
  });

  el.classList.add('active');
  renderProdukPengunjung(getFilteredProduk());
}

function filterProduk() {
  renderProdukPengunjung(getFilteredProduk());
}

function openProdukDetail(id) {
  var p = produkData.find(function(x) {
    return x.id == id;
  });

  if (!p) return;

  currentProdukId = id;

  var fotoSrc = getFotoProduk(p);
  var hargaStr = formatRupiahPengunjung(p.harga);
  var wa = p.no_wa ? 'https://wa.me/' + String(p.no_wa).replace(/[^0-9]/g, '') : '#';
  var mdImg = document.getElementById('mdImg');

  if (mdImg) {
    mdImg.src = fotoSrc;
    mdImg.onerror = function() {
      fallbackFoto(this);
    };
  }

  document.getElementById('mdKat').textContent = p.kategori || '-';
  document.getElementById('mdNama').textContent = p.nama || '-';
  document.getElementById('mdToko').textContent = '🏪 ' + (p.nama_toko || '-');
  document.getElementById('mdHarga').textContent = hargaStr;
  document.getElementById('mdDesc').textContent = p.deskripsi || '';
  document.getElementById('mdKatInfo').textContent = p.kategori || '-';
  document.getElementById('mdTokoInfo').textContent = p.nama_toko || '-';
  document.getElementById('mdLokasi').textContent = p.lokasi || '-';
  document.getElementById('mdWaLink').href = wa;

  updateModalWishlistBtn(id);

  document.getElementById('modalDetail').classList.add('show');
}
