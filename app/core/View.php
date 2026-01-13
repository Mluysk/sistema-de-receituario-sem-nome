<?php
class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            http_response_code(404);
            exit('View não encontrada.');
        }
        include __DIR__ . '/../views/layout.php';
    }
}
