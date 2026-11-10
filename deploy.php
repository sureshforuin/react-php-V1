<?php
/**
 * deploy.php — Pull-deploy script
 * Manually upload to: public_html/deploy.php on Hostinger (never commit to git)
 *
 * Called by GitHub Actions after every push:
 *   Frontend: POST /deploy.php  (Header: X-Deploy-Target: frontend)
 *   Backend:  POST /deploy.php  (Header: X-Deploy-Target: backend)
 */

// ── Config ────────────────────────────────────────────────────────────────────
define('DEPLOY_TOKEN', 'REPLACE_WITH_YOUR_SECRET_TOKEN');

// Hostinger paths — update SUBDOMAIN_PATH to match your actual subdomain directory
define('FRONTEND_PATH', $_SERVER['DOCUMENT_ROOT']);                               // ~/public_html/
define('SUBDOMAIN_PATH', dirname($_SERVER['DOCUMENT_ROOT']) . '/api.creyaengineering.com'); // ~/public_html/api.creyaengineering.com/

// ── Auth ──────────────────────────────────────────────────────────────────────
$token = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// ── Pull latest from deploy branch ────────────────────────────────────────────
$output = [];
exec('cd ' . escapeshellarg(FRONTEND_PATH) . ' && git pull origin deploy 2>&1', $output, $exitCode);

if ($exitCode !== 0) {
    http_response_code(500);
    echo json_encode(['success' => false, 'step' => 'git pull', 'output' => implode("\n", $output)]);
    exit();
}

// ── Copy backend files to subdomain if requested ──────────────────────────────
$target = $_SERVER['HTTP_X_DEPLOY_TARGET'] ?? 'frontend';
$copyOutput = [];

if ($target === 'backend') {
    $apiDeployPath = FRONTEND_PATH . '/api-deploy';
    $subdomainPath = SUBDOMAIN_PATH;

    if (is_dir($apiDeployPath)) {
        // Use rsync to copy api-deploy/ → subdomain directory
        exec(
            'rsync -av --delete ' .
            escapeshellarg($apiDeployPath . '/') . ' ' .
            escapeshellarg($subdomainPath . '/') . ' 2>&1',
            $copyOutput,
            $copyExitCode
        );
    } else {
        $copyOutput[] = "api-deploy/ folder not found — skipping copy";
        $copyExitCode = 0;
    }
}

// ── Respond ───────────────────────────────────────────────────────────────────
http_response_code(200);
echo json_encode([
    'success'     => true,
    'target'      => $target,
    'pull_output' => implode("\n", $output),
    'copy_output' => implode("\n", $copyOutput ?? []),
]);
