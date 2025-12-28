<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Tabs Styles */
    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
        margin-bottom: 20px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .tab-btn {
        padding: 10px 20px;
        cursor: pointer;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 500;
        color: #666;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s;
    }
    .tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .dropdown-content a:hover {
        background-color: #f8f9fa !important;
        color: var(--primary) !important;
    }
    
    /* Responsive Styles for Mobile/Tablet */
    @media (max-width: 768px) {
        .school-hero h1 {
            font-size: 1.5rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .school-hero {
            padding: 25px 15px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .tabs {
            gap: 8px;
            padding-bottom: 5px;
        }
        
        .tab-btn {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        
        /* Formulários em coluna */
        form[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
        
        /* Filtros em coluna */
        .filter-container {
            padding: 1rem;
        }
        
        /* Dropdowns */
        .dropdown-content {
            right: auto !important;
            left: 0 !important;
            min-width: 100% !important;
        }
    }
</style>


</style>

<div class="school-hero">
    <h1>
        <i class="fas fa-school"></i> 
        <?= isset($school['name']) ? htmlspecialchars($school['name']) : 'Painel da Escola' ?>
    </h1>
    <p>Painel de Gestão do Coordenador Pedagógico</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-blue">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($professors) ?></span>
            <span class="stat-label">Professores</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon-wrapper bg-purple">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($classes) ?></span>
            <span class="stat-label">Turmas</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper bg-orange">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($plannings) ?></span>
            <span class="stat-label">Planejamentos</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper bg-red">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-content">
            <span class="stat-value"><?= count($pendingSubmissions) ?></span>
            <span class="stat-label">Pendências</span>
        </div>
    </div>
</div>

<div class="tabs">
    <button class="tab-btn active" onclick="openTab(event, 'tab-planning')">Meus Planejamentos</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-bimesters')">Organização (Bimestres)</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-pending')">Pendências de Entrega</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-uploads'); markUploadsViewed(this)" style="position: relative;">
        Envios Recentes
        <?php if (!empty($newUploadsCount) && $newUploadsCount > 0): ?>
            <span id="badge-uploads" style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; position: absolute; top: 0; right: 0; transform: translate(50%, -10%); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                <?= $newUploadsCount ?>
            </span>
        <?php endif; ?>
    </button>

    <button class="tab-btn" onclick="openTab(event, 'tab-classes')">Turmas</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-professors')">Professores</button>
</div>

<!-- TAB 1: PLANEJAMENTOS (LISTA) -->
<div id="tab-planning" class="tab-content active">
    <!-- Removed sub-tabs -->
    
    <!-- Content from old subtab-list -->
    <div style="margin-bottom: 15px;">
        <a href="<?= url('school/planning/create') ?>" class="btn btn-primary" style="width: auto;"><i class="fas fa-plus"></i> Novo Planejamento</a>
    </div>
    <div class="list-section">
        <h3>Meus Planejamentos Cadastrados</h3>
        <table class="data-table">
            <thead>
                <tr>
                   <th>Nome</th>
                   <th>Descrição/Período</th>
                   <th>Prazo Limite</th>
                   <th>Área de envios</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($plannings)): ?>
                    <tr><td colspan="4">Nenhum planejamento criado.</td></tr>
                <?php else: ?>
                    <?php foreach($plannings as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['description']) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['deadline'])) ?></td>
                            <td style="display: flex; gap: 20px; align-items: center;">
                                <a href="<?= url('school/planning/view?id=' . $p['id']) ?>" class="btn btn-primary" style="width: auto; padding: 5px 15px; font-size: 0.85rem;">
                                    <i class="fas fa-list"></i> Controle de Envios
                                </a>
                                <div style="display: flex; gap: 10px; border-left: 1px solid #ddd; padding-left: 15px;">
                                    <a href="<?= url('school/planning/edit?id=' . $p['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                    <a href="<?= url('school/planning/delete?id=' . $p['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este planejamento? Todos os envios relacionados também serão afetados.')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- TAB 2: BIMESTRES -->
<div id="tab-bimesters" class="tab-content">
    <div class="list-section">
        <h3 style="color: var(--primary);"><i class="fas fa-layer-group"></i> Organização por Bimestres - 2026</h3>
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Utilize os quadros abaixo para agrupar seus planejamentos para fins de organização pedagógica.</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <?php for($b = 1; $b <= 4; $b++): ?>
                <div class="card" style="border: 1px solid #ddd; background: #fff; padding: 0; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <div style="background: #f8f9fa; padding: 15px; border-bottom: 2px solid var(--primary); border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                        <h4 style="margin: 0; color: #333;"><?= $b ?>º Bimestre</h4>
                        <div class="dropdown" style="position: relative; display: inline-block;">
                            <button class="btn" style="width: auto; padding: 6px 12px; font-size: 0.8rem; background: var(--primary); border: none; color: #fff; cursor: pointer; border-radius: 20px; font-weight: bold; display: flex; align-items: center; gap: 5px; transition: all 0.2s;" title="Adicionar Planejamento existente">
                                <i class="fas fa-plus-circle"></i> Incluir
                            </button>
                            <div class="dropdown-content" style="display: none; position: absolute; background-color: #fff; min-width: 280px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 1000; border-radius: 12px; border: 1px solid #e1e4e8; margin-top: 10px; right: 0; overflow: hidden; animation: slideDown 0.2s ease-out;">
                                <div style="padding: 12px 15px; border-bottom: 1px solid #f1f3f5; background: #f8f9fa; font-weight: 700; font-size: 0.75rem; color: #495057; text-transform: uppercase; letter-spacing: 0.5px;">Vincular a este bimestre:</div>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <?php 
                                        $unorganized = array_filter($plannings, function($p) { return empty($p['bimester']); });
                                        if(empty($unorganized)):
                                    ?>
                                        <div style="padding: 20px; font-size: 0.85rem; color: #adb5bd; text-align: center; font-style: italic;">Todos já organizados</div>
                                    <?php else: ?>
                                        <?php foreach($unorganized as $up): ?>
                                            <a href="<?= url('school/planning/associate-bimester?id=' . $up['id'] . '&bimester=' . $b) ?>" style="color: #212529; padding: 12px 15px; text-decoration: none; display: block; font-size: 0.9rem; border-bottom: 1px solid #f8f9fa; transition: background 0.2s; display: flex; align-items: center; gap: 10px;">
                                                <i class="fas fa-file-alt" style="color: #6c757d;"></i>
                                                <span><?= htmlspecialchars($up['name']) ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 15px; min-height: 80px; background: #fafafa;">
                        <?php 
                            $bimesterPlannings = array_filter($plannings, function($p) use ($b) { return $p['bimester'] == $b; });
                        ?>
                        <?php if(empty($bimesterPlannings)): ?>
                            <div style="text-align: center; color: #bbb; padding-top: 20px; font-size: 0.8rem; font-style: italic;">
                                Nenhum item vinculado
                            </div>
                        <?php else: ?>
                            <?php foreach($bimesterPlannings as $p): ?>
                                <div style="background: #fff; border: 1px solid #eee; margin-bottom: 8px; padding: 10px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid var(--primary);">
                                    <div style="font-weight: bold; color: #333; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 180px;" title="<?= htmlspecialchars($p['name']) ?>">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </div>
                                    <a href="<?= url('school/planning/associate-bimester?id=' . $p['id'] . '&bimester=0') ?>" class="btn-icon" style="color: #999; font-size: 0.8rem;" title="Remover deste bimestre">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- TAB 3: PENDÊNCIAS -->
<div id="tab-pending" class="tab-content">
    <div class="list-section">
        <h3 style="color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i> Pendências de Entrega (Tempo Real)</h3>
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Acompanhe abaixo os professores que ainda não enviaram os planejamentos vigentes ou atrasados.</p>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Professor</th>
                    <th>Turma</th>
                    <th>Planejamento Pendente</th>
                    <th>Prazo</th>
                    <th>Status</th>
                    <th>Cobrar</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($pendingSubmissions)): ?>
                    <tr><td colspan="6" style="text-align: center; color: #2ecc71; font-weight: bold; padding: 20px;"><i class="fas fa-check-circle"></i> Parabéns! Nenhuma pendência encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach($pendingSubmissions as $p): 
                        $isLate = strtotime($p['deadline']) < time();
                    ?>
                        <tr style="<?= $isLate ? 'background-color: #fff5f5;' : '' ?>">
                            <td><?= htmlspecialchars($p['professor_name']) ?></td>
                            <td><?= htmlspecialchars($p['class_name']) ?></td>
                            <td><?= htmlspecialchars($p['planning_name']) ?></td>
                            <td style="font-weight: bold; <?= $isLate ? 'color: #c0392b;' : '' ?>">
                                <?= date('d/m/Y', strtotime($p['deadline'])) ?>
                            </td>
                            <td>
                                <?php if($isLate): ?>
                                    <span class="status-badge status-rejeitado">Atrasado</span>
                                <?php else: ?>
                                    <span class="status-badge status-ajustado">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['whatsapp'])): 
                                    $phone = preg_replace('/\D/', '', $p['whatsapp']);
                                    if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                        $phone = '55' . $phone;
                                    }
                                    $msg = $isLate 
                                        ? "Olá, " . urlencode($p['professor_name']) . "! Consta em nosso sistema que a entrega do planejamento *" . urlencode($p['planning_name']) . "* está atrasada. O prazo era " . date('d/m/Y', strtotime($p['deadline'])) . ". Poderia verificar?"
                                        : "Olá, " . urlencode($p['professor_name']) . "! Lembrete amigável: o prazo para entrega do planejamento *" . urlencode($p['planning_name']) . "* encerra em " . date('d/m/Y', strtotime($p['deadline'])) . ".";
                                ?>
                                    <a href="https://wa.me/<?= $phone ?>?text=<?= $msg ?>" target="_blank" class="whatsapp-btn" style="background: #25D366;">
                                        <i class="fab fa-whatsapp"></i> Cobrar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- TAB 2: ENVIOS RECENTES -->
<div id="tab-uploads" class="tab-content">
    <div class="list-section">
        <h3>Últimos Documentos Recebidos</h3>
        
        <!-- Filter Form -->
        <!-- Filter Form -->
        <form method="GET" action="<?= url('school/dashboard') ?>" class="filter-container">
            <!-- Hack to keep tab active after reload -->
            <input type="hidden" name="tab" value="uploads"> 
            
            <div class="filter-group">
                <label class="filter-label">Planejamento/Bimestre</label>
                <select name="period_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach($plannings as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($filters['period_id'] == $p['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Professor</label>
                <select name="professor_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach($professors as $prof): ?>
                        <option value="<?= $prof['id'] ?>" <?= ($filters['professor_id'] == $prof['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prof['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group" style="flex: 0 0 150px; min-width: 150px;">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="enviado" <?= ($filters['status'] == 'enviado') ? 'selected' : '' ?>>Enviado</option>
                    <option value="aprovado" <?= ($filters['status'] == 'aprovado') ? 'selected' : '' ?>>Aprovado</option>
                    <option value="ajustado" <?= ($filters['status'] == 'ajustado') ? 'selected' : '' ?>>Ajustado</option>
                    <option value="rejeitado" <?= ($filters['status'] == 'rejeitado') ? 'selected' : '' ?>>Rejeitado</option>
                    <option value="atrasado" <?= ($filters['status'] == 'atrasado') ? 'selected' : '' ?>>Atrasado</option>
                </select>
            </div>
            
            <div class="filter-actions">
                 <a href="<?= url('school/dashboard') ?>" class="btn-filter-clear">
                    <i class="fas fa-times"></i> Limpar
                 </a>
            </div>
        </form>

        <table class="data-table">
           <thead>
               <tr>
                   <th>Professor</th>
                   <th>Turma</th> <!-- Placeholder for linking data later -->
                   <th>Documento</th>
                   <th>Status</th>
                   <th>Ação</th>
               </tr>
           </thead>
           <tbody>
               <?php if(empty($documents)): ?>
                   <tr><td colspan="5">Nenhum documento.</td></tr>
               <?php else: ?>
                   <?php foreach($documents as $doc): ?>
                       <tr>
                           <td><?= htmlspecialchars($doc['professor_name']) ?></td>
                           <td>-</td> <!-- Need to join class name in query if needed here -->
                           <td><?= htmlspecialchars($doc['title']) ?></td>
                           <td><span class="status-badge status-<?= $doc['status'] ?>"><?= ucfirst($doc['status']) ?></span></td>
                           <td><a href="<?= url('uploads/' . $doc['file_path']) ?>" target="_blank" class="btn-icon"><i class="fas fa-eye"></i></a></td>
                       </tr>
                   <?php endforeach; ?>
               <?php endif; ?>
           </tbody>
        </table>
    </div>
</div>

<!-- TAB 3: TURMAS -->
<div id="tab-classes" class="tab-content">
    <div class="content-row">
        <div class="upload-section">
            <h3>Cadastrar Nova Turma</h3>
            <form action="<?= url('school/class/store') ?>" method="POST">
                <div class="form-group">
                    <label>Nome da Turma</label>
                    <input type="text" name="name" required placeholder="Ex: 5º Ano A">
                </div>
                <button type="submit" class="btn btn-primary">Salvar Turma</button>
            </form>
        </div>
        <div class="list-section">
            <h3>Turmas Cadastradas</h3>
            <ul style="list-style: none;">
                <?php foreach($classes as $c): ?>
                    <li style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                        <div>
                            <span style="font-weight: bold;"><?= htmlspecialchars($c['name']) ?></span>
                            <div style="font-size: 0.85rem; color: #666; margin-top: 4px;">
                                <?php if($c['professor_name']): ?>
                                    <i class="fas fa-chalkboard-teacher"></i> Titular: <?= htmlspecialchars($c['professor_name']) ?>
                                <?php else: ?>
                                    <span style="color: #e74c3c;"><i class="fas fa-exclamation-circle"></i> Sem professor titular</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="<?= url('school/class/edit?id='.$c['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('school/class/delete?id='.$c['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('ATENÇÃO: Tem certeza que deseja excluir esta turma? Os professores vinculados ficarão sem turma.')"><i class="fas fa-trash"></i></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- TAB 4: PROFESSORES -->
<div id="tab-professors" class="tab-content">
    <div class="content-row">
        <div class="upload-section">
            <h3>Cadastrar Professor</h3>
            <form action="<?= url('school/professor/store') ?>" method="POST">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>E-mail (Login)</label>
                    <input type="email" name="email" required>
                </div>
                <!-- Senha padrão oculta: 123456 -->
                <div class="form-group">
                    <label>WhatsApp</label>
                    <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
                </div>
                <div class="form-group">
                    <label>Vincular a Turma</label>
                    <select name="class_id">
                        <option value="">Selecione uma turma...</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                    <input type="checkbox" name="is_physical_education" id="prof_is_pe" value="1" style="width: 18px; height: 18px;">
                    <label for="prof_is_pe" style="margin: 0; cursor: pointer;">Professor de Educação Física?</label>
                </div>
                <button type="submit" class="btn btn-primary">Cadastrar Professor</button>
            </form>
        </div>
        <div class="list-section">
            <h3>Professores da Escola</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Turma</th>
                        <th>WhatsApp</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($professors as $prof): ?>
                        <tr>
                            <td><?= htmlspecialchars($prof['name']) ?></td>
                            <td>
                                <?php 
                                if ($prof['is_physical_education'] == 1) {
                                    echo '<span style="color: #10b981; font-weight: 600;">Educação Física</span>';
                                } elseif ($prof['class_name']) {
                                    echo htmlspecialchars($prof['class_name']);
                                } else {
                                    echo '<span style="color:red">Sem Turma</span>';
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($prof['whatsapp']) ?></td>
                            <td>
                                <a href="<?= url('school/professor/edit?id='.$prof['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                <?php if (!empty($prof['whatsapp'])): 
                                    $phone = preg_replace('/\D/', '', $prof['whatsapp']);
                                    if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                        $phone = '55' . $phone;
                                    }
                                ?>
                                    <a href="https://wa.me/<?= $phone ?>?text=Olá, professor(a) <?= urlencode($prof['name']) ?>!" target="_blank" class="btn-icon" style="color: #25D366;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                                <a href="<?= url('school/professor/reset-password?id='.$prof['id']) ?>" class="btn-icon" style="color: #f59e0b;" title="Resetar Senha" onclick="return confirm('Resetar a senha do professor <?= htmlspecialchars($prof['name']) ?> para \'professor123\'?')"><i class="fas fa-key"></i></a>
                                <a href="<?= url('school/professor/delete?id='.$prof['id']) ?>" class="btn-icon" style="color: red;" onclick="return confirm('Tem certeza que vai excluir o professor? (Esta ação não pode ser desfeita)')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }



    function markUploadsViewed(btn) {
        // Hides badge immediately
        const badge = document.getElementById('badge-uploads');
        if (badge) badge.style.display = 'none';

        // Notify server
        fetch('<?= url('school/mark-viewed') ?>')
            .then(response => response.json())
            .then(data => console.log('Uploads viewed updated'))
            .catch(error => console.error('Error updating view:', error));
    }

    // Dropdown toggle logic
    document.addEventListener('click', function(e) {
        if (e.target.closest('.dropdown > button')) {
            const dropdown = e.target.closest('.dropdown');
            const content = dropdown.querySelector('.dropdown-content');
            const isOpen = content.style.display === 'block';
            
            // Close all first
            document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = 'none');
            
            // Toggle current
            content.style.display = isOpen ? 'none' : 'block';
            e.stopPropagation();
        } else {
            document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = 'none');
        }
    });
    // Open specific tab if in URL params (e.g. from filtering)
    <?php if(isset($_GET['period_id']) || isset($_GET['professor_id']) || isset($_GET['status']) || (isset($_GET['tab']) && $_GET['tab'] == 'uploads')): ?>
        document.addEventListener('DOMContentLoaded', () => {
             openTab(null, 'tab-uploads');
        });
    <?php endif; ?>
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
