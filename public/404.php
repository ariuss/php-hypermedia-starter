<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex h-100">
    <div
      class="d-flex w-100 h-100 p-3 mx-auto flex-column justify-content-center"
      style="max-width: 25em;"
    >
        <div class="d-flex justify-content-center">        
            <div class="card card-body border-0" method="post" action="">
                <main class="px-3 text-center">
                    <h1 class="h3 my-3 fw-bold text-center">404 - Page Not Found</h1>
                    <p class="lead mt-3">
                        Oops! The page you are looking for does not exist.
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
