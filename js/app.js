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
  tr.innerHTML = '<td>' + n + '</td><td>' + nama + '</td><td>0</td><td><div class="adm-act-btns"><button class="adm-btn-edit">Edit</button><button class="adm-btn-hapus" onclick="hapusBaris(this)">Hapus</button></div></td>';
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
    if (el && !f.check(el.value)) { err.style.display = 'block'; valid = false; }
    else if (err) { err.style.display = 'none'; }
  });
  if (valid) {
    alert('Produk berhasil diajukan! Menunggu persetujuan admin.');
    window.location.href = 'dashboard-pemilik.html';
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
}

function doLogin() {
  var email = document.getElementById('email').value.trim();
  var password = document.getElementById('password').value.trim();

  if (!email || !password) {
    alert('Email dan password wajib diisi.');
    return;
  }

  if (!selectedRole) {
    alert('Pilih peran terlebih dahulu.');
    return;
  }

  if (selectedRole === 'admin') {
    if (email === 'admin@umkmify.com' && password === 'admin123') {
      window.location.href = 'dashboard-admin.html';
    } else {
      alert('Email atau password admin salah!');
    }
  } else if (selectedRole === 'pemilik') {
    window.location.href = 'dashboard-pemilik.html';
  } else {
    window.location.href = 'dashboard_pengunjung.html';
  }
}

function doRegister() {
  var nama = document.getElementById('nama') ? document.getElementById('nama').value.trim() : '';
  var email = document.getElementById('email').value.trim();
  var pass = document.getElementById('password').value;
  var konfirm = document.getElementById('konfirmasi') ? document.getElementById('konfirmasi').value : '';
  var terms = document.getElementById('terms') ? document.getElementById('terms').checked : true;
  if (!nama || !email || !pass) { alert('Semua field wajib diisi.'); return; }
  if (pass !== konfirm) { alert('Password tidak cocok.'); return; }
  if (!terms) { alert('Setujui syarat & ketentuan terlebih dahulu.'); return; }
  if (!selectedRole) { alert('Pilih peran terlebih dahulu.'); return; }
  alert('Akun berhasil dibuat! Silakan login.');
  window.location.href = 'login.html?role=' + selectedRole;
}

var activeKategori = '';

function setFilter(el, kat) {
  activeKategori = kat;
  document.querySelectorAll('.filter-chip').forEach(function(c) {
    c.classList.remove('active');
  });
  el.classList.add('active');
  filterProduk();
}

function filterProduk() {
  var searchEl = document.getElementById('searchInput');
  var q = searchEl ? searchEl.value.toLowerCase() : '';
  document.querySelectorAll('.produk-card').forEach(function(card) {
    var matchKat = !activeKategori || card.dataset.kategori === activeKategori;
    var matchCari = !q || card.dataset.nama.toLowerCase().includes(q) || card.dataset.toko.toLowerCase().includes(q);
    card.style.display = (matchKat && matchCari) ? '' : 'none';
  });
}

function filterTabelProduk() {
  var q = (document.getElementById('searchProduk') ? document.getElementById('searchProduk').value.toLowerCase() : '');
  var kat = (document.getElementById('filterKategori') ? document.getElementById('filterKategori').value : '');
  var status = (document.getElementById('filterStatus') ? document.getElementById('filterStatus').value : '');
  var rows = document.querySelectorAll('#tabelProduk tbody tr');
  rows.forEach(function(row) {
    var nama = (row.dataset.nama || '').toLowerCase();
    var umkm = (row.dataset.umkm || '').toLowerCase();
    var kategori = row.dataset.kategori || '';
    var rowStatus = row.dataset.status || '';
    var matchQ = !q || nama.includes(q) || umkm.includes(q);
    var matchKat = !kat || kategori === kat;
    var matchStatus = !status || rowStatus === status;
    row.style.display = (matchQ && matchKat && matchStatus) ? '' : 'none';
  });
}

function filterTabelUMKM() {
  var q = (document.getElementById('searchUMKM') ? document.getElementById('searchUMKM').value.toLowerCase() : '');
  var kat = (document.getElementById('filterKategoriUMKM') ? document.getElementById('filterKategoriUMKM').value : '');
  var status = (document.getElementById('filterStatusUMKM') ? document.getElementById('filterStatusUMKM').value : '');
  var rows = document.querySelectorAll('#tabelUMKM tbody tr');
  rows.forEach(function(row) {
    var nama = (row.dataset.nama || '').toLowerCase();
    var pemilik = (row.dataset.pemilik || '').toLowerCase();
    var kategori = row.dataset.kategori || '';
    var rowStatus = row.dataset.status || '';
    var matchQ = !q || nama.includes(q) || pemilik.includes(q);
    var matchKat = !kat || kategori === kat;
    var matchStatus = !status || rowStatus === status;
    row.style.display = (matchQ && matchKat && matchStatus) ? '' : 'none';
  });
}

(function() {
  var params = new URLSearchParams(window.location.search);
  var roleFromUrl = params.get('role');
  if (roleFromUrl) pilihRole(roleFromUrl);
})();

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