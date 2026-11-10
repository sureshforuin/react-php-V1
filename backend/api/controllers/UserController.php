<?php
/**
 * UserController — handles CRUD for /api/users
 */

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

class UserController
{
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    /** GET /api/users */
    public function index(): void
    {
        $users = $this->model->findAll();
        echo json_encode($users);
    }

    /** GET /api/users/:id */
    public function show(int $id): void
    {
        $user = $this->model->findById($id);
        if (!$user) {
            respond(404, ['message' => 'User not found']);
        }
        echo json_encode($user);
    }

    /** POST /api/users */
    public function store(): void
    {
        $data = $this->parseBody();

        if (empty($data['name']) || empty($data['email'])) {
            respond(422, ['message' => 'name and email are required']);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            respond(422, ['message' => 'Invalid email address']);
        }

        $id   = $this->model->create(trim($data['name']), trim($data['email']));
        $user = $this->model->findById($id);

        http_response_code(201);
        echo json_encode($user);
    }

    /** PUT /api/users/:id */
    public function update(int $id): void
    {
        $existing = $this->model->findById($id);
        if (!$existing) {
            respond(404, ['message' => 'User not found']);
        }

        $data   = $this->parseBody();
        $name   = trim($data['name']   ?? $existing['name']);
        $email  = trim($data['email']  ?? $existing['email']);
        $status = trim($data['status'] ?? $existing['status']);

        if (!in_array($status, ['active', 'inactive'], true)) {
            respond(422, ['message' => 'status must be active or inactive']);
        }

        $this->model->update($id, $name, $email, $status);
        echo json_encode($this->model->findById($id));
    }

    /** DELETE /api/users/:id */
    public function destroy(int $id): void
    {
        if (!$this->model->findById($id)) {
            respond(404, ['message' => 'User not found']);
        }

        $this->model->delete($id);
        http_response_code(200);
        echo json_encode(['message' => 'User deleted']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function parseBody(): array
    {
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            respond(400, ['message' => 'Invalid JSON body']);
        }

        return $data ?? [];
    }
}
