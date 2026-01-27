<?php

require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../lib/auth.php';
require __DIR__ . '/../lib/csrf.php';
require __DIR__ . '/../lib/log.php';

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
    } else {
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            $errors['password'] = 'Password must be at least 8 characters long and contain at least one letter and one number.';
        }
    }

    if (empty($errors)) {
        try {
            $db = get_db();
            $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
            $stmt->execute([
                ':email'    => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            header("Location: /login", true, 303);
            exit;
        } catch (PDOException $exception) {
            // SQLite constraint violation error code = 23000 (SQLSTATE)
            if ($exception->getCode() === '23000') {
                $errors['email'] = 'Email address is already registered.';
                http_response_code(422);
            } else {
                log_event(LogLevel::ERROR, 'Unexpected DB error', $exception->getTrace());
                http_response_code(500);
            }
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
    <title>Registration</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex h-100">
    <div
      class="d-flex w-100 h-100 p-3 mx-auto flex-column justify-content-center"
      style="max-width: 25em;"
    >
        <div class="d-flex justify-content-center">
            <form class="card card-body" method="post" action="">
                <h1 class="h3 mb-3 fw-bold text-center">Registration</h1>

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

                <button type="submit" class="btn btn-primary w-100 text-uppercase">Register</button>

                <p class="text-center mt-3">
                    Already have an account?
                    <a href="/login" class="text-decoration-none">
                        Login
                    </a>
                </p>
            </form>
        </div>
    <div >
</body>
</html>