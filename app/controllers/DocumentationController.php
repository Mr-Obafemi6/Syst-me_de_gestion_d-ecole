<?php

require_once ROOT_PATH . '/app/core/Controller.php';

class DocumentationController extends Controller {
    public function index(?string $param = null): void {
        AuthMiddleware::requireAuth();

        $this->render('documentation/index', [
            'title' => 'Documentation',
            'pageTitle' => 'Documentation SGE',
            'user' => AuthMiddleware::user(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }
}
