<?php
// app/controllers/NoteController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Note.php';
require_once ROOT_PATH . '/app/models/Matiere.php';
require_once ROOT_PATH . '/app/models/Classe.php';
require_once ROOT_PATH . '/app/models/Eleve.php';

class NoteController extends Controller {

    private Note    $noteModel;
    private Matiere $matiereModel;
    private Classe  $classeModel;
    private Eleve   $eleveModel;

    public function __construct() {
        $this->noteModel    = new Note();
        $this->matiereModel = new Matiere();
        $this->classeModel  = new Classe();
        $this->eleveModel   = new Eleve();
    }

    // ─────────────────────────────────────────
    // GET /notes — Page principale saisie
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $user     = AuthMiddleware::user();
        $classeId = (int) $this->get('classe', 0);
        $periode  = (int) $this->get('periode', 1);

        // Prof : uniquement ses matières
        if (AuthMiddleware::hasRole(ROLE_PROF)) {
            $matieres = $this->matiereModel->parProf($user['id']);
            $classes  = array_unique(array_column($matieres, 'classe_id'));
        } else {
            $matieres = $classeId > 0
                ? $this->matiereModel->parClasse($classeId)
                : [];
        }

        $classes = $this->classeModel->toutesLesClasses();

        $this->render('notes/saisie', [
            'title'     => 'Notes',
            'pageTitle' => 'Saisie des notes',
            'user'      => $user,
            'flash'     => $this->getFlash(),
            'classes'   => $classes,
            'matieres'  => $matieres,
            'classeId'  => $classeId,
            'periode'   => $periode,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /notes/eleve/{id} — Notes d'un élève
    // ─────────────────────────────────────────
    public function eleve(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF, ROLE_PARENT]);

        $id      = (int) $param;
        $periode = (int) $this->get('periode', 0);
        $eleve   = $this->eleveModel->ficheComplete($id);

        if (!$eleve) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('eleves');
        }

        $moyennes = $this->noteModel->moyennesParEleve($id, $periode);
        $notes    = $this->noteModel->parEleve($id, $periode);

        // Calcul moyenne générale par période
        $moyGenerales = [];
        for ($p = 1; $p <= 3; $p++) {
            $moyGenerales[$p] = $this->noteModel->moyenneGenerale($id, $p);
        }

        $this->render('notes/eleve', [
            'title'       => 'Notes — ' . $eleve['prenom'] . ' ' . $eleve['nom'],
            'pageTitle'   => 'Relevé de notes',
            'user'        => AuthMiddleware::user(),
            'flash'       => $this->getFlash(),
            'eleve'       => $eleve,
            'csrf_token'  => $this->generateCsrfToken(),
            'notes'       => $notes,
            'moyennes'    => $moyennes,
            'moyGenerales'=> $moyGenerales,
            'periode'     => $periode,
        ]);
    }

    // ═══════════════════════════════════════════
    // API REST — /notes/api/...
    // Toutes les réponses sont en JSON
    // ═══════════════════════════════════════════

    /**
     * GET /notes/api/eleves?classe_id=X&periode=Y
     * Retourne la liste des élèves d'une classe avec leurs notes pour une matière
     */
    public function apiEleves(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classeId   = (int) $this->get('classe_id', 0);
        $matiereId  = (int) $this->get('matiere_id', 0);
        $periode    = (int) $this->get('periode', 1);

        if ($classeId <= 0) {
            $this->json(['error' => 'classe_id manquant'], 400);
        }

        $eleves = $this->eleveModel->parClasse($classeId);

        if ($matiereId > 0) {
            // Ajouter les notes existantes pour chaque élève
            $notesExistantes = $this->noteModel->parMatiereEtClasse($matiereId, $periode);
            $noteIndex = [];
            foreach ($notesExistantes as $n) {
                $noteIndex[$n['eleve_id']][] = $n;
            }

            foreach ($eleves as &$eleve) {
                $eleve['notes'] = $noteIndex[$eleve['id']] ?? [];
                // Calcul de la moyenne courante
                if (!empty($eleve['notes'])) {
                    $eleve['moyenne'] = round(
                        array_sum(array_column($eleve['notes'], 'note')) / count($eleve['notes']),
                        2
                    );
                } else {
                    $eleve['moyenne'] = null;
                }
            }
        }

        $this->json(['eleves' => $eleves, 'total' => count($eleves)]);
    }

    /**
     * GET /notes/api/matieres?classe_id=X
     * Retourne les matières d'une classe
     */
    public function apiMatieres(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classeId = (int) $this->get('classe_id', 0);

        if ($classeId <= 0) {
            $this->json(['error' => 'classe_id manquant'], 400);
        }

        $matieres = $this->matiereModel->parClasse($classeId);
        $this->json(['matieres' => $matieres]);
    }

    /**
     * POST /notes/api/sauvegarder
     * Sauvegarde une note pour un élève
     * Body JSON: { eleve_id, matiere_id, note, type_eval, periode, date_eval, commentaire }
     */
    public function apiSauvegarder(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->json(['error' => 'Données JSON invalides'], 400);
        }

        // Validation
        $eleveId   = (int) ($input['eleve_id']   ?? 0);
        $matiereId = (int) ($input['matiere_id'] ?? 0);
        $note      = (float) ($input['note']      ?? -1);
        $typeEval  = $input['type_eval']  ?? 'devoir';
        $periode   = (int) ($input['periode']    ?? 1);
        $dateEval  = $input['date_eval']  ?? date('Y-m-d');
        $commentaire = trim($input['commentaire'] ?? '');

        if ($eleveId <= 0 || $matiereId <= 0) {
            $this->json(['error' => 'eleve_id et matiere_id sont obligatoires'], 400);
        }

        if ($note < 0 || $note > 20) {
            $this->json(['error' => 'La note doit être entre 0 et 20'], 400);
        }

        if (!in_array($typeEval, ['devoir', 'composition', 'examen'])) {
            $this->json(['error' => 'Type d\'évaluation invalide'], 400);
        }

        if ($periode < 1 || $periode > 3) {
            $this->json(['error' => 'Période invalide (1, 2 ou 3)'], 400);
        }

        // Insérer la note
        $id = $this->noteModel->insert([
            'eleve_id'    => $eleveId,
            'matiere_id'  => $matiereId,
            'note'        => $note,
            'type_eval'   => $typeEval,
            'periode'     => $periode,
            'date_eval'   => $dateEval,
            'commentaire' => $commentaire ?: null,
        ]);

        // Recalculer la moyenne
        $moyennes = $this->noteModel->moyennesParEleve($eleveId, $periode);
        $moyMatiere = null;
        foreach ($moyennes as $m) {
            if ($m['matiere_id'] == $matiereId) {
                $moyMatiere = $m['moyenne'];
                break;
            }
        }

        $this->json([
            'success'  => true,
            'note_id'  => $id,
            'moyenne'  => $moyMatiere,
            'message'  => 'Note enregistrée avec succès.',
        ]);
    }

    /**
     * DELETE /notes/api/supprimer/{id}
     * Supprime une note
     */
    public function apiSupprimer(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST'); // On utilise POST avec _method=DELETE pour compatibilité

        $id   = (int) $param;
        $note = $this->noteModel->findById($id);

        if (!$note) {
            $this->json(['error' => 'Note introuvable'], 404);
        }

        $this->noteModel->delete($id);

        // Recalculer la moyenne après suppression
        $moyenne = $this->noteModel->moyenneGenerale($note['eleve_id'], $note['periode']);

        $this->json([
            'success' => true,
            'moyenne' => $moyenne,
            'message' => 'Note supprimée.',
        ]);
    }

    /**
     * GET /notes/api/moyennes?eleve_id=X&periode=Y
     * Retourne les moyennes d'un élève
     */
    public function apiMoyennes(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF, ROLE_PARENT]);

        $eleveId = (int) $this->get('eleve_id', 0);
        $periode = (int) $this->get('periode', 0);

        if ($eleveId <= 0) {
            $this->json(['error' => 'eleve_id manquant'], 400);
        }

        $moyennes = $this->noteModel->moyennesParEleve($eleveId, $periode);
        $moyGen   = $periode > 0
            ? $this->noteModel->moyenneGenerale($eleveId, $periode)
            : null;

        $this->json([
            'moyennes'         => $moyennes,
            'moyenne_generale' => $moyGen,
        ]);
    }
}
