<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>✏️ Editar Escola</h1>
    <p>Atualize as informações da unidade escolar.</p>
</div>

<div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #e2e8f0;">
    <form action="<?= url('admin/school/update') ?>" method="POST" class="modern-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <input type="hidden" name="id" value="<?= $school['id'] ?>">
        
        <div class="form-group">
            <label class="modern-label">Nome da Escola</label>
            <input type="text" name="name" value="<?= htmlspecialchars($school['name']) ?>" required class="modern-input">
        </div>

        <div class="form-group">
            <label class="modern-label">Localidade (Endereço)</label>
            <input type="text" name="address" value="<?= htmlspecialchars($school['address'] ?? '') ?>" class="modern-input">
        </div>

        <div class="form-group">
            <label class="modern-label">Nome do Diretor(a)</label>
            <input type="text" name="director_name" value="<?= htmlspecialchars($school['director_name'] ?? '') ?>" class="modern-input">
        </div>

        <div class="form-group">
            <label class="modern-label">Telefone do Diretor(a)</label>
            <input type="text" name="director_phone" value="<?= htmlspecialchars($school['director_phone'] ?? '') ?>" class="modern-input">
        </div>
        
        <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px; display: flex; gap: 10px;">
            <button type="submit" class="modern-btn">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
            <a href="<?= url('admin/schools') ?>" class="modern-btn" style="background: #ef4444; width: auto; text-decoration: none;">
                Cancelar
            </a>
        </div>
    </form>
    
    <style>
        .modern-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .modern-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            color: #334155;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        .modern-input:focus {
            border-color: #3b82f6;
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .modern-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }
    </style>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
