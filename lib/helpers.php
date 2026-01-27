<?php

/**
 * Render a private (prepended with dot) partial template.
 * Usage: <?php render_partial('example', ['name' => 'world']); ?>
 *
 * @param string $name Partial name in kebab-case, e.g., 'header-menu'
 * @param array $vars Associative array of variables to pass to the partial
 */
function render_partial(string $name, array $vars = []) {
    $file = __DIR__ . "/../public/partials/.{$name}.php";

    if (!file_exists($file)) {
        throw new Exception("Private partial '{$name}' not found in /public/partials/");
    }

    // Make array keys available as variables
    extract($vars);

    // Include the partial
    include $file;
}
