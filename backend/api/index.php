<?php
/**
 * PHP REST API Entry Point / Router
 * All requests are routed through this file via .htaccess
 */

declare(strict_types=1);

// ── CORS ──────────────────────────────────────────────────────────────────────
header('Access-Control-Allow-Origin: https://app.creyaengineering.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/UserController.php';

// ── Parse Request ─────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// No /api prefix needed — this PHP lives at the root of api.medalya.in
// Strip trailing slash, default to /
$uri = rtrim($uri, '/') ?: '/';

// Extract ID segment: /users/5 → $resource = users, $id = 5
$parts    = explode('/', trim($uri, '/'));
$resource = $parts[0] ?? '';
$id       = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : null;

// ── Route ─────────────────────────────────────────────────────────────────────
try {
    switch ($resource) {
        // GET /api/health
        case 'health':
            $dbStatus = 'disconnected';
            try {
                $db = Database::getInstance()->getConnection();
                $dbStatus = 'connected';
            } catch (Throwable $e) {
                $dbStatus = 'disconnected: ' . $e->getMessage();
            }
            echo json_encode([
                'status'      => 'ok',
                'server_time' => date('Y-m-d H:i:s'),
                'php_version' => PHP_VERSION,
                'db'          => $dbStatus,
            ]);
            break;

        // /api/users[/:id]
        case 'users':
            $controller = new UserController();
            match (true) {
                $method === 'GET'    && $id === null => $controller->index(),
                $method === 'GET'    && $id !== null => $controller->show($id),
                $method === 'POST'   && $id === null => $controller->store(),
                $method === 'PUT'    && $id !== null => $controller->update($id),
                $method === 'DELETE' && $id !== null => $controller->destroy($id),
                default => respond(405, ['message' => 'Method Not Allowed']),
            };
            break;

        default:
            respond(404, ['message' => 'Route not found']);
    }
} catch (Throwable $e) {
    respond(500, ['message' => 'Internal Server Error', 'detail' => $e->getMessage()]);
}

// ── Helper ────────────────────────────────────────────────────────────────────
function respond(int $code, array $data): void
{
    http_response_code($code);
    echo json_encode($data);
    exit();
}
