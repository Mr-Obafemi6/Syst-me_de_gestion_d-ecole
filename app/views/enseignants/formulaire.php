<?php
$teacher = $teacher ?? [];
$errors = $errors ?? [];
$csrf_token = (string) ($csrf_token ?? '');
?>
<div class="card">
    <div class="card-header">Informations enseignant</div>
    <div class="card-body">
        <form method="POST" action="<?= Router::url('enseignants/save') ?>" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="user_id" value="<?= (int) ($teacher['user_id'] ?? 0) ?>">

            <div class="col-md-4">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($teacher['nom'] ?? '') ?>" required>
                <?php if (!empty($errors['nom'])): ?><small class="text-danger"><?= htmlspecialchars($errors['nom']) ?></small><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($teacher['prenom'] ?? '') ?>" required>
                <?php if (!empty($errors['prenom'])): ?><small class="text-danger"><?= htmlspecialchars($errors['prenom']) ?></small><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sexe</label>
                <select name="sexe" class="form-select">
                    <option value="M" <?= (($teacher['sexe'] ?? 'M') === 'M') ? 'selected' : '' ?>>Masculin</option>
                    <option value="F" <?= (($teacher['sexe'] ?? 'M') === 'F') ? 'selected' : '' ?>>Féminin</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($teacher['date_naissance'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Lieu de naissance</label>
                <input type="text" name="lieu_naissance" class="form-control" value="<?= htmlspecialchars($teacher['lieu_naissance'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Nationalité</label>
                <input type="text" name="nationalite" class="form-control" value="<?= htmlspecialchars($teacher['nationalite'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Téléphone</label>
                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($teacher['telephone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($teacher['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Matricule</label>
                <input type="text" name="matricule" class="form-control" value="<?= htmlspecialchars($teacher['matricule'] ?? '') ?>" required>
                <?php if (!empty($errors['matricule'])): ?><small class="text-danger"><?= htmlspecialchars($errors['matricule']) ?></small><?php endif; ?>
            </div>

            <div class="col-12">
                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control" rows="2"><?= htmlspecialchars($teacher['adresse'] ?? '') ?></textarea>
            </div>

            <div class="col-md-3">
                <label class="form-label">Niveau d’étude</label>
                <input type="text" name="niveau_etude" class="form-control" value="<?= htmlspecialchars($teacher['niveau_etude'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Spécialité</label>
                <input type="text" name="specialite" class="form-control" value="<?= htmlspecialchars($teacher['specialite'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Diplôme</label>
                <input type="text" name="diplome" class="form-control" value="<?= htmlspecialchars($teacher['diplome'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Expérience</label>
                <input type="text" name="experience" class="form-control" value="<?= htmlspecialchars($teacher['experience'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Date de recrutement</label>
                <input type="date" name="date_recrutement" class="form-control" value="<?= htmlspecialchars($teacher['date_recrutement'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut professionnel</label>
                <input type="text" name="statut_professionnel" class="form-control" value="<?= htmlspecialchars($teacher['statut_professionnel'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Type de contrat</label>
                <input type="text" name="type_contrat" class="form-control" value="<?= htmlspecialchars($teacher['type_contrat'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Téléphone professionnel</label>
                <input type="tel" name="telephone_professionnel" class="form-control" value="<?= htmlspecialchars($teacher['telephone_professionnel'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Email professionnel</label>
                <input type="email" name="email_professionnel" class="form-control" value="<?= htmlspecialchars($teacher['email_professionnel'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Photo</label>
                <input type="text" name="photo" class="form-control" value="<?= htmlspecialchars($teacher['photo'] ?? '') ?>">
            </div>

            <div class="col-12 text-end">
                <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>
