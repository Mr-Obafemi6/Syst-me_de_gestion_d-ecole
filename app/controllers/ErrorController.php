<?php
// app/controllers/ErrorController.php

require_once ROOT_PATH . '/app/core/Controller.php';

class ErrorController extends Controller {

    public function index(?string $param = null): void {
        $this->notFound();
    }

    public function notFound(): void {
        http_response_code(404);
        $data = ['title' => 'Page introuvable', 'pageTitle' => '404 — Page introuvable'];
        $this->render('errors/404', $data);
    }

    public function forbidden(): void {
        http_response_code(403);
        $data = ['title' => 'Accès refusé', 'pageTitle' => '403 — Accès refusé'];
        $this->render('errors/403', $data);
    }
}
