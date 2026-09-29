<?php
$csrf_token = (string) ($csrf_token ?? '');
$rows = $rows ?? [];
$teachers = $teachers ?? [];
$classes = $classes ?? [];
$subjects = $subjects ?? [];
$teacherId = (int) ($teacherId ?? 0);
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-primary mb-1">Emploi du temps</h4>
        <div class="text-muted">Planification des cours et vérification des conflits</div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Nouvel horaire</div>
    <div class="card-body">
        <form method="POST" action="<?= Router::url('emplois-du-temps/save') ?>" class="row g-3 align-items-end">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="col-md-3">
                <label class="form-label">Enseignant</label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">Choisir</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= (int) $teacher['id'] ?>" <?= (int) $teacher['id'] === $teacherId ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($teacher['prenom'] ?? '') . ' ' . ($teacher['nom'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Classe</label>
                <select name="class_id" class="form-select" required>
                    <option value="">Choisir</option>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?= (int) $classe['id'] ?>"><?= htmlspecialchars($classe['libelle_complete'] ?? $classe['nom'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Matière</label>
                <select name="subject_id" class="form-select" required>
                    <option value="">Choisir</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= (int) $subject['id'] ?>"><?= htmlspecialchars($subject['nom'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jour</label>
                <select name="jour" class="form-select" required>
                    <option value="">Choisir</option>
                    <option value="Lundi">Lundi</option>
                    <option value="Mardi">Mardi</option>
                    <option value="Mercredi">Mercredi</option>
                    <option value="Jeudi">Jeudi</option>
                    <option value="Vendredi">Vendredi</option>
                    <option value="Samedi">Samedi</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Salle</label>
                <input type="text" name="salle" class="form-control" placeholder="Ex: A101">
            </div>
            <div class="col-md-2">
                <label class="form-label">Heure début</label>
                <input type="time" name="heure_debut" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Heure fin</label>
                <input type="time" name="heure_fin" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Année</label>
                <select name="school_year_id" class="form-select" required>
                    <option value="">Choisir</option>
                    <?php foreach (($years ?? []) as $year): ?>
                        <option value="<?= (int) $year['id'] ?>" <?= !empty($year['active']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($year['libelle'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Enseignant</th>
                    <th>Classe</th>
                    <th>Matière</th>
                    <th>Jour</th>
                    <th>Horaire</th>
                    <th>Salle</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Aucun emploi du temps enregistré.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars(($row['enseignant_prenom'] ?? '') . ' ' . ($row['enseignant_nom'] ?? '')) ?></td>
                            <td><?= htmlspecialchars(($row['niveau_nom'] ?? '') . ' ' . ($row['classe_nom'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($row['matiere_nom'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['jour'] ?? '') ?></td>
                            <td><?= htmlspecialchars(($row['heure_debut'] ?? '') . ' - ' . ($row['heure_fin'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($row['salle'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
