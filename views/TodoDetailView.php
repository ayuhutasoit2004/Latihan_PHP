a<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Todo</title>
  <link rel="stylesheet" href="assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
  <h3 class="mb-3">Detail Todo</h3>

  <?php if (!empty($todo)): ?>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($todo['activity']) ?></h5>
        <p class="card-text"><strong>Status:</strong>
          <?= $todo['status'] == 1 ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-danger">Belum</span>' ?>
        </p>
        <p><strong>Dibuat:</strong> <?= date('d M Y H:i', strtotime($todo['created_at'])) ?></p>
        <p><strong>Diperbarui:</strong> <?= date('d M Y H:i', strtotime($todo['updated_at'])) ?></p>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">Todo tidak ditemukan.</div>
  <?php endif; ?>

  <a href="index.php" class="btn btn-secondary mt-3">← Kembali</a>
</div>
</body>
</html>
