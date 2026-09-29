<?php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Classe.php';
require_once ROOT_PATH . '/app/models/Enseignant.php';

class EmploiDuTempsController extends Controller {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $teacherRequest = $this->get('teacher_id', 0);
        if (is_array($teacherRequest)) {
            $teacherRequest = $teacherRequest[0] ?? 0;
        }

        $teacherId = AuthMiddleware::hasRole(ROLE_PROF)
            ? (int) (AuthMiddleware::user()['id'] ?? 0)
            : (int) $teacherRequest;

        $stmt = $this->db->prepare(
            "SELECT e.*, u.nom AS enseignant_nom, u.prenom AS enseignant_prenom, c.nom AS classe_nom, n.nom AS niveau_nom, m.nom AS matiere_nom
             FROM `enseignant_horaires` e
             JOIN `users` u ON u.id = e.teacher_id
             JOIN `classes` c ON c.id = e.class_id
             LEFT JOIN `niveaux` n ON n.id = c.niveau_id
             JOIN `matieres` m ON m.id = e.subject_id
             WHERE (? = 0 OR e.teacher_id = ?)
             ORDER BY FIELD(e.jour, 'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'), e.heure_debut"
        );
        $stmt->execute([$teacherId, $teacherId]);
        $rows = $stmt->fetchAll();

        $years = $this->db->query("SELECT id, libelle, active FROM `annees_scolaires` ORDER BY id DESC")->fetchAll();

        $this->render('emploi_du_temps/index', [
            'title' => 'Emplois du temps',
            'pageTitle' => 'Emplois du temps',
            'user' => AuthMiddleware::user(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'rows' => $rows,
            'teacherId' => $teacherId,
            'teachers' => $this->db->query("SELECT id, nom, prenom FROM `users` WHERE role = 'professeur' AND actif = 1 ORDER BY nom, prenom")->fetchAll(),
            'classes' => (new Classe())->toutesLesClasses(),
            'subjects' => $this->db->query("SELECT id, nom FROM `matieres` WHERE statut = 1 ORDER BY nom")->fetchAll(),
            'years' => $years,
        ]);
    }

    public function save(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $data = [
            'teacher_id' => (int) $this->post('teacher_id', 0),
            'class_id' => (int) $this->post('class_id', 0),
            'subject_id' => (int) $this->post('subject_id', 0),
            'jour' => trim((string) $this->post('jour', '')),
            'heure_debut' => trim((string) $this->post('heure_debut', '')),
            'heure_fin' => trim((string) $this->post('heure_fin', '')),
            'salle' => trim((string) $this->post('salle', '')),
            'school_year_id' => (int) $this->post('school_year_id', 0),
        ];

        if ($data['teacher_id'] <= 0 || $data['class_id'] <= 0 || $data['subject_id'] <= 0 || $data['school_year_id'] <= 0) {
            $this->flash('error', 'Tous les champs obligatoires doivent être remplis.');
            Router::redirect('emplois-du-temps');
        }

        if ($data['heure_debut'] === '' || $data['heure_fin'] === '') {
            $this->flash('error', 'Les heures de début et de fin sont obligatoires.');
            Router::redirect('emplois-du-temps');
        }

        if ($data['heure_debut'] >= $data['heure_fin']) {
            $this->flash('error', 'L’heure de fin doit être supérieure à l’heure de début.');
            Router::redirect('emplois-du-temps');
        }

        $conflicts = $this->detectConflict($data);
        if (!empty($conflicts)) {
            $this->flash('error', 'Conflit d’emploi du temps : ' . $conflicts[0]);
            Router::redirect('emplois-du-temps');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO `enseignant_horaires` (`teacher_id`, `class_id`, `subject_id`, `jour`, `heure_debut`, `heure_fin`, `salle`, `school_year_id`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $data['teacher_id'],
            $data['class_id'],
            $data['subject_id'],
            $data['jour'],
            $data['heure_debut'],
            $data['heure_fin'],
            $data['salle'],
            $data['school_year_id'],
        ]);

        $this->flash('success', 'Emploi du temps enregistré.');
        Router::redirect('emplois-du-temps');
    }

    private function detectConflict(array $data): array {
        $errors = [];
        $stmt = $this->db->prepare(
            "SELECT * FROM `enseignant_horaires`
             WHERE `jour` = ?
               AND `school_year_id` = ?
               AND (
                   (`heure_debut` < ? AND `heure_fin` > ?) OR
                   (`heure_debut` < ? AND `heure_fin` > ?)
               )
               AND `status` = 1"
        );
        $stmt->execute([
            $data['jour'],
            $data['school_year_id'],
            $data['heure_fin'],
            $data['heure_debut'],
            $data['heure_fin'],
            $data['heure_debut'],
        ]);
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            if ((int) $row['teacher_id'] === (int) $data['teacher_id']) {
                $errors[] = 'cet enseignant est déjà affecté à une autre classe à cette heure.';
            }
            if ((int) $row['class_id'] === (int) $data['class_id']) {
                $errors[] = 'cette classe a déjà une autre matière à cette heure.';
            }
            if ($row['salle'] !== '' && trim((string) $data['salle']) !== '' && $row['salle'] === $data['salle']) {
                $errors[] = 'cette salle est déjà utilisée à cette heure.';
            }
        }

        return array_unique($errors);
    }
}
