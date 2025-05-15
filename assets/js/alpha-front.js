console.log("Alpha Forms iniciado");

document.addEventListener('DOMContentLoaded', () => {
	initAlphaForm();
	initAlphaNavigation();
	initAlphaRadioNavigation();
	applyAlphaLetters();
	applyAlphaInputMasks()
});
function applyAlphaInputMasks() {
	document.querySelectorAll('input[data-mask]').forEach(input => {
		const mask = input.dataset.mask;

		// Remove qualquer máscara anterior
		input.removeEventListener('input', input._maskHandler);

		let handler;

		switch (mask) {
			case 'cpf':
				handler = e => {
					e.target.value = e.target.value
						.replace(/\D/g, '')
						.slice(0, 11) // CPF = 11 dígitos
						.replace(/(\d{3})(\d)/, '$1.$2')
						.replace(/(\d{3})(\d)/, '$1.$2')
						.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
				};
				break;

			case 'cnpj':
				handler = e => {
					e.target.value = e.target.value
						.replace(/\D/g, '')
						.slice(0, 14) // CNPJ = 14 dígitos
						.replace(/^(\d{2})(\d)/, '$1.$2')
						.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
						.replace(/\.(\d{3})(\d)/, '.$1/$2')
						.replace(/(\d{4})(\d)/, '$1-$2');
				};
				break;

			case 'cep':
				handler = e => {
					e.target.value = e.target.value
						.replace(/\D/g, '')
						.slice(0, 8) // CEP = 8 dígitos
						.replace(/^(\d{5})(\d)/, '$1-$2');
				};
				break;

			case 'currency':
				handler = e => {
					let val = e.target.value.replace(/\D/g, '').slice(0, 15); // limite opcional
					val = (parseInt(val || 0) / 100).toFixed(2) + '';
					val = val.replace('.', ',');
					val = val.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
					e.target.value = 'R$ ' + val;
				};
				break;

			case 'cel':
				handler = e => {
					e.target.value = e.target.value
						.replace(/\D/g, '')
						.slice(0, 11) // Celular: (XX) XXXXX-XXXX
						.replace(/(\d{2})(\d)/, '($1) $2')
						.replace(/(\d{5})(\d)/, '$1-$2')
						.replace(/(-\d{4})\d+?$/, '$1');
				};
				break;
		}

		if (handler) {
			input._maskHandler = handler;
			input.addEventListener('input', handler);
		}
	});
}

function applyAlphaLetters() {
	document.querySelectorAll('label[data-letter]').forEach(label => {
		// Evita duplicação
		if (label.classList.contains('alpha-letter-active')) return;

		const letter = label.getAttribute('data-letter');
		if (letter) {
			label.classList.add('alpha-letter-active');
			label.setAttribute('data-letter-display', letter);
		}
	});
}

function initAlphaNavigation() {
	document.addEventListener('click', e => {
		const btn = e.target.closest('[data-alpha]');
		if (!btn) return;

		const action = btn.dataset.alpha;
		let formId = btn.dataset.aFTarget || btn.dataset.a_f_target;

		if (!formId) {
			const wrapper = btn.closest('.widget-alpha-form-n[data-id]');
			if (wrapper) {
				formId = wrapper.getAttribute('data-id');
			}
		}

		if (!formId) return;

		const form = document.querySelector(`.widget-alpha-form-n[data-id="${formId}"]`);
		if (!form) return;

		const current = form.querySelector('.alpha-form-field.active');
		if (!current) return;

		if (action === 'next') {
			if (!isValid(current)) {
				toggleErrorMessage(current, true);
				return;
			}

			toggleErrorMessage(current, false);
			goToNextField(formId);
		}

		if (action === 'prev') {
			goToPrevField(formId);
		}
	});
}



function initAlphaRadioNavigation() {
	document.addEventListener('change', e => {
		const input = e.target;
		if (input.type !== 'radio') return;

		const wrapper = input.closest('.widget-alpha-form-n[data-id]');
		if (!wrapper) return;

		const formId = wrapper.getAttribute('data-id');
		const nextOffset = input.dataset.next ? parseInt(input.dataset.next) : 1;

		setTimeout(() => goToNextField(formId, nextOffset), 50);
	});
}

function markRequiredFields() {
	const forms = document.querySelectorAll('.widget-alpha-form-n');

	forms.forEach(widget => {
		const form = widget.querySelector('form[data-show-required="yes"]');
		if (!form) return;

		const titles = form.querySelectorAll('.alpha-form-titulo');

		titles.forEach(title => {
			const container = title.closest('.alpha-form-field') || title.closest('.alpha-inputs');
			if (!container) return;

			const requiredInput = container.querySelector('input[required], select[required], textarea[required], radio[required], checkbox[required]');
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

	if (!input.hasAttribute('required')) return true;

	const type = input.type;

	if (type === 'email') {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return emailRegex.test(input.value);
	}

	if (input.hasAttribute('pattern')) {
		const pattern = new RegExp(input.getAttribute('pattern'));
		return pattern.test(input.value);
	}

	if (type === 'radio') {
		const name = input.name;
		const group = field.querySelectorAll(`input[type="radio"][name="${name}"]`);
		return Array.from(group).some(r => r.checked);
	}

	if (type === 'checkbox') {
		const group = field.querySelectorAll('input[type="checkbox"]');
		return Array.from(group).some(c => c.checked);
	}

	return !!input.value.trim();
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

function toggleErrorMessage(field, show = true) {
	const errorMessage = field.querySelector('.alpha-error-message');
	if (errorMessage) {
		errorMessage.style.display = show ? 'block' : 'none';
	}
}

function goToNextField(formId, next = 1, absolute = false) {
	const form = document.querySelector(`.widget-alpha-form-n[data-id="${formId}"]`);
	if (!form) return;

	const fields = Array.from(form.querySelectorAll('.alpha-form-field'));
	const current = form.querySelector('.alpha-form-field.active');
	if (!current || !fields.length) return;

	if (!isValid(current)) {
		toggleErrorMessage(current, true);
		return;
	} else {
		toggleErrorMessage(current, false);
	}

	const index = fields.indexOf(current);
	const targetIndex = absolute ? (parseInt(next) - 1) : (index + parseInt(next));

	const nextField = fields[targetIndex];

	if (nextField) {
		current.classList.remove('active');
		nextField.classList.add('active');
		const input = nextField.querySelector('input:not([type="hidden"]), select, textarea');
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

function initAlphaRadioNavigation() {
	document.addEventListener('change', e => {
		const input = e.target;
		if (input.type !== 'radio') return;

		const wrapper = input.closest('.widget-alpha-form-n[data-id]');
		if (!wrapper) return;

		const formId = wrapper.getAttribute('data-id');
		const nextAttr = input.dataset.next;

		if (nextAttr) {
			// Trata como índice absoluto (campo 3 → índice 2)
			setTimeout(() => goToNextField(formId, nextAttr, true), 50);
		} else {
			setTimeout(() => goToNextField(formId), 50); // Padrão: próximo item
		}
	});
}


// Chama a função no carregamento
window.addEventListener('DOMContentLoaded', markRequiredFields);