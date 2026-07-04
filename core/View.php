<?php
class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $file = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($file)) {
            echo "Vista no encontrada: $view";
            return;
        }

        require $file;
    }

    public static function partial(string $view, array $data = []): void
    {
        self::render($view, $data);
    }

    public static function layout(string $layout, string $content, array $data = []): void
    {
        $data['content'] = $content;
        self::render($layout, $data);
    }

    public static function capture(string $view, array $data = []): string
    {
        ob_start();
        self::render($view, $data);
        return ob_get_clean();
    }
}
