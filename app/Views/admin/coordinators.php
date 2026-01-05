<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>👔 Gestão de Coordenadores</h1>
    <p>Visualize e gerencie os coordenadores pedagógicos.</p>
</div>

<!-- Tip Block -->
<div style="background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e40af; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
    <p style="margin: 0;"><i class="fas fa-info-circle"></i> Para cadastrar novos coordenadores e vinculá-los às escolas, utilize preferencialmente o <strong>Painel SEMED</strong> para uma experiência mais completa com gestão de vínculos múltiplos.</p>
</div>

<div class="list-section">
    <h3 style="margin-bottom: 20px; color: #1e293b;">Coordenadores Cadastrados</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Escola Principal (ID/Nome)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($coordinators)): ?>
                <tr><td colspan="3">Nenhum coordenador encontrado.</td></tr>
            <?php else: ?>
                <?php foreach($coordinators as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td>
                            <?php 
                                // Display primary school ID or name if available in aggregation (depends on User Model aggregation)
                                // If getByRole('coordinator') returns school_name already (it does from previous edits), use it.
                                echo htmlspecialchars($c['school_name'] ?? $c['school_id']);
                            ?>
                        </td>
                        <td>
                            <a href="<?= url('admin/user/reset-password?id='.$c['id']) ?>" class="btn-icon" title="Resetar Senha para '123456'" onclick="return confirm('Tem certeza que deseja resetar a senha deste usuário para 123456?')"><i class="fas fa-key"></i></a>
                            <a href="<?= url('admin/user/delete?id='.$c['id']) ?>" class="btn-icon" style="color: red;" title="Excluir Usuário" onclick="return confirm('Tem certeza que deseja excluir este usuário permanentemente?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
