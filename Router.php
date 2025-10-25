<?php
namespace Src;

class Router {
    private array $routes = [];

    // Tambahkan route (GET, POST, dll)
    public function add(string $method, string $path, callable $handler) {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Hapus base path (misal /api-php-native/public)
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $basePath = rtrim($scriptName, '/');
        $uri = preg_replace('#^' . preg_quote($basePath) . '#', '', $uri);

        foreach ($this->routes as $route) {
            // Ubah {parameter} menjadi regex dinamis
            $pattern = "#^" . preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $route['path']) . "$#";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                // Ambil parameter yang cocok
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) $params[$key] = $value;
                }

                // Panggil handler dengan parameter
                call_user_func_array($route['handler'], $params);
                return;
            }
        }

        // Jika tidak ada route yang cocok
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => [
                "code" => 404,
                "message" => "Route not found",
                "path" => $uri
            ]
        ]);
    }
}