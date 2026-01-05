<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .coordinator-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }

    .coordinator-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .coordinator-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    
    .coord-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .coord-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
    }

    .coord-info h4 {
        margin: 0;
        font-size: 1.1rem;
        color: #1f2937;
    }

    .coord-info span {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .schools-list {
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    
    .schools-label {
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 8px;
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .school-badge {
        display: inline-flex;
        align-items: center;
        background: #f3f4f6;
        color: #374151;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 5px;
        margin-bottom: 5px;
        border: 1px solid #e5e7eb;
    }

    .school-badge a.remove-link {
        margin-left: 6px;
        color: #ef4444;
        text-decoration: none;
        font-weight: bold;
        opacity: 0.7;
    }
    
    .school-badge a.remove-link:hover {
        opacity: 1;
    }

    .card-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        cursor: pointer;
        background: #f9fafb;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .btn-whatsapp { color: #25D366; background: rgba(37, 211, 102, 0.1); }
    .btn-edit { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
    .btn-key { color: #f59e0b; background: rgba(245, 158, 11, 0.1); }
    .btn-add { color: #8b5cf6; background: rgba(139, 92, 246, 0.1); }

    /* Form Styles Cleanup */
    .upload-section {
        background: white;
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
</style>

<div class="dashboard-header">
    <h2>Coordenadores</h2>
</div>

<div class="content-row" style="display: block;"> <!-- Changed to block to stack form and grid -->
    
    <div class="upload-section">
        <h3 style="margin-bottom: 20px; color: #4b5563; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-user-plus"></i> Novo Coordenador
        </h3>
        <form action="<?= url('semed/coordinator/store') ?>" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label>Nome Completo</label>
                <input type="text" name="name" required placeholder="Nome do Coordenador">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>E-mail (Login)</label>
                <input type="email" name="email" required placeholder="exemplo@sgp.com">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Vincular à Escola</label>
                <select name="school_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach($schools as $school): ?>
                        <option value="<?= $school['id'] ?>"><?= htmlspecialchars($school['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
            </div>
            <button type="submit" class="btn btn-primary" style="height: 42px;">Salvar</button>
        </form>
        <p style="font-size: 0.8rem; color: #9ca3af; margin-top: 10px;">* Senha padrão: <strong>123456</strong></p>
    </div>
    
    <div class="coordinator-grid">
        <?php foreach($coordinators as $coord): ?>
            <div class="coordinator-card">
                <div>
                    <div class="coord-header">
                        <div class="coord-avatar">
                            <?= strtoupper(substr($coord['name'], 0, 1)) ?>
                        </div>
                        <div class="coord-info">
                            <h4><?= htmlspecialchars($coord['name']) ?></h4>
                            <span><?= htmlspecialchars($coord['email']) ?></span>
                        </div>
                    </div>

                    <div class="schools-list">
                        <span class="schools-label">Escolas Vinculadas</span>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px; align-items: center;">
                            <?php 
                                if (!empty($coord['school_name'])) {
                                    $names = explode(', ', $coord['school_name']);
                                    $ids = explode(',', $coord['school_ids_raw'] ?? '');
                                    
                                    foreach($names as $i => $n) {
                                        $sid = $ids[$i] ?? 0;
                                        if (empty($n)) continue;
                                        echo '<span class="school-badge">' 
                                            . htmlspecialchars($n) . 
                                            ' <a href="'.url('semed/coordinator/unlink-school?user_id='.$coord['id'].'&school_id='.$sid).'" onclick="return confirm(\'Remover vínculo com esta escola?\')" class="remove-link" title="Remover">&times;</a></span>';
                                    }
                                } else {
                                    echo '<span style="color: #9ca3af; font-size: 0.9rem;">Nenhuma escola vinculada.</span>';
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="<?= url('semed/coordinator/edit?id=' . $coord['id']) ?>" class="action-btn btn-edit" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <?php if (!empty($coord['whatsapp'])): 
                        $phone = preg_replace('/\D/', '', $coord['whatsapp']);
                        if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') $phone = '55' . $phone;
                    ?>
                        <a href="https://wa.me/<?= $phone ?>?text=Olá, <?= urlencode($coord['name']) ?>!" target="_blank" class="action-btn btn-whatsapp" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    <?php endif; ?>

                    <a href="<?= url('semed/password/reset?id=' . $coord['id'] . '&role=coordinator') ?>" class="action-btn btn-key" title="Resetar Senha (123456)" onclick="return confirm('Resetar senha para 123456?')">
                        <i class="fas fa-key"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
