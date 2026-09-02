<!-- app/views/notes/saisie.php -->

<!-- Sélecteurs -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Classe</label>
                <select id="sel-classe" class="form-select">
                    <option value="">-- Choisir une classe --</option>
                    <?php foreach ($classes as $cl): ?>
                    <option value="<?= $cl['id'] ?>" <?= $classeId == $cl['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cl['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Matière</label>
                <select id="sel-matiere" class="form-select" disabled>
                    <option value="">-- Choisir d'abord une classe --</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Période</label>
                <select id="sel-periode" class="form-select">
                    <option value="1" <?= $periode == 1 ? 'selected' : '' ?>>1er Trimestre</option>
                    <option value="2" <?= $periode == 2 ? 'selected' : '' ?>>2ème Trimestre</option>
                    <option value="3" <?= $periode == 3 ? 'selected' : '' ?>>3ème Trimestre</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Type d'éval.</label>
                <select id="sel-type" class="form-select">
                    <option value="devoir">Devoir</option>
                    <option value="composition">Composition</option>
                    <option value="examen">Examen</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Date</label>
                <input type="date" id="sel-date" class="form-control"
                       value="<?= date('Y-m-d') ?>">
            </div>
        </div>
    </div>
</div>

<!-- Zone de saisie des notes -->
<div id="zone-notes">
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-pencil-square fs-1"></i>
            <p class="mt-3">Sélectionnez une classe et une matière pour saisir les notes.</p>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// ─── Chargement des matières quand on change de classe ───
document.getElementById('sel-classe').addEventListener('change', async function() {
    const classeId = this.value;
    const selMatiere = document.getElementById('sel-matiere');

    selMatiere.innerHTML = '<option value="">Chargement...</option>';
    selMatiere.disabled = true;
    document.getElementById('zone-notes').innerHTML = zonePlaceholder('Choisissez une matière.');

    if (!classeId) return;

    try {
        const data = await fetchJSON(`${BASE_URL}/notes/apiMatieres?classe_id=${classeId}`);
        selMatiere.innerHTML = '<option value="">-- Choisir une matière --</option>';
        data.matieres.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = `${m.nom} (coef. ${m.coefficient})`;
            selMatiere.appendChild(opt);
        });
        selMatiere.disabled = false;
    } catch(e) {
        selMatiere.innerHTML = '<option>Erreur de chargement</option>';
    }
});

// ─── Chargement des élèves quand on change de matière/période ───
['sel-matiere', 'sel-periode'].forEach(id => {
    document.getElementById(id).addEventListener('change', chargerEleves);
});

async function chargerEleves() {
    const classeId  = document.getElementById('sel-classe').value;
    const matiereId = document.getElementById('sel-matiere').value;
    const periode   = document.getElementById('sel-periode').value;

    if (!classeId || !matiereId) return;

    document.getElementById('zone-notes').innerHTML = zonePlaceholder('Chargement des élèves...');

    try {
        const data = await fetchJSON(
            `${BASE_URL}/notes/apiEleves?classe_id=${classeId}&matiere_id=${matiereId}&periode=${periode}`
        );
        afficherTableauNotes(data.eleves, matiereId, periode);
    } catch(e) {
        document.getElementById('zone-notes').innerHTML = zonePlaceholder('Erreur de chargement.');
    }
}

// ─── Affichage du tableau de saisie ───
function afficherTableauNotes(eleves, matiereId, periode) {
    if (!eleves.length) {
        document.getElementById('zone-notes').innerHTML = zonePlaceholder('Aucun élève dans cette classe.');
        return;
    }

    let html = `
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-pencil-square me-1 text-primary"></i>
            Saisie des notes — ${eleves.length} élève${eleves.length > 1 ? 's' : ''}</span>
            <div id="statut-sauvegarde" class="text-muted small"></div>
        </div>
        <div class="table-responsive">
        <table class="table table-hover mb-0" id="table-notes">
            <thead>
                <tr>
                    <th style="width:40%">Élève</th>
                    <th class="text-center">Notes existantes</th>
                    <th style="width:180px">Nouvelle note /20</th>
                    <th class="text-center">Moyenne</th>
                </tr>
            </thead>
            <tbody>`;

    eleves.forEach(eleve => {
        const notesHTML = eleve.notes && eleve.notes.length
            ? eleve.notes.map(n => `
                <span class="badge bg-secondary me-1" style="cursor:pointer"
                      title="${n.type_eval} — ${n.date_eval}"
                      data-note-id="${n.id}" onclick="supprimerNote(${n.id}, this)">
                    ${n.note}/20 <i class="bi bi-x"></i>
                </span>`).join('')
            : '<span class="text-muted small">Aucune note</span>';

        const moy = eleve.moyenne !== null
            ? `<span class="fw-bold ${couleurMoyenne(eleve.moyenne)}">${eleve.moyenne}/20</span>`
            : '<span class="text-muted">—</span>';

        html += `
            <tr id="row-${eleve.id}">
                <td>
                    <div class="fw-semibold">${eleve.nom} ${eleve.prenom}</div>
                    <div class="text-muted small">${eleve.matricule}</div>
                </td>
                <td id="notes-${eleve.id}">${notesHTML}</td>
                <td>
                    <div class="d-flex gap-1">
                        <input type="number" class="form-control form-control-sm"
                               id="note-${eleve.id}"
                               min="0" max="20" step="0.25"
                               placeholder="0 — 20"
                               onkeydown="if(event.key==='Enter') sauvegarderNote(${eleve.id}, ${matiereId}, ${periode})">
                        <button class="btn btn-sm btn-success"
                                onclick="sauvegarderNote(${eleve.id}, ${matiereId}, ${periode})"
                                title="Enregistrer">
                            <i class="bi bi-check"></i>
                        </button>
                    </div>
                </td>
                <td class="text-center" id="moy-${eleve.id}">${moy}</td>
            </tr>`;
    });

    html += `</tbody></table></div></div>`;
    document.getElementById('zone-notes').innerHTML = html;
}

// ─── Sauvegarder une note via API ───
async function sauvegarderNote(eleveId, matiereId, periode) {
    const input    = document.getElementById(`note-${eleveId}`);
    const note     = parseFloat(input.value);
    const typeEval = document.getElementById('sel-type').value;
    const dateEval = document.getElementById('sel-date').value;
    const statut   = document.getElementById('statut-sauvegarde');

    if (isNaN(note) || note < 0 || note > 20) {
        input.classList.add('is-invalid');
        setTimeout(() => input.classList.remove('is-invalid'), 2000);
        return;
    }

    input.disabled = true;
    statut.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split me-1"></i>Enregistrement...</span>';

    try {
        const data = await fetchJSON(`${BASE_URL}/notes/apiSauvegarder`, {
            method: 'POST',
            body: JSON.stringify({
                eleve_id:    eleveId,
                matiere_id:  matiereId,
                note:        note,
                type_eval:   typeEval,
                periode:     parseInt(periode),
                date_eval:   dateEval,
                commentaire: ''
            })
        });

        // Ajouter le badge de la nouvelle note
        const cellNotes = document.getElementById(`notes-${eleveId}`);
        const oldBadges = cellNotes.querySelector('.text-muted');
        if (oldBadges) cellNotes.innerHTML = '';

        const badge = document.createElement('span');
        badge.className = 'badge bg-secondary me-1';
        badge.style.cursor = 'pointer';
        badge.title = `${typeEval} — ${dateEval}`;
        badge.setAttribute('data-note-id', data.note_id);
        badge.innerHTML = `${note}/20 <i class="bi bi-x"></i>`;
        badge.onclick = () => supprimerNote(data.note_id, badge);
        cellNotes.appendChild(badge);

        // Mettre à jour la moyenne
        if (data.moyenne !== null) {
            document.getElementById(`moy-${eleveId}`).innerHTML =
                `<span class="fw-bold ${couleurMoyenne(data.moyenne)}">${data.moyenne}/20</span>`;
        }

        input.value = '';
        input.disabled = false;
        input.focus();

        statut.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Note enregistrée</span>';
        setTimeout(() => statut.innerHTML = '', 2000);

    } catch(e) {
        input.disabled = false;
        statut.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Erreur</span>';
    }
}

// ─── Supprimer une note ───
async function supprimerNote(noteId, badge) {
    if (!confirm('Supprimer cette note ?')) return;

    try {
        await fetchJSON(`${BASE_URL}/notes/apiSupprimer/${noteId}`, { method: 'POST' });
        badge.remove();
        showToast('Note supprimée.', 'warning');
        // Recharger pour recalculer les moyennes
        chargerEleves();
    } catch(e) {
        showToast('Erreur lors de la suppression.', 'danger');
    }
}

// ─── Helpers ───
function couleurMoyenne(moy) {
    if (moy >= 14) return 'text-success';
    if (moy >= 10) return 'text-primary';
    if (moy >= 8)  return 'text-warning';
    return 'text-danger';
}

function zonePlaceholder(msg) {
    return `<div class="card"><div class="card-body text-center text-muted py-5">
        <i class="bi bi-pencil-square fs-1"></i><p class="mt-3">${msg}</p></div></div>`;
}

// Charger au démarrage si classe déjà sélectionnée
<?php if ($classeId > 0): ?>
document.getElementById('sel-classe').dispatchEvent(new Event('change'));
<?php endif; ?>
</script>
