<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .school-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }

    .school-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }

    .school-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    
    .school-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
    }

    .school-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        gap: 15px;
    }

    .school-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .school-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.4;
    }

    .school-info {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #6b7280;
        font-size: 0.95rem;
    }

    .info-item i {
        width: 20px;
        text-align: center;
        color: #9ca3af;
    }
    
    .info-item span.label {
        font-weight: 600;
        margin-right: 5px;
        color: #4b5563;
    }

    .whatsapp-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background-color: #25D366;
        color: white;
        border-radius: 50%;
        font-size: 14px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(37, 211, 102, 0.3);
    }

    .whatsapp-link:hover {
        transform: scale(1.1);
        background-color: #128C7E;
        box-shadow: 0 4px 8px rgba(37, 211, 102, 0.4);
    }
</style>

<div class="dashboard-header">
    <h2>Minhas Escolas</h2>
    <p style="color: #6b7280; font-size: 1rem; margin-top: 5px;">
        <i class="fas fa-info-circle"></i> Você está visualizando apenas as unidades escolares vinculadas ao seu perfil.
    </p>
</div>

<!-- Removed Upload Section/Create Form as per request -->

<?php if (empty($schools)): ?>
    <div style="text-align: center; padding: 50px; color: #9ca3af;">
        <i class="fas fa-school" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
        <p>Nenhuma escola vinculada ao seu perfil.</p>
    </div>
<?php else: ?>
    <div class="school-grid">
        <?php foreach($schools as $school): ?>
            <div class="school-card">
                <div class="school-header">
                    <div class="school-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="school-name">
                        <?= htmlspecialchars($school['name']) ?>
                    </div>
                </div>
                
                <div class="school-info">
                    <div class="info-item">
                        <i class="fas fa-user-tie" title="Diretor"></i>
                        <div>
                            <span class="label">Diretor(a):</span>
                            <?= htmlspecialchars($school['director_name'] ?? 'Não informado') ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                         <i class="fas fa-phone" title="Telefone"></i>
                         <div style="display: flex; align-items: center; gap: 8px;">
                             <span class="label">Telefone:</span>
                             <?php
                                $phoneDisplay = htmlspecialchars($school['director_phone'] ?? 'Não informado');
                                $phoneRaw = preg_replace('/\D/', '', $school['director_phone'] ?? '');
                                
                                if (!empty($phoneRaw)) {
                                    if (strlen($phoneRaw) >= 10 && substr($phoneRaw, 0, 2) != '55') {
                                        $phoneRaw = '55' . $phoneRaw;
                                    }
                                    echo "<span>{$phoneDisplay}</span>";
                                    echo '<a href="https://wa.me/' . $phoneRaw . '" target="_blank" class="whatsapp-link" title="Chamar no WhatsApp"><i class="fab fa-whatsapp"></i></a>';
                                } else {
                                    echo $phoneDisplay;
                                }
                             ?>
                         </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-id-card" title="Código INEP"></i>
                         <div>
                             <span class="label">INEP:</span>
                             <?= htmlspecialchars($school['inep_code'] ?? 'Não informado') ?>
                         </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
