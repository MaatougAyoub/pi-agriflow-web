import './bootstrap.js';
<<<<<<< HEAD
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
=======
import './styles/app.css';

const EYE_ICON = `
<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
	<path d="M1.5 12s3.8-6.5 10.5-6.5S22.5 12 22.5 12 18.7 18.5 12 18.5 1.5 12 1.5 12Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
	<circle cx="12" cy="12" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
</svg>`;

const EYE_OFF_ICON = `
<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
	<path d="M3 3l18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
	<path d="M10.6 6.1A10.7 10.7 0 0 1 12 5.5c6.7 0 10.5 6.5 10.5 6.5a17 17 0 0 1-3.8 4.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
	<path d="M6.2 7.7A17.7 17.7 0 0 0 1.5 12S5.3 18.5 12 18.5c1.7 0 3.2-.4 4.5-1.1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
	<path d="M14.4 14.4A3.2 3.2 0 0 1 9.6 9.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>`;

function enhancePasswordInput(input) {
	if (input.dataset.passwordToggleReady === '1') {
		return;
	}

	const wrapper = document.createElement('div');
	wrapper.className = 'agf-password-wrapper';

	const parent = input.parentNode;
	if (!parent) {
		return;
	}

	parent.insertBefore(wrapper, input);
	wrapper.appendChild(input);

	input.classList.add('agf-password-input');
	input.dataset.passwordToggleReady = '1';

	const toggle = document.createElement('button');
	toggle.type = 'button';
	toggle.className = 'agf-password-toggle';
	toggle.setAttribute('aria-label', 'Afficher le mot de passe');
	toggle.setAttribute('aria-pressed', 'false');
	toggle.title = 'Afficher / masquer le mot de passe';
	toggle.innerHTML = EYE_ICON;

	toggle.addEventListener('click', () => {
		const showPassword = input.type === 'password';
		input.type = showPassword ? 'text' : 'password';

		toggle.setAttribute('aria-pressed', String(showPassword));
		toggle.setAttribute('aria-label', showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
		toggle.innerHTML = showPassword ? EYE_OFF_ICON : EYE_ICON;
	});

	wrapper.appendChild(toggle);
}

function initPasswordToggles() {
	const passwordInputs = document.querySelectorAll('input[type="password"]');
	passwordInputs.forEach(enhancePasswordInput);
}

document.addEventListener('DOMContentLoaded', initPasswordToggles);
document.addEventListener('turbo:load', initPasswordToggles);
>>>>>>> 2e5d736b7c09004bb95e82e33ea9ef850dfb84eb
