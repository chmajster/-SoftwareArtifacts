<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../Views/' . $layout . '.php';

        if (!is_file($viewFile)) {
            http_response_code(404);
            echo 'View not found.';
            return;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layoutFile;
    }
}
