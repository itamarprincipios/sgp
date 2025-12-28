<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 20px 15px !important;
        }
        
        .dashboard-header h1 {
            font-size: 1.5rem !important;
        }
        
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
        
        .tabs {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .tab-btn {
            white-space: nowrap;
            flex-shrink: 0;
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        
        /* Formulários responsivos */
        form[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        form button[type="submit"] {
            width: 100%;
        }
    }
</style>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>🛡️ Painel do Super Admin</h1>
    <p>Controle total do sistema</p>
</div>


<!-- Stats -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #2563eb;">
        <h3>Escolas</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['schools'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #16a34a;">
        <h3>SEMED</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['semed'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #9333ea;">
        <h3>Coordenadores</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['coordinators'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #ca8a04;">
        <h3>Professores</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['professors'] ?></div>
    </div>
</div>

<div class="tabs">
    <button class="tab-btn active" onclick="openTab(event, 'tab-semed')">Administradores SEMED</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-schools')">Escolas</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-coordinators')">Coordenadores</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-professors')">Professores</button>
</div>

<!-- TAB SEMED -->
<div id="tab-semed" class="tab-content active">
    <h3>Gestão SEMED</h3>
    <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h4>Cadastrar Novo Usuário SEMED</h4>
        <form action="<?= url('admin/user/store') ?>" method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
            <input type="hidden" name="role" value="semed">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="name" required class="form-control" placeholder="Nome Completo">
            </div>
            <div class="form-group">
                <label>Email (Login)</label>
                <input type="email" name="email" required class="form-control" placeholder="email@exemplo.com">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($semedUsers as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <a href="<?= url('admin/user/reset-password?id='.$u['id']) ?>" class="btn-icon" title="Resetar Senha (123456)" onclick="return confirm('Resetar senha para 123456?')"><i class="fas fa-key"></i></a>
                        <a href="<?= url('admin/user/delete?id='.$u['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Excluir este usuário?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- TAB Schools -->
<div id="tab-schools" class="tab-content">
    <h3>Gestão Escolas</h3>
    <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h4>Cadastrar Nova Escola</h4>
        <form action="<?= url('admin/school/store') ?>" method="POST" style="display: grid; grid-template-columns: 1fr auto; gap: 15px; align-items: end;">
            <div class="form-group">
                <label>Nome da Escola</label>
                <input type="text" name="name" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($schools as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td>
                        <a href="<?= url('admin/school/delete?id='.$s['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Excluir esta escola?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- TAB Coordinators -->
<div id="tab-coordinators" class="tab-content">
    <h3>Gestão Coordenadores</h3>
    <!-- Creation logic is complex due to school linking, keeping simple listing/deletion/reset here -->
    <p>Para criar coordenadores, use o painel SEMED ou cadastre aqui associando o ID da escola manualmente se necessário (simplificado).</p>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Escola (ID)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($coordinators as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= $c['school_name'] ?? $c['school_id'] ?></td>
                    <td>
                        <a href="<?= url('admin/user/reset-password?id='.$c['id']) ?>" class="btn-icon" title="Resetar Senha" onclick="return confirm('Resetar senha?')"><i class="fas fa-key"></i></a>
                        <a href="<?= url('admin/user/delete?id='.$c['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Excluir?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- TAB Professors -->
<div id="tab-professors" class="tab-content">
    <h3>Gestão Professores</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Escola (ID)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($professors as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= $p['school_name'] ?? $p['school_id'] ?></td>
                    <td>
                        <a href="<?= url('admin/user/reset-password?id='.$p['id']) ?>" class="btn-icon" title="Resetar Senha" onclick="return confirm('Resetar senha?')"><i class="fas fa-key"></i></a>
                        <a href="<?= url('admin/user/delete?id='.$p['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Excluir?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
    // Default open
    document.getElementById("tab-semed").style.display = "block";
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
