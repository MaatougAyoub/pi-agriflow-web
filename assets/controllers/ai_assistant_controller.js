import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'trigger',
        'status',
        'titreField',
        'descriptionField',
        'categorieField',
        'unitePrixField',
        'localisationField',
        'typeField',
    ];

    static values = {
        url: String,
    };

    async generate(event) {
        event.preventDefault();

        if (!this.hasUrlValue || !this.urlValue) {
            this.updateStatus('Assistant indisponible : route non configurée.', 'error');
            return;
        }

        this.triggerTarget.disabled = true;
        this.updateStatus('Génération en cours...', 'loading');

        try {
            const response = await fetch(this.urlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    titre: this.hasTitreFieldTarget ? this.titreFieldTarget.value : '',
                    description: this.hasDescriptionFieldTarget ? this.descriptionFieldTarget.value : '',
                    categorie: this.hasCategorieFieldTarget ? this.categorieFieldTarget.value : '',
                    unitePrix: this.hasUnitePrixFieldTarget ? this.unitePrixFieldTarget.value : '',
                    localisation: this.hasLocalisationFieldTarget ? this.localisationFieldTarget.value : '',
                    type: this.hasTypeFieldTarget ? this.typeFieldTarget.value : '',
                }),
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Assistant indisponible pour le moment.');
            }

            this.applySuggestion(this.titreFieldTarget, payload.suggestions.titre);
            this.applySuggestion(this.descriptionFieldTarget, payload.suggestions.description);
            this.applySuggestion(this.categorieFieldTarget, payload.suggestions.categorie);
            this.applySuggestion(this.unitePrixFieldTarget, payload.suggestions.unitePrix);

            const provider = payload.suggestions.provider ? ` (${payload.suggestions.provider})` : '';
            this.updateStatus(`Suggestions appliquées${provider}. Vous pouvez encore les modifier avant l'enregistrement.`, 'success');
        } catch (error) {
            this.updateStatus(error.message || 'Assistant indisponible pour le moment.', 'error');
        } finally {
            this.triggerTarget.disabled = false;
        }
    }

    applySuggestion(field, value) {
        if (!field || typeof value !== 'string' || value.trim() === '') {
            return;
        }

        field.value = value.trim();
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
    }

    updateStatus(message, state) {
        if (!this.hasStatusTarget) {
            return;
        }

        this.statusTarget.textContent = message;
        this.statusTarget.dataset.state = state;
    }
}
