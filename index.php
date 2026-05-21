<?php
session_start();
require_once __DIR__ . '/classes/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = User::findByEmail( $email);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        if ($user['role'] === 'demandeur') {
            header('Location: dashboard_demandeur.php');
        } elseif ($user['role'] === 'validateur') {
            header('Location: dashboard_validateur.php');
        } else {
            header('Location: dashboard_admin.php');
        }
        exit;
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion - Gestion des Besoins 2</title>
  <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .login-container {
      width: 100%;
      max-width: 450px;
      padding: 20px;
    }
    .login-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      animation: slideUp 0.5s ease-out;
    }
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .login-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 40px 30px;
      text-align: center;
      color: white;
    }
    .login-logo {
      width: 80px;
      height: 80px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      font-size: 2.5rem;
      backdrop-filter: blur(10px);
    }
    .login-header h2 {
      margin: 0;
      font-size: 1.8rem;
      font-weight: 600;
    }
    .login-header p {
      margin: 10px 0 0;
      opacity: 0.9;
      font-size: 0.95rem;
    }
    .login-body {
      padding: 40px 35px;
    }
    .form-floating {
      margin-bottom: 20px;
    }
    .form-floating > .form-control {
      border-radius: 12px;
      border: 2px solid #e9ecef;
      padding: 1rem 1rem 1rem 3rem;
      height: calc(3.5rem + 2px);
      transition: all 0.3s ease;
    }
    .form-floating > .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    .form-floating > label {
      padding-left: 3rem;
      color: #6c757d;
    }
    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #667eea;
      font-size: 1.2rem;
      z-index: 4;
      pointer-events: none;
    }
    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 12px;
      padding: 15px;
      font-size: 1.1rem;
      font-weight: 600;
      color: white;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 10px;
    }
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    .alert {
      border-radius: 12px;
      border: none;
      padding: 15px;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-danger {
      background-color: #fee;
      color: rgba(61, 8, 8, 1);
    }
  </style>
</head>
<body>
<div class="login-container">
  <div class="login-card">
    <div class="login-header">
      <div class="login-logo">
        <i class="bi bi-shield-lock-fill"></i>
      </div>
      <h2>Gestion des Besoins 8</h2>
      <p>Connectez-vous à votre compte</p>
    </div>
    <div class="login-body">
      <?php if ($error): ?>
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-circle-fill"></i>
          <span><?php echo htmlspecialchars($error); ?></span>
        </div>
      <?php endif; ?>
      <form method="post">
        <div class="form-floating position-relative">
          <i class="bi bi-envelope-fill input-icon"></i>
          <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="nom@exemple.com" required autofocus>
          <label for="floatingEmail">Adresse email</label>
        </div>
        <div class="form-floating position-relative">
          <i class="bi bi-lock-fill input-icon"></i>
          <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Mot de passe" required>
          <label for="floatingPassword">Mot de passe</label>
        </div>
        <button class="btn btn-login" type="submit">
          <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
        </button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
