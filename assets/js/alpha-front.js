console.log("Alpha Forms iniciado");

document.addEventListener('DOMContentLoaded', () => {
	initAlphaForm();
	initAlphaNavigation();
});

function initAlphaForm() {
	const fields = Array.from(document.querySelectorAll('.alpha-form-field'));
	if (!fields.length) return;

	const first = fields.find(f => {
		const input = f.querySelector('input, select, textarea');
		return !input || input.type !== 'hidden';
	});

	if (first) {
		first.classList.add('active');
		const input = first.querySelector('input:not([type="hidden"]), select, textarea');
		if (input) input.focus();
	}
}

function isValid(field) {
	const input = field.querySelector('input, select, textarea');
	if (!input) return true;
	if (input.type === 'hidden') return true;

	if (input.hasAttribute('required')) {
		if (input.type === 'email') {
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			return emailRegex.test(input.value);
		}
		if (input.hasAttribute('pattern')) {
			const pattern = new RegExp(input.getAttribute('pattern'));
			return pattern.test(input.value);
		}
		return !!input.value.trim();
	}
	return true;
}

function getNextField(current) {
	const fields = Array.from(document.querySelectorAll('.alpha-form-field'));
	const index = fields.indexOf(current);
	for (let i = index + 1; i < fields.length; i++) {
		const input = fields[i].querySelector('input, select, textarea');
		if (!input || input.type !== 'hidden') return fields[i];
	}
	return null;
}

function getPrevField(current) {
	const fields = Array.from(document.querySelectorAll('.alpha-form-field'));
	const index = fields.indexOf(current);
	for (let i = index - 1; i >= 0; i--) {
		const input = fields[i].querySelector('input, select, textarea');
		if (!input || input.type !== 'hidden') return fields[i];
	}
	return null;
}

function goToNextField() {
	const current = document.querySelector('.alpha-form-field.active');
	if (!current || !isValid(current)) return;

	const next = getNextField(current);
	if (next) {
		current.classList.remove('active');
		next.classList.add('active');
		const input = next.querySelector('input:not([type="hidden"]), select, textarea');
		if (input) input.focus();
	}
}

function goToPrevField() {
	const current = document.querySelector('.alpha-form-field.active');
	if (!current) return;

	const prev = getPrevField(current);
	if (prev) {
		current.classList.remove('active');
		prev.classList.add('active');
		const input = prev.querySelector('input:not([type="hidden"]), select, textarea');
		if (input) input.focus();
	}
}

function initAlphaNavigation() {
	document.addEventListener('click', e => {
		const btn = e.target.closest('[data-alpha]');
		if (!btn) return;

		const action = btn.dataset.alpha;
		if (action === 'next') goToNextField();
		if (action === 'prev') goToPrevField();
	});
}
