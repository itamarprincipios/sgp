<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .report-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        padding: 25px;
        margin-bottom: 25px;
    }
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 15px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .info-box {
        background: #f8fafc;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #3b82f6;
    }
    .info-label {
        font-size: 0.85rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
        font-weight: 600;
    }
    .info-value {
        font-size: 1.1rem;
        color: #1e293b;
        font-weight: 500;
    }
    .data-table-clean {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table-clean th {
        text-align: left;
        padding: 12px;
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table-clean td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
    }
    .filter-bar {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .badge-blue { background: #dbeafe; color: #1e40af; }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-purple { background: #f3e8ff; color: #6b21a8; }
    
    @media print {
        @page { size: landscape; margin: 1cm; }
        body * { visibility: hidden; }
        .report-card, .report-card * { visibility: visible; }
        .report-card { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; 
            margin: 0; 
            box-shadow: none; 
            border: 1px solid #ddd;
        }
        .filter-bar, nav, header, .btn-icon, .semed-header { display: none !important; }
        .badge { border: 1px solid #ccc; background: #fff !important; color: #000 !important; }
        a { text-decoration: none; color: #000; }
        .info-box { border-left: 1px solid #000 !important; background: #fff !important; }
    }
</style>

<div class="filter-bar">
    <div style="flex-grow: 1;">
        <h2 style="margin: 0; color: #1e293b; font-size: 1.5rem;"><i class="fas fa-chart-pie" style="color: #3b82f6;"></i> Relatórios de Gestão</h2>
        <p style="margin: 5px 0 0; color: #64748b;">Visualize vínculos e estruturas das unidades escolares.</p>
    </div>
    
    <div style="display: flex; gap: 10px; align-items: flex-end;">
         <button onclick="window.print()" style="height: 42px; padding: 0 20px; border: none; background: #475569; color: white; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500;">
            <i class="fas fa-print"></i> Imprimir
        </button>
        
        <form action="<?= url('admin/reports') ?>" method="GET" style="display: flex; gap: 10px;">
            <div>
                <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: #64748b;">Tipo de Relatório</label>
                <select name="type" class="modern-select" onchange="this.form.submit()" style="padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                    <option value="general" <?= $type === 'general' ? 'selected' : '' ?>>Visão Geral</option>
                    <option value="school" <?= $type === 'school' ? 'selected' : '' ?>>Por Escola</option>
                    <option value="semed_user" <?= $type === 'semed_user' ? 'selected' : '' ?>>Por Gestor SEMED</option>
                </select>
            </div>
            
            <?php if ($type === 'school'): ?>
            <div>
                <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: #64748b;">Selecione a Escola</label>
                <select name="id" class="modern-select" onchange="this.form.submit()" style="padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; min-width: 200px;">
                    <option value="" disabled <?= !$selectedId ? 'selected' : '' ?>>Escolha...</option>
                    <?php foreach ($allSchools as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $selectedId == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if ($type === 'semed_user'): ?>
            <div>
                <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: #64748b;">Selecione o Gestor</label>
                <select name="id" class="modern-select" onchange="this.form.submit()" style="padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; min-width: 200px;">
                    <option value="" disabled <?= !$selectedId ? 'selected' : '' ?>>Escolha...</option>
                    <?php foreach ($allSemedUsers as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $selectedId == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($type === 'general'): ?>
    <div class="report-card">
        <div class="report-header">
            <h3 style="margin: 0;">🏫 Visão Geral: Escolas x Gestores</h3>
        </div>
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>Escola</th>
                    <th>Gestores SEMED Vinculados</th>
                    <th>Qtd. Coordenadores</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reportData['schools'] as $s): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($s['name']) ?></strong><br>
                        <span style="font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($s['director_name'] ?? 'Sem diretor') ?></span>
                    </td>
                    <td>
                        <?php if(!empty($s['managers'])): ?>
                            <?php foreach($s['managers'] as $m): ?>
                                <span class="badge badge-blue"><?= htmlspecialchars($m['name']) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-style: italic;">Nenhum vinculado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Ideally fetch count efficiently, for now just placeholder or we add method -->
                        <a href="<?= url('admin/reports?type=school&id='.$s['id']) ?>" style="color: #3b82f6; text-decoration: none; font-size: 0.9rem;">Ver Detalhes <i class="fas fa-arrow-right"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="report-card">
        <div class="report-header">
            <h3 style="margin: 0;">👥 Visão Geral: Gestores SEMED</h3>
        </div>
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email / WhatsApp</th>
                    <th>Escolas Geridas</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reportData['semed_users'] as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td>
                        <?= htmlspecialchars($u['email']) ?><br>
                        <small style="color: #22c55e;"><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($u['whatsapp'] ?? '-') ?></small>
                    </td>
                    <td>
                        <span class="badge badge-purple"><?= $u['school_count'] ?> Escolas</span>
                    </td>
                    <td>
                         <a href="<?= url('admin/reports?type=semed_user&id='.$u['id']) ?>" style="color: #3b82f6; text-decoration: none;">Ver Portfólio</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($type === 'school' && isset($reportData['school'])): ?>
    <div class="report-card">
        <div class="report-header">
            <h3 style="margin: 0;"><?= htmlspecialchars($reportData['school']['name']) ?></h3>
            <span class="badge badge-green">INEP: <?= htmlspecialchars($reportData['school']['inep_code'] ?? 'N/A') ?></span>
        </div>
        
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Direção</div>
                <div class="info-value"><?= htmlspecialchars($reportData['school']['director_name'] ?? 'Não informado') ?></div>
                <div style="font-size: 0.9rem; margin-top: 5px;"><i class="fas fa-phone"></i> <?= htmlspecialchars($reportData['school']['director_phone'] ?? '-') ?></div>
            </div>
            <div class="info-box" style="border-left-color: #8b5cf6;">
                <div class="info-label">Supervisão SEMED</div>
                <?php if(!empty($reportData['semed_users'])): ?>
                    <?php foreach($reportData['semed_users'] as $su): ?>
                        <div class="info-value" style="margin-bottom: 5px;">
                            <?= htmlspecialchars($su['name']) ?>
                            <a href="<?= url('admin/reports?type=semed_user&id='.$su['id']) ?>" style="font-size: 0.8rem; color: #3b82f6;"><i class="fas fa-link"></i></a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="info-value" style="color: #94a3b8;">Ninguém vinculado</div>
                <?php endif; ?>
            </div>
            <div class="info-box" style="border-left-color: #f59e0b;">
                <div class="info-label">Localidade</div>
                <div class="info-value"><?= htmlspecialchars($reportData['school']['address'] ?? 'Não informado') ?></div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4 style="margin-bottom: 15px; color: #475569;">Coordenadores (<?= count($reportData['coordinators']) ?>)</h4>
                <?php if(!empty($reportData['coordinators'])): ?>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach($reportData['coordinators'] as $c): ?>
                            <li style="padding: 10px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between;">
                                <span><?= htmlspecialchars($c['name']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: #94a3b8; font-style: italic;">Nenhum coordenador encontrado.</p>
                <?php endif; ?>
            </div>
            
            <div>
                <h4 style="margin-bottom: 15px; color: #475569;">Professores (<?= count($reportData['professors']) ?>)</h4>
                 <?php if(!empty($reportData['professors'])): ?>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <table class="data-table-clean">
                            <?php foreach($reportData['professors'] as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <!-- Add Class info here if queried -->
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: #94a3b8; font-style: italic;">Nenhum professor encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php elseif ($type === 'semed_user' && isset($reportData['user'])): ?>
    <div class="report-card">
        <div class="report-header">
            <h3 style="margin: 0;"><?= htmlspecialchars($reportData['user']['name']) ?></h3>
            <span class="badge badge-purple">Gestor SEMED</span>
        </div>
        
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Contato</div>
                <div class="info-value"><?= htmlspecialchars($reportData['user']['email']) ?></div>
                <div style="margin-top: 5px; color: #22c55e;"><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($reportData['user']['whatsapp'] ?? 'Não informado') ?></div>
            </div>
            <div class="info-box" style="border-left-color: #ec4899;">
                <div class="info-label">Total de Escolas</div>
                <div class="info-value" style="font-size: 2rem; font-weight: 700;"><?= count($reportData['schools']) ?></div>
            </div>
        </div>
        
        <h4 style="margin-bottom: 15px; color: #475569;">Escolas sob Gestão</h4>
        <?php if(!empty($reportData['schools'])): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                <?php foreach($reportData['schools'] as $s): ?>
                    <div style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($s['name']) ?></div>
                            <div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($s['director_name'] ?? 'Sem diretor') ?></div>
                        </div>
                        <a href="<?= url('admin/reports?type=school&id='.$s['id']) ?>" class="btn-icon" title="Ver Escola"><i class="fas fa-eye"></i></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="padding: 20px; background: #fff1f2; color: #be123c; border-radius: 8px;">Este usuário não possui escolas vinculadas.</p>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
