<?php

require __DIR__ . '/../lib/auth.php';
require __DIR__ . '/../lib/helpers.php';

require_auth();

?>

<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex h-100">
    <div
      class="d-flex w-100 h-100 p-3 mx-auto flex-column justify-content-center"
      style="max-width: 27em;"
    >
        <div class="d-flex justify-content-center">        
            <div class="card card-body border-0" method="post" action="">
                <main class="px-3 text-center">
                    <?php render_partial('example', ['name' => 'world']); ?>

                    <p class="lead mt-4">
                        <a href="/logout" class="btn btn-primary px-4 text-uppercase">
                            Log out
                        </a>
                    </p>
                </main>
            </div>
        </div>
    </div>
</body>
</html>