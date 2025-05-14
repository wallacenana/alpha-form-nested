console.log("Alpha Forms iniciado");

document.addEventListener('DOMContentLoaded', () => {
	initAlphaForm();
	initAlphaNavigation();
});

function markRequiredFields() {
	const forms = document.querySelectorAll('.widget-alpha-form-n');

	forms.forEach(widget => {
		const form = widget.querySelector('form[data-show-required="yes"]');
		if (!form) return;

		const titles = form.querySelectorAll('.alpha-form-titulo');

		titles.forEach(title => {
			const container = title.closest('.alpha-form-field') || title.closest('.alpha-inputs');
			if (!container) return;

			const requiredInput = container.querySelector('input[required], select[required], textarea[required]');
			if (!requiredInput) return;

			if (title.classList.contains('alpha-required-injected')) return;

			const mark = document.createElement('span');
			mark.textContent = ' *';
			mark.style.color = '#ff0000';
			mark.style.marginLeft = '4px';
			mark.classList.add('alpha-required-mark');

			title.appendChild(mark);
			title.classList.add('alpha-required-injected');
		});
	});
}

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

function getNextField(current, form) {
	const fields = Array.from(form.querySelectorAll('.alpha-form-field'));
	const index = fields.indexOf(current);
	for (let i = index + 1; i < fields.length; i++) {
		const input = fields[i].querySelector('input, select, textarea');
		if (!input || input.type !== 'hidden') return fields[i];
	}
	return null;
}

function getPrevField(current, form) {
	const fields = Array.from(form.querySelectorAll('.alpha-form-field'));
	const index = fields.indexOf(current);
	for (let i = index - 1; i >= 0; i--) {
		const input = fields[i].querySelector('input, select, textarea');
		if (!input || input.type !== 'hidden') return fields[i];
	}
	return null;
}

function goToNextField(formId) {
	const form = document.querySelector(`.widget-alpha-form-n[data-id="${formId}"]`);
	if (!form) return;

	const current = form.querySelector('.alpha-form-field.active');
	if (!current) return;

	// Validação
	if (!isValid(current)) {
		const errorMessage = current.querySelector('.alpha-error-message');
		if (errorMessage) {
			errorMessage.style.display = 'block';
		}
		return;
	} else {
		const errorMessage = current.querySelector('.alpha-error-message');
		if (errorMessage) {
			errorMessage.style.display = 'none';
		}
	}

	const next = getNextField(current, form);
	if (next) {
		current.classList.remove('active');
		next.classList.add('active');
		const input = next.querySelector('input:not([type="hidden"]), select, textarea');
		if (input) input.focus();
	}
}


function goToPrevField(formId) {
	const form = document.querySelector(`.widget-alpha-form-n[data-id="${formId}"]`);
	if (!form) return;

	const current = form.querySelector('.alpha-form-field.active');
	if (!current) return;

	const prev = getPrevField(current, form);
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
		let formId = btn.dataset.aFTarget || btn.dataset.a_f_target;

		// 🧠 Caso não tenha target explícito, tenta descobrir via DOM
		if (!formId) {
			const wrapper = btn.closest('.widget-alpha-form-n[data-id]');
			if (wrapper) {
				formId = wrapper.getAttribute('data-id');
			}
		}

		if (!formId) return;

		if (action === 'next') goToNextField(formId);
		if (action === 'prev') goToPrevField(formId);
	});
}


// Chama a função no carregamento
window.addEventListener('DOMContentLoaded', markRequiredFields);