<!-- app/views/absences/formulaire.php -->

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-calendar-x text-warning fs-5"></i>
        Enregistrer une absence
    </div>
    <div class="card-body">

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= Router::url('absences/store') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <!-- Sélection élève -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Élève <span class="text-danger">*</span></label>
                <?php if ($eleve): ?>
                <input type="hidden" name="eleve_id" value="<?= $eleve['id'] ?>">
                <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center"
                         style="width:42px;height:42px;font-weight:700">
                        <?= strtoupper(substr($eleve['prenom'],0,1) . substr($eleve['nom'],0,1)) ?>
                    </div>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($eleve['matricule']) ?> — <?= htmlspecialchars($eleve['classe_nom'] ?? '') ?></div>
                    </div>
                    <a href="<?= Router::url('absences/ajouter') ?>" class="btn btn-sm btn-outline-secondary ms-auto">
                        Changer
                    </a>
                </div>
                <?php else: ?>
                <!-- Sélectionner depuis la liste -->
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-1"></i>
                    Allez sur la <a href="<?= Router::url('eleves') ?>">fiche d'un élève</a>
                    puis cliquez <strong>"Absence"</strong> pour pré-sélectionner l'élève.
                </div>
                <select name="eleve_id" class="form-select" required>
                    <option value="">-- Sélectionner un élève --</option>
                    <?php
                    // Charger tous les élèves groupés par classe
                    $db = Database::getConnection();
                    $elevesAll = $db->query(
                        "SELECT e.id, e.nom, e.prenom, e.matricule, c.nom AS classe_nom
                         FROM `eleves` e
                         JOIN `classes` c ON c.id = e.classe_id
                         WHERE e.actif = 1
                         ORDER BY c.nom, e.nom, e.prenom"
                    )->fetchAll();
                    $currentClasse = '';
                    foreach ($elevesAll as $el):
                        if ($el['classe_nom'] !== $currentClasse):
                            if ($currentClasse !== '') echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($el['classe_nom']) . '">';
                            $currentClasse = $el['classe_nom'];
                        endif;
                    ?>
                    <option value="<?= $el['id'] ?>"
                        <?= ($data['eleve_id'] ?? 0) == $el['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($el['nom'] . ' ' . $el['prenom']) ?> — <?= htmlspecialchars($el['matricule']) ?>
                    </option>
                    <?php endforeach; ?>
                    <?php if ($currentClasse !== '') echo '</optgroup>'; ?>
                </select>
                <?php endif; ?>
            </div>

            <div class="row g-3">
                <!-- Date -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date_absence" class="form-control"
                           value="<?= htmlspecialchars($data['date_absence'] ?? date('Y-m-d')) ?>"
                           max="<?= date('Y-m-d') ?>" required>
                </div>

                <!-- Motif -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Motif</label>
                    <select name="motif" class="form-select">
                        <option value="non_justifie" <?= ($data['motif'] ?? '') === 'non_justifie' ? 'selected' : '' ?>>Non justifié</option>
                        <option value="maladie"      <?= ($data['motif'] ?? '') === 'maladie'      ? 'selected' : '' ?>>Maladie</option>
                        <option value="familial"     <?= ($data['motif'] ?? '') === 'familial'     ? 'selected' : '' ?>>Raison familiale</option>
                        <option value="autre"        <?= ($data['motif'] ?? '') === 'autre'        ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>

                <!-- Heure début -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Heure début</label>
                    <input type="time" name="heure_debut" class="form-control"
                           value="<?= htmlspecialchars($data['heure_debut'] ?? '') ?>">
                    <div class="form-text">Laisser vide = journée entière</div>
                </div>

                <!-- Heure fin -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Heure fin</label>
                    <input type="time" name="heure_fin" class="form-control"
                           value="<?= htmlspecialchars($data['heure_fin'] ?? '') ?>">
                </div>

                <!-- Justifiée -->
                <div class="col-md-4 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input type="checkbox" name="justifiee" value="1"
                               class="form-check-input" id="chkJustifiee"
                               <?= ($data['justifiee'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="chkJustifiee">
                            Absence justifiée
                        </label>
                    </div>
                </div>

                <!-- Commentaire -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Commentaire (optionnel)</label>
                    <input type="text" name="commentaire" class="form-control"
                           value="<?= htmlspecialchars($data['commentaire'] ?? '') ?>"
                           placeholder="Ex: Certificat médical fourni">
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-circle me-1"></i> Enregistrer l'absence
                </button>
                <a href="<?= Router::url('absences') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
