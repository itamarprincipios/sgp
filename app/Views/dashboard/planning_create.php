<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Cadastrar Novo Planejamento</h2>
        <a href="<?= url('school/dashboard') ?>" class="btn btn-secondary" style="width: auto; background-color: #6c757d;">Voltar</a>
    </div>
</div>

<div class="content-row">
    <div class="upload-section" style="width: 100%; max-width: 800px; margin: 0 auto;">
        <form action="<?= url('school/planning/store') ?>" method="POST">
            <div class="form-group">
                <label for="name">Nome do Planejamento</label>
                <input type="text" name="name" id="name" required placeholder="Ex: Planejamento Bimestral 01" class="form-control">
                <small style="color: #666;">Identificação principal do documento.</small>
            </div>
            
            <div class="form-group">
                <label for="description">Descrição do Período</label>
                <input type="text" name="description" id="description" required placeholder="Ex: Período de 02 a 13 de Março/2026" class="form-control">
                <small style="color: #666;">Texto explicativo para os professores.</small>
            </div>

            <div class="form-group">
                <label for="start_date">Início da Vigência</label>
                <input type="date" name="start_date" id="start_date" required class="form-control" value="<?= date('Y-m-d') ?>">
                <small style="color: #666;">O sistema definirá o prazo final de envio para 7 dias antes desta data.</small>
            </div>
            
            <input type="hidden" name="end_date" value="<?= date('Y-m-d H:i:s', strtotime('+30 days')) ?>">

            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                <input type="checkbox" name="is_physical_education" id="is_physical_education" value="1" style="width: 18px; height: 18px;">
                <label for="is_physical_education" style="margin: 0; cursor: pointer;">Este planejamento é exclusivo para <strong>Educação Física</strong>?</label>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Salvar Planejamento</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
