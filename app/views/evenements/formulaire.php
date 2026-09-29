<!-- app/views/evenements/formulaire.php -->

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-calendar-event text-primary fs-5"></i>
        Nouvel événement
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

        <form method="POST" action="<?= Router::url('evenements/store') ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                <input type="text" name="titre" class="form-control"
                       value="<?= htmlspecialchars($data['titre'] ?? '') ?>"
                       placeholder="Ex: Conseil de classe — 3ème A" required>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date et heure <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="date_debut" class="form-control"
                           value="<?= htmlspecialchars($data['date_debut'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type</label>
                    <select name="type" class="form-select">
                        <option value="reunion" <?= ($data['type'] ?? '') === 'reunion' ? 'selected' : '' ?>>Réunion</option>
                        <option value="sortie"  <?= ($data['type'] ?? '') === 'sortie'  ? 'selected' : '' ?>>Sortie pédagogique</option>
                        <option value="examen"  <?= ($data['type'] ?? '') === 'examen'  ? 'selected' : '' ?>>Examen</option>
                        <option value="conseil" <?= ($data['type'] ?? '') === 'conseil' ? 'selected' : '' ?>>Conseil de classe</option>
                        <option value="autre"   <?= ($data['type'] ?? '') === 'autre'   ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Classe concernée</label>
                    <select name="classe_id" class="form-select">
                        <option value="">-- Toutes les classes --</option>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($data['classe_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Description (optionnel)</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Détails, lieu, informations complémentaires..."><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Photo de l'événement (optionnel)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <small class="text-muted">Formats acceptés : JPG, PNG, WEBP (max 2 Mo)</small>
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"
                        data-loading-message="Ajout de l'événement en cours..."
                        data-loading-label="Ajout en cours...">
                    <i class="bi bi-check-circle me-1"></i> Ajouter à l'agenda
                </button>
                <a href="<?= Router::url('evenements') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
