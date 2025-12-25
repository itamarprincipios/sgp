    </div> <!-- End main-container -->

<?php if(auth() && in_array(auth()['role'], ['semed', 'professor', 'coordinator'])): ?>
    <!-- Global Password Change Modal -->
    <div id="modal-password-global" class="modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: var(--card-bg, #fff); margin: 15% auto; padding: 25px; border-radius: 12px; width: 320px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; color: var(--text-color, #333);">
            <span onclick="document.getElementById('modal-password-global').style.display='none'" style="position: absolute; right: 20px; top: 15px; cursor: pointer; font-size: 24px; opacity: 0.6;">&times;</span>
            <h3 style="margin-top: 0; font-size: 1.25rem; display:flex; align-items:center; gap:10px;">🔐 <span style="font-size:1.1rem">Alterar Senha</span></h3>
            
            <?php 
                $actionUrl = '';
                if(auth()['role'] == 'semed') $actionUrl = url('semed/password/change');
                elseif(auth()['role'] == 'professor') $actionUrl = url('professor/password/change');
                elseif(auth()['role'] == 'coordinator') $actionUrl = url('school/password/change');
            ?>

            <form action="<?= $actionUrl ?>" method="POST" style="margin-top: 20px;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nova Senha</label>
                    <input type="password" name="password" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;" placeholder="Digite a nova senha">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; font-weight: 600; background: #667eea; color:white; border:none; border-radius:6px; cursor:pointer;">Salvar</button>
            </form>
        </div>
    </div>
<?php endif; ?>


</body>
</html>
