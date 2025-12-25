<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGP - Login</title>
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="login-container">
        <div class="logo">
            <h1>SGP</h1>
            <p>Sistema de Gestão Pedagógica</p>
        </div>
        
        <?php if (session('error')): ?>
            <div class="alert error">
                <?= session('error'); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('auth/verify') ?>" method="POST">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required placeholder="seu@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" required placeholder="********" style="padding-right: 40px;">
                    <span onclick="togglePassword('password', 'eye-icon-login')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;">
                        <i id="eye-icon-login" class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
    
    <!-- WhatsApp Button - Only on Login Page -->
    <div style="position: fixed; bottom: 20px; left: 20px; z-index: 9999;">
        <a href="https://wa.me/5595991248941?text=Olá, preciso de suporte no sistema SGP." target="_blank" style="background: #25D366; color: white; padding: 10px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 8px; transition: transform 0.2s;">
            <i class="fab fa-whatsapp" style="font-size: 1.2rem;"></i> Fale Conosco
        </a>
    </div>
    
    <!-- N CIRCUITS Logo - Only on Login Page -->
    <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); text-align: center;">
        <div style="background: white; padding: 8px 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <img src="<?= url('img/n_circuits_logo.png') ?>" alt="N Circuits Technologies" style="width: 100px; display: block;">
        </div>
    </div>
    </div>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
