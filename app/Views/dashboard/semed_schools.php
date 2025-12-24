<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <h2>Gestão de Escolas</h2>
</div>

<div class="content-row">
    <div class="upload-section">
        <h3>Nova Escola</h3>
        <form action="<?= url('semed/school/store') ?>" method="POST">
            <div class="form-group">
                <label>Nome da Escola</label>
                <input type="text" name="name" required placeholder="Ex: Escola Municipal João Paulo II">
            </div>
            <div class="form-group">
                <label>Código INEP (Opcional)</label>
                <input type="text" name="inep_code" placeholder="Ex: 12345678">
            </div>
            <div class="form-group">
                <label>Nome do Diretor</label>
                <input type="text" name="director_name" placeholder="Ex: Maria Silva">
            </div>
            <div class="form-group">
                <label>Telefone do Diretor</label>
                <input type="text" name="director_phone" placeholder="Ex: (11) 98765-4321">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
    
    <div class="list-section">
        <h3>Escolas Cadastradas</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Diretor</th>
                    <th>Telefone</th>
                    <th>INEP</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($schools as $school): ?>
                    <tr>
                        <td><?= htmlspecialchars($school['name']) ?></td>
                        <td><?= htmlspecialchars($school['director_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($school['director_phone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($school['inep_code'] ?? '-') ?></td>
                        <td>
                            <a href="<?= url('semed/school/edit?id=' . $school['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="#" onclick="if(confirm('Tem certeza que deseja excluir a escola <?= htmlspecialchars($school['name']) ?>? Esta ação não pode ser desfeita.')) { window.location.href='<?= url('semed/school/delete?id=' . $school['id']) ?>'; } return false;" class="btn-icon" style="color: #dc3545;" title="Excluir"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
