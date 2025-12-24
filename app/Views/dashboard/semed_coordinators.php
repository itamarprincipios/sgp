<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <h2>Gestão de Coordenadores</h2>
</div>

<div class="content-row">
    <div class="upload-section">
        <h3>Novo Coordenador</h3>
        <form action="<?= url('semed/coordinator/store') ?>" method="POST">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="name" required placeholder="Nome do Coordenador">
            </div>
            <div class="form-group">
                <label>E-mail (Login)</label>
                <input type="email" name="email" required placeholder="exemplo@sgp.com">
            </div>
            <div class="form-group">
                <label>Vincular à Escola</label>
                <select name="school_id" required>
                    <option value="">Selecione uma escola...</option>
                    <?php foreach($schools as $school): ?>
                        <option value="<?= $school['id'] ?>"><?= htmlspecialchars($school['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
            </div>
            <p style="font-size: 0.8rem; color: #666;">* Senha padrão para novos acessos: <strong>123456</strong></p>
            <button type="submit" class="btn btn-primary">Salvar Coordenador</button>
        </form>
    </div>
    
    <div class="list-section">
        <h3>Coordenadores e Gestores</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Escola</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($coordinators as $coord): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($coord['name']) ?><br>
                            <small style="color: #666;"><?= htmlspecialchars($coord['email']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($coord['school_name'] ?? 'Não Vinculado') ?></td>
                        <td>
                            <a href="<?= url('semed/coordinator/edit?id=' . $coord['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <?php if (!empty($coord['whatsapp'])): 
                                $phone = preg_replace('/\D/', '', $coord['whatsapp']);
                                if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                    $phone = '55' . $phone;
                                }
                            ?>
                                <a href="https://wa.me/<?= $phone ?>?text=Olá, <?= urlencode($coord['name']) ?>!" target="_blank" class="btn-icon" style="color: #25D366;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            <?php endif; ?>
                            <a href="<?= url('semed/password/reset?id=' . $coord['id'] . '&role=coordinator') ?>" class="btn-icon" style="color: #f39c12;" title="Resetar Senha" onclick="return confirm('Deseja resetar a senha deste coordenador para 123456?')"><i class="fas fa-key"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
