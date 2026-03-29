<!-- app/views/export/index.php -->

<div class="row g-4">

    <!-- Export Élèves -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-people-fill me-1 text-primary"></i> Export Élèves
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Exporte la liste complète des élèves avec leurs informations personnelles,
                    classe et coordonnées du parent.
                </p>
                <form method="GET" action="<?= Router::url('export/eleves') ?>">
                    <div class="mb-3">
                        <label class="form-label">Filtrer par classe</label>
                        <select name="classe" class="form-select">
                            <option value="0">Toutes les classes</option>
                            <?php foreach ($classes as $cl): ?>
                            <option value="<?= $cl['id'] ?>">
                                <?= htmlspecialchars($cl['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-download me-1"></i> Télécharger CSV
                    </button>
                </form>
            </div>
            <div class="card-footer text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Colonnes : Matricule, Nom, Prénom, Naissance, Sexe, Classe, Parent
            </div>
        </div>
    </div>

    <!-- Export Notes -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pencil-square me-1 text-success"></i> Export Notes & Moyennes
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Exporte les moyennes par matière, moyenne générale, mention et rang
                    pour une classe et un trimestre donnés.
                </p>
                <form method="GET" action="<?= Router::url('export/notes') ?>">
                    <div class="mb-3">
                        <label class="form-label">Classe <span class="text-danger">*</span></label>
                        <select name="classe" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($classes as $cl): ?>
                            <option value="<?= $cl['id'] ?>">
                                <?= htmlspecialchars($cl['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trimestre</label>
                        <select name="periode" class="form-select">
                            <option value="1">1er Trimestre</option>
                            <option value="2">2ème Trimestre</option>
                            <option value="3">3ème Trimestre</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-download me-1"></i> Télécharger CSV
                    </button>
                </form>
            </div>
            <div class="card-footer text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Colonnes : Élève, Moyenne/matière, Moy. générale, Mention, Rang
            </div>
        </div>
    </div>

    <!-- Export Paiements -->
    <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-cash-coin me-1 text-warning"></i> Export Paiements
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Exporte tous les paiements de l'année scolaire active avec les montants en FCFA,
                    modes de paiement et statuts.
                </p>
                <?php if ($annee): ?>
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-calendar3 me-1"></i>
                    Année active : <strong><?= htmlspecialchars($annee['libelle']) ?></strong>
                </div>
                <?php endif; ?>
                <a href="<?= Router::url('export/paiements') ?>" class="btn btn-warning w-100">
                    <i class="bi bi-download me-1"></i> Télécharger CSV
                </a>
            </div>
            <div class="card-footer text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Colonnes : Reçu, Élève, Classe, Montant, Date, Mode, Statut
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Guide d'utilisation -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-question-circle me-1 text-primary"></i> Comment ouvrir le fichier CSV dans Excel ?
    </div>
    <div class="card-body">
        <ol class="mb-0 small">
            <li class="mb-1">Téléchargez le fichier CSV.</li>
            <li class="mb-1">Ouvrez Excel → <strong>Fichier → Ouvrir</strong> → sélectionnez le fichier.</li>
            <li class="mb-1">Dans l'assistant d'importation, choisissez <strong>délimité par point-virgule (;)</strong>.</li>
            <li class="mb-1">Encodage : <strong>UTF-8</strong>.</li>
            <li>Cliquez <strong>Terminer</strong> — les données s'affichent correctement avec les accents.</li>
        </ol>
    </div>
</div>
