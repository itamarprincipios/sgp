<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <h2>Planejamentos da Rede</h2>
</div>

<div class="list-section" style="margin-bottom: 20px;">
    <form action="" method="GET" class="filter-container">
        <div class="filter-group">
            <label class="filter-label">Filtrar por Escola</label>
            <select name="school_id" onchange="this.form.submit()" class="filter-select">
                <option value="">Todas as Escolas</option>
                <?php foreach($schools as $school): ?>
                    <option value="<?= $school['id'] ?>" <?= ($filters['school_id'] == $school['id']) ? 'selected' : '' ?>><?= htmlspecialchars($school['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if (!empty($professors)): ?>
        <div class="filter-group">
            <label class="filter-label">Filtrar por Professor</label>
            <select name="professor_id" onchange="this.form.submit()" class="filter-select">
                <option value="">Todos os Professores</option>
                <?php foreach($professors as $prof): ?>
                    <option value="<?= $prof['id'] ?>" <?= ($filters['professor_id'] == $prof['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['name']) ?>
                        <?= !empty($prof['school_name']) ? ' - ' . htmlspecialchars($prof['school_name']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <div class="filter-group" style="flex: 0 0 150px; min-width: 150px;">
            <label class="filter-label">Bimestre</label>
            <select name="bimester" onchange="this.form.submit()" class="filter-select">
                <option value="">Todos</option>
                <option value="1" <?= ($filters['bimester'] == '1') ? 'selected' : '' ?>>1º Bimestre</option>
                <option value="2" <?= ($filters['bimester'] == '2') ? 'selected' : '' ?>>2º Bimestre</option>
                <option value="3" <?= ($filters['bimester'] == '3') ? 'selected' : '' ?>>3º Bimestre</option>
                <option value="4" <?= ($filters['bimester'] == '4') ? 'selected' : '' ?>>4º Bimestre</option>
            </select>
        </div>

        <div class="filter-group" style="flex: 0 0 180px; min-width: 180px;">
            <label class="filter-label">Status</label>
            <select name="status" onchange="this.form.submit()" class="filter-select">
                <option value="">Todos os Status</option>
                <option value="aprovado" <?= ($filters['status'] == 'aprovado') ? 'selected' : '' ?>>Aprovados</option>
                <option value="ajustado" <?= ($filters['status'] == 'ajustado') ? 'selected' : '' ?>>Aprovados c/ Ajustes</option>
                <option value="enviado" <?= ($filters['status'] == 'enviado') ? 'selected' : '' ?>>Aguardando Revisão</option>
            </select>
        </div>
        
        <div class="filter-actions">
            <a href="<?= url('semed/plannings') ?>" class="btn-filter-clear">
                <i class="fas fa-times"></i> Limpar
            </a>
        </div>
    </form>
</div>

</div>

<?php if(!empty($documents)): ?>
<div class="list-section" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; justify-content: space-around;">
    <div style="flex: 1; min-width: 300px; max-width: 450px;">
        <canvas id="statusChart"></canvas>
    </div>
    <div style="flex: 1; min-width: 300px;">
        <h3 style="color: #666; margin-bottom: 15px;">Resumo dos Envios</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            <div style="background: #e8f5e9; padding: 15px; border-radius: 10px; border-left: 4px solid #28a745;">
                <div style="font-size: 0.9rem; color: #2e7d32; font-weight: bold;">Aprovados</div>
                <div style="font-size: 1.5rem; font-weight: bold; color: #1b5e20;"><?= $statusCounts['aprovado'] ?></div>
            </div>
            <div style="background: #fff3e0; padding: 15px; border-radius: 10px; border-left: 4px solid #ff9800;">
                 <div style="font-size: 0.9rem; color: #e65100; font-weight: bold;">Com Ajustes</div>
                 <div style="font-size: 1.5rem; font-weight: bold; color: #bf360c;"><?= $statusCounts['ajustado'] ?></div>
            </div>
            <div style="background: #ffebee; padding: 15px; border-radius: 10px; border-left: 4px solid #dc3545;">
                 <div style="font-size: 0.9rem; color: #c62828; font-weight: bold;">Reprovados</div>
                 <div style="font-size: 1.5rem; font-weight: bold; color: #b71c1c;"><?= $statusCounts['rejeitado'] ?></div>
            </div>
            <div style="background: #e3f2fd; padding: 15px; border-radius: 10px; border-left: 4px solid #007bff;">
                 <div style="font-size: 0.9rem; color: #1565c0; font-weight: bold;">Aguardando</div>
                 <div style="font-size: 1.5rem; font-weight: bold; color: #0d47a1;"><?= $statusCounts['enviado'] ?></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Aprovado', 'Com Ajustes', 'Reprovado', 'Aguardando'],
                datasets: [{
                    data: [
                        <?= $statusCounts['aprovado'] ?>,
                        <?= $statusCounts['ajustado'] ?>,
                        <?= $statusCounts['rejeitado'] ?>,
                        <?= $statusCounts['enviado'] ?>
                    ],
                    backgroundColor: [
                        '#28a745', // Green
                        '#ffc107', // Yellow/Orange
                        '#dc3545', // Red
                        '#007bff'  // Blue
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 20 }
                    },
                    title: {
                        display: true,
                        text: 'Status dos Planejamentos Filtrados',
                        font: { size: 16 }
                    }
                }
            }
        });
    });
</script>
<?php endif; ?>

<div class="list-section">
    <table class="data-table">
        <thead>
            <tr>
                <th>Data Env.</th>
                <th>Escola</th>
                <th>Professor</th>
                <th>Planejamento</th>
                <th style="text-align: center;">Bim.</th>
                <th>Status</th>
                <th>Documento</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($documents)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 40px; color: #999;">Nenhum planejamento encontrado com os filtros selecionados.</td></tr>
            <?php else: ?>
                <?php foreach($documents as $doc): ?>
                    <tr>
                        <td style="font-size: 0.85rem;"><?= date('d/m/Y', strtotime($doc['submitted_at'])) ?></td>
                        <td style="font-weight: bold; font-size: 0.85rem;"><?= htmlspecialchars($doc['school_name']) ?></td>
                        <td><?= htmlspecialchars($doc['professor_name']) ?></td>
                        <td style="font-size: 0.85rem;"><?= htmlspecialchars($doc['planning_name']) ?></td>
                        <td style="text-align: center;"><strong><?= $doc['bimester'] ?>º</strong></td>
                        <td>
                            <?php 
                            $statusLabel = 'Enviado';
                            $statusClass = 'status-sent';
                            if ($doc['status'] == 'aprovado') { $statusLabel = 'Aprovado'; $statusClass = 'status-approved'; }
                            elseif ($doc['status'] == 'ajustado') { $statusLabel = 'Aprovação c/ Ajustes'; $statusClass = 'status-adjusted'; }
                            elseif ($doc['status'] == 'rejeitado') { $statusLabel = 'Devolvido'; $statusClass = 'status-rejected'; }
                            ?>
                            <span class="status-badge <?= $statusClass ?>" style="font-size: 0.75rem; padding: 3px 8px; border-radius: 12px;">
                                <?= $statusLabel ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= url($doc['file_path']) ?>" target="_blank" class="btn btn-sm" style="background: #f1f3f5; color: #333; border: 1px solid #ddd; width: auto; font-size: 0.7rem;">
                                <i class="fas fa-download"></i> Ver Arquivo
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
