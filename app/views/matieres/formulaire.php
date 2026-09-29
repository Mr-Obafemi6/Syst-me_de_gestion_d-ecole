<?php $matiere = $matiere ?? []; $errors = $errors ?? []; $classes = $classes ?? []; $profs = $profs ?? []; $csrf_token = (string) ($csrf_token ?? ''); ?>
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
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"
                                data-loading-message="<?= !empty($matiere['id']) ? 'Modification de la matière en cours...' : 'Création de la matière en cours...' ?>"
                                data-loading-label="<?= !empty($matiere['id']) ? 'Modification...' : 'Création...' ?>">
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
