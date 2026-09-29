<?php
require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class AffectationController extends Controller {
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('assignments.view');
        $db = Database::getConnection();
        $assignments = $db->query("SELECT ta.*, u.nom AS enseignant_nom, u.prenom AS enseignant_prenom, c.nom AS classe_nom, n.nom AS niveau_nom, m.nom AS matiere_nom, a.libelle AS annee FROM teacher_assignments ta JOIN users u ON u.id=ta.teacher_id JOIN classes c ON c.id=ta.class_id JOIN niveaux n ON n.id=c.niveau_id JOIN matieres m ON m.id=ta.subject_id JOIN annees_scolaires a ON a.id=ta.school_year_id ORDER BY u.nom,u.prenom,n.ordre,m.nom")->fetchAll();
        $teachers = $db->query("SELECT id,nom,prenom FROM users WHERE role='professeur' AND actif=1 ORDER BY nom,prenom")->fetchAll();
        $classes = (new Classe())->toutesLesClasses();
        $subjects = $db->query("SELECT id,nom,code FROM matieres WHERE statut=1 ORDER BY nom")->fetchAll();
        $years = (new Classe())->toutesLesAnnees();
        $this->render('affectations/index', ['title'=>'Affectations enseignants','pageTitle'=>'Affectations enseignants','user'=>AuthMiddleware::user(),'flash'=>$this->getFlash(),'csrf_token'=>$this->generateCsrfToken(),'assignments'=>$assignments,'teachers'=>$teachers,'classes'=>$classes,'subjects'=>$subjects,'years'=>$years]);
    }

    public function save(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('assignments.manage');
        $this->requireMethod('POST');
        $this->validateCsrf();
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO teacher_assignments (teacher_id,class_id,subject_id,school_year_id,status) VALUES (?,?,?,?,1) ON DUPLICATE KEY UPDATE status=1");
        $stmt->execute([(int)$this->post('teacher_id'), (int)$this->post('class_id'), (int)$this->post('subject_id'), (int)$this->post('school_year_id')]);
        $this->flash('success', 'Affectation enregistrée.');
        Router::redirect('affectations');
    }

    public function toggle(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('assignments.manage');
        $this->requireMethod('POST');
        $this->validateCsrf();
        $db = Database::getConnection();
        $db->prepare("UPDATE teacher_assignments SET status=IF(status=1,0,1) WHERE id=?")->execute([(int)$param]);
        Router::redirect('affectations');
    }
}
