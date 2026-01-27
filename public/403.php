<?php http_response_code(403); ?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Forbidden</title>
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
                    <h1 class="h3 my-3 fw-bold text-center">403 - Access Forbidden</h1>
                    <p class="lead mt-3">
                        You don’t have permission to access this page.
                        <a href="/">
                            Go to Home
                        </a>
                    </p>
                </main>
            </div>
        </div>
    </div>
</body>
</html>