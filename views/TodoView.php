<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Todo List</title>
  <link rel="stylesheet" href="assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-3">Todo List</h2>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
  <?php endif; ?>

  <!-- Filter dan Pencarian -->
  <form class="row g-2 mb-3" method="GET" action="index.php">
    <div class="col-auto">
      <select name="filter" class="form-select">
        <option value="all" <?= ($_GET['filter'] ?? '') == 'all' ? 'selected' : '' ?>>Semua</option>
        <option value="finished" <?= ($_GET['filter'] ?? '') == 'finished' ? 'selected' : '' ?>>Selesai</option>
        <option value="unfinished" <?= ($_GET['filter'] ?? '') == 'unfinished' ? 'selected' : '' ?>>Belum</option>
      </select>
    </div>
    <div class="col">
      <input type="text" name="search" placeholder="Cari aktivitas..." class="form-control"
             value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-secondary" type="submit">Terapkan</button>
      <a href="index.php" class="btn btn-outline-secondary">Reset</a>
    </div>
    <div class="col-auto">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah</button>
    </div>
  </form>

  <!-- Tabel Todo -->
  <table class="table table-bordered bg-white align-middle">
    <thead class="table-light">
      <tr><th>ID</th><th>Aktivitas</th><th>Deskripsi</th><th>Status</th><th>Dibuat</th><th>Diperbarui</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php if (!empty($todos)): foreach ($todos as $t): ?>
        <!-- ✅ Tambahkan atribut untuk drag & drop -->
        <tr data-id="<?= $t['id'] ?>" draggable="true" class="todo-item">
          <td><?= $t['id'] ?></td>
          <td><?= htmlspecialchars($t['activity']) ?></td>
          <td><?= htmlspecialchars($t['description'] ?? '-') ?></td>
          <td><?= $t['status'] == 1 ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-danger">Belum</span>' ?></td>
          <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
          <td><?= date('d M Y H:i', strtotime($t['updated_at'])) ?></td>
          <td>
            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $t['id'] ?>">Edit</button>
            <a href="index.php?page=delete&id=<?= $t['id'] ?>" 
               class="btn btn-sm btn-danger" 
               onclick="return confirm('Hapus todo ini?')">Hapus</a>

            <a href="index.php?page=detail&id=<?= $t['id'] ?>" class="btn btn-sm btn-info">Detail</a>
            <a href="index.php?page=toggle&id=<?= $t['id'] ?>" class="btn btn-sm <?= $t['status'] == 1 ? 'btn-outline-secondary' : 'btn-success' ?>">
              <?= $t['status'] == 1 ? 'Tandai Belum' : 'Tandai Selesai' ?>
            </a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="7" class="text-center text-muted">Belum ada data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="index.php?page=create">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Aktivitas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Aktivitas</label>
          <input type="text" name="activity" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Tambahkan deskripsi..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit untuk setiap todo -->
<?php if (!empty($todos)): foreach ($todos as $t): ?>
<div class="modal fade" id="editModal<?= $t['id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="index.php?page=update">
      <div class="modal-header">
        <h5 class="modal-title">Edit Aktivitas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="<?= $t['id'] ?>">
        <div class="mb-3">
          <label class="form-label">Nama Aktivitas</label>
          <input type="text" name="activity" class="form-control" 
                 value="<?= htmlspecialchars($t['activity']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($t['description'] ?? '') ?></textarea>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="status" id="status<?= $t['id'] ?>" value="1"
                 <?= ($t['status'] == 1) ? 'checked' : '' ?>>
          <label class="form-check-label" for="status<?= $t['id'] ?>">Tandai Selesai</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
<?php endforeach; endif; ?>

<!-- ✅ Script drag & drop -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const rows = document.querySelectorAll('.todo-item');
  const tableBody = document.querySelector('tbody');
  let draggedItem = null;

  rows.forEach(row => {
    row.addEventListener('dragstart', () => draggedItem = row);
    row.addEventListener('dragover', e => e.preventDefault());
    row.addEventListener('drop', e => {
      e.preventDefault();
      if (draggedItem && draggedItem !== row) {
        const next = row.nextSibling === draggedItem ? row : row.nextSibling;
        tableBody.insertBefore(draggedItem, next);
        saveNewOrder();
      }
    });
  });

  function saveNewOrder() {
    const order = [...document.querySelectorAll('.todo-item')].map(row => row.dataset.id);
    fetch('index.php?page=reorder', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ order: order })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) console.log('✅ Urutan berhasil disimpan!');
    })
    .catch(err => console.error('❌ Gagal simpan urutan:', err));
  }
});
</script>

<script src="assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
