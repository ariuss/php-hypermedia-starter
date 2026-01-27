<?php

require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/auth.php';
require __DIR__ . '/../lib/csrf.php';

$email = '';
$password = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            auth_login($user);
            header('Location: /dashboard', true, 303);
            exit;
        } else {
            http_response_code(401);
            $errors['login'] = 'Invalid email or password.';
        }
    } else {
        http_response_code(422);
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex h-100">
    <div
      class="d-flex w-100 h-100 p-3 mx-auto flex-column justify-content-center"
      style="max-width: 25em;"
    >
        <div class="d-flex justify-content-center">        
            <form class="card card-body" method="post" action="">
                <h1 class="h3 mb-3 fw-bold text-center">Login</h1>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control<?= !empty($errors['email']) ? ' is-invalid': '' ?>"
                        value="<?= $email ?>"
                        required
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['email'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control<?= !empty($errors['password']) ? ' is-invalid': '' ?>"
                        value="<?= $password ?>"
                        required
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['password'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($errors['login']): ?>
                    <div class="alert alert-warning" role="alert">
                        <?= htmlspecialchars($errors['login']) ?>
                    </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary w-100 text-uppercase">Log in</button>

                <p class="text-center mt-3">
                    Don't have an account?
                    <a href="/register" class="text-decoration-none">
                        Register
                    </a>
                </p>
            </form>
        
        </div>
    </div>
</body>
</html>