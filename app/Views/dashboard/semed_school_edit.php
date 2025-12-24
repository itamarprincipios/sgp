<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <h2>Editar Escola</h2>
    <a href="<?= url('semed/schools') ?>" class="btn btn-secondary" style="width: auto;">Voltar</a>
</div>

<div class="upload-section" style="max-width: 600px; margin: 0 auto;">
    <form action="<?= url('semed/school/update') ?>" method="POST">
        <input type="hidden" name="id" value="<?= $school['id'] ?>">
        <div class="form-group">
            <label>Nome da Escola</label>
            <input type="text" name="name" value="<?= htmlspecialchars($school['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Código INEP</label>
            <input type="text" name="inep_code" value="<?= htmlspecialchars($school['inep_code'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Nome do Diretor</label>
            <input type="text" name="director_name" value="<?= htmlspecialchars($school['director_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Telefone do Diretor</label>
            <input type="text" name="director_phone" value="<?= htmlspecialchars($school['director_phone'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
