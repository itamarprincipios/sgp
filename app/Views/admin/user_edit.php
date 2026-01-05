<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>✏️ Editar Usuário SEMED</h1>
    <p>Atualize dados e vínculos deste gestor.</p>
</div>

<div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #e2e8f0;">
    <form action="<?= url('admin/user/update') ?>" method="POST" class="modern-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <input type="hidden" name="role" value="semed"> <!-- Keep role -->
        
        <div class="form-group">
            <label class="modern-label">Nome Completo</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="modern-input">
        </div>

        <div class="form-group">
            <label class="modern-label">Email (Login)</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="modern-input">
        </div>

        <div class="form-group">
            <label class="modern-label"><i class="fab fa-whatsapp" style="color: #25D366;"></i> WhatsApp</label>
            <input type="text" name="whatsapp" value="<?= htmlspecialchars($user['whatsapp'] ?? '') ?>" class="modern-input">
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
            <label class="modern-label">Escolas Vinculadas</label>
            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 10px;">Adicione ou remova escolas. Escolas já vinculadas a outros usuários não aparecem aqui.</p>
            
            <div class="school-selector-container" style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 15px;">
                <div style="display: flex; gap: 10px; margin-bottom: 0;">
                    <div id="schools-inputs-container"></div>
                    
                    <div style="position: relative; flex-grow: 1;">
                        <select id="school-select-source" class="modern-select" style="width: 100%;">
                            <option value="" disabled selected>+ Adicionar Escola...</option>
                            <?php foreach($schools as $s): ?>
                                <option value="<?= $s['id'] ?>" data-name="<?= htmlspecialchars($s['name']) ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" id="btn-add-school" class="modern-btn" style="width: auto; padding: 0 20px; background: #22c55e;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <div id="selected-schools-list" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; min-height: 40px;">
                    <span style="color: #94a3b8; font-style: italic; font-size: 0.9rem; align-self: center;" id="no-schools-msg">Nenhuma escola vinculada.</span>
                </div>
            </div>
        </div>
        
        <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px; display: flex; gap: 10px;">
            <button type="submit" class="modern-btn">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
            <a href="<?= url('admin/dashboard') ?>" class="modern-btn" style="background: #ef4444; width: auto; text-decoration: none;">
                Cancelar
            </a>
        </div>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sourceSelect = document.getElementById('school-select-source');
            const addBtn = document.getElementById('btn-add-school');
            const listContainer = document.getElementById('selected-schools-list');
            const selectedIds = new Set();
            
            function addSchool(id, name) {
                if(!id || selectedIds.has(id)) return;
                
                selectedIds.add(id);
                document.getElementById('no-schools-msg').style.display = 'none';
                
                const option = sourceSelect.querySelector(`option[value="${id}"]`);
                if(option) option.hidden = true;
                
                const item = document.createElement('div');
                item.className = 'selected-school-item';
                item.style.cssText = 'background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.3s;';
                item.innerHTML = `
                    <span>${name}</span>
                    <button type="button" style="background: none; border: none; color: #e11d48; cursor: pointer; font-size: 1.1rem; padding: 0;" onclick="removeSchool(this, '${id}')">&times;</button>
                    <input type="hidden" name="schools[]" value="${id}">
                `;
                
                listContainer.appendChild(item);
                sourceSelect.value = "";
            }
            
            window.removeSchool = function(btn, id) {
                btn.parentElement.remove();
                selectedIds.delete(id);
                
                const option = sourceSelect.querySelector(`option[value="${id}"]`);
                if(option) option.hidden = false;
                
                if(selectedIds.size === 0) {
                    document.getElementById('no-schools-msg').style.display = 'block';
                }
            };
            
            addBtn.addEventListener('click', () => {
                const id = sourceSelect.value;
                if(id) {
                    const option = sourceSelect.options[sourceSelect.selectedIndex];
                    const name = option.getAttribute('data-name');
                    addSchool(id, name);
                }
            });

            // Initialize with assigned schools
            <?php 
            // We need to match IDs to Names from the $schools list which contains ALL available + assigned
            // Javascript needs to know the names.
            // Let's iterate PHP-side and output calls to addSchool
            if (!empty($assignedSchoolIds)) {
                foreach ($assignedSchoolIds as $assignedId) {
                    // Find name in $schools array
                    $name = '';
                    foreach ($schools as $s) {
                        if ($s['id'] == $assignedId) {
                            $name = $s['name'];
                            break;
                        }
                    }
                    if ($name) {
                        echo "addSchool('$assignedId', " . json_encode($name) . ");\n";
                    }
                }
            }
            ?>
        });
    </script>
    
    <style>
        .modern-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .modern-input, .modern-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            color: #334155;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        .modern-input:focus, .modern-select:focus {
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
