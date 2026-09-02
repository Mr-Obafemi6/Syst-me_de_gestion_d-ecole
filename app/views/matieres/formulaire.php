<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>
                <?= !empty($matiere['id']) ? 'Modifier la matière' : 'Ajouter une matière au catalogue' ?>
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

                <form method="POST" action="<?= !empty($matiere['id']) ? Router::url('matieres/update/' . $matiere['id']) : Router::url('matieres/store') ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom de la matière <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($matiere['nom'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" value="<?= htmlspecialchars($matiere['code'] ?? '') ?>" placeholder="FR">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Coefficient</label>
                            <input type="number" class="form-control" name="coefficient" min="0.5" max="20" step="0.5" value="<?= htmlspecialchars((string) ($matiere['coefficient'] ?? 1)) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($matiere['description'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="statut">
                                <option value="1" <?= (($matiere['statut'] ?? 1) == 1) ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= (($matiere['statut'] ?? 1) == 0) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Professeur titulaire</label>
                            <select class="form-select" name="prof_id">
                                <option value="">-- Aucun --</option>
                                <?php foreach (($profs ?? []) as $prof): ?>
                                    <option value="<?= (int) $prof['id'] ?>" <?= ((int) ($matiere['prof_id'] ?? 0)) === (int) $prof['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prof['prenom'] . ' ' . $prof['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Classes concernées</label>
                            <div class="row g-2">
                                <?php foreach ($classes as $classe): ?>
                                    <div class="col-md-4">
                                        <div class="form-check border rounded p-2">
                                            <input class="form-check-input" type="checkbox" name="classes[]" value="<?= (int) $classe['id'] ?>" id="classe-<?= (int) $classe['id'] ?>">
                                            <label class="form-check-label" for="classe-<?= (int) $classe['id'] ?>"><?= htmlspecialchars($classe['libelle_complete'] ?? $classe['nom']) ?></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            <?= !empty($matiere['id']) ? 'Enregistrer' : 'Créer la matière' ?>
                        </button>
                        <a href="<?= Router::url('matieres') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x me-1"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
