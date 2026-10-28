@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const projetSelect = document.getElementById(@json($projectSelectId));
    const stagiaireSelect = document.getElementById(@json($stagiaireSelectId));
    const endpoint = @json(route('mentor.tasks.getStagiairesDisponibles'));
    let requete;

    projetSelect.addEventListener('change', async function () {
        requete?.abort();
        const courante = new AbortController();
        requete = courante;
        const selection = stagiaireSelect.value;
        stagiaireSelect.disabled = true;
        stagiaireSelect.replaceChildren(new Option('Chargement des stagiaires…', ''));
        try {
            const url = this.value ? endpoint + '?project_id=' + encodeURIComponent(this.value) : endpoint;
            const response = await fetch(url, {headers: {'Accept': 'application/json'}, signal: courante.signal});
            if (!response.ok) throw new Error('Chargement impossible');
            const stagiaires = await response.json();
            if (requete !== courante) return;
            stagiaireSelect.replaceChildren(new Option(stagiaires.length ? 'Sélectionner un stagiaire' : 'Aucun stagiaire disponible', ''));
            stagiaires.forEach(stagiaire => stagiaireSelect.add(new Option(stagiaire.nom + ' — ' + stagiaire.matricule, stagiaire.id)));
            if (stagiaires.some(stagiaire => String(stagiaire.id) === selection)) stagiaireSelect.value = selection;
        } catch (error) {
            if (error.name === 'AbortError' || requete !== courante) return;
            stagiaireSelect.replaceChildren(new Option('Chargement impossible : sélectionnez à nouveau le projet', ''));
        } finally {
            if (requete === courante) stagiaireSelect.disabled = false;
        }
    });
});
</script>
@endpush
