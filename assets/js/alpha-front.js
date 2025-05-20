console.log("[Alpha Forms] - iniciado");

document.addEventListener('DOMContentLoaded', () => {
	const forms = document.querySelectorAll('.alpha-form[data-alpha-widget-id]');
	let formId = [];

	forms.forEach(form => {
		const id = form.getAttribute('data-alpha-widget-id');
		if (id) {
			if (!window.alphaFormTime) window.alphaFormTime = {};
			if (!window.alphaFormTime[id]) {
				window.alphaFormTime[id] = {
					__last: Date.now()
				};
			}

			formId.push(id);
			saveAlphaStepToDB(id, '__init', 1, { pageView: 1 }, 0, JSON.stringify(window.alphaFormTime[id]));
		}
	});


	initAlphaForm();
	initAlphaNavigation();
	initAlphaRadioNavigation();
	applyAlphaLetters();
	applyAlphaInputMasks();
	// initAlphaFormIntegrations();
	initShortcodeTextBindings();

	document.querySelectorAll('[data-alpha="next"]').forEach(btn => {
		btn.addEventListener('click', () => {
			updateShortcodeText();
		});
	});
});

function initShortcodeTextBindings(scope = document.body) {
	const treeWalker = document.createTreeWalker(scope, NodeFilter.SHOW_TEXT, {
		acceptNode: (node) => {
			const parent = node.parentElement;

			if (!parent || parent.closest('a, .alpha-ignore-shortcode')) {
				return NodeFilter.FILTER_REJECT;
			}

			if (/\[field-[^\]]+\]/.test(node.nodeValue)) {
				return NodeFilter.FILTER_ACCEPT;
			}

			return NodeFilter.FILTER_REJECT;
		}
	});

	const nodesToReplace = [];

	while (treeWalker.nextNode()) {
		nodesToReplace.push(treeWalker.currentNode);
	}

	nodesToReplace.forEach(textNode => {
		const html = textNode.nodeValue.replace(/\[field-([^\|\]]+)\|([^\]]+)\]/g, (_, key, fallback) => {
			return `<span class="alpha-shortcode" data-key="${key}" data-default="${fallback}">${fallback}</span>`;
		});

		const wrapper = document.createElement('span');
		wrapper.innerHTML = html;

		textNode.parentNode.replaceChild(wrapper, textNode);
	});
}

function updateShortcodeText(scope = document.body) {
	const inputs = scope.querySelectorAll('input, select, textarea');

	inputs.forEach(input => {
		const key = input.name || input.id;
		if (!key) return;

		const value = input.value.trim();
		const spans = scope.querySelectorAll(`.alpha-shortcode[data-key="${key}"]`);

		spans.forEach(span => {
			span.textContent = value || span.dataset.default;
		});
	});
}

function initAlphaForm() {
	const wrappers = document.querySelectorAll('.widget-alpha-form-n[data-id]');
	if (!wrappers.length) return;

	const globalStorage = getAlphaStorage();
	console.log(globalStorage)

	const exitBlockForms = new Set();
	const locationForms = new Set();
	const returnForms = new Set();

	wrappers.forEach(wrapper => {
		const form = wrapper.querySelector('.alpha-form');
		if (!form) return;

		const formId = form.getAttribute('data-alpha-widget-id');
		if (!formId) return;

		const fields = Array.from(wrapper.querySelectorAll('.alpha-form-field'));
		if (!fields.length) return;

		const saved = globalStorage[formId];
		const savedData = saved?.data || {};

		resetActiveStates(fields);
		restoreSavedValues(fields, savedData);

		if (form.hasAttribute('data-exit-block')) exitBlockForms.add(formId);
		if (form.hasAttribute('data-location')) locationForms.add(formId);
		if (form.hasAttribute('data-return')) returnForms.add(formId);

		const shouldReturn = returnForms.has(formId);
		const stepActivated = shouldReturn ? activateLastVisitedStep(fields, saved) : false;

		if (!stepActivated) {
			activateFirstStep(fields);
		}

		updateAlphaProgressBar(formId);
		resolveAlphaShortcodes(form);
	});

	if (exitBlockForms.size > 0) {
		enableFormLeaveWarning(exitBlockForms);
	}
}

function resolveAlphaShortcodes(formElement) {

	if (!formElement) return;
	const values = {};

	const inputs = formElement.querySelectorAll('input, select, textarea');
	inputs.forEach(input => {
		const id = input.name || input.id || input.dataset.customId;
		if (!id) return;

		const key = `[field-${id}]`;
		let value = '';

		if (input.type === 'checkbox') {
			const checked = formElement.querySelectorAll(`input[name="${input.name}"]:checked`);
			value = Array.from(checked).map(cb => cb.value).join(', ');
		} else if (input.type === 'radio') {
			const selected = formElement.querySelector(`input[name="${input.name}"]:checked`);
			value = selected ? selected.value : '';
		} else {
			value = input.value;
		}

		values[key] = value;
	});

	// 1. Substituir em todos os elementos com texto
	document.querySelectorAll('body *').forEach(el => {
		if (el.children.length === 0) {
			let html = el.innerHTML;
			let altered = false;

			Object.entries(values).forEach(([shortcode, val]) => {
				if (html.includes(shortcode)) {
					html = html.replaceAll(shortcode, val);
					altered = true;
				}
			});

			if (altered) el.innerHTML = html;
		}
	});

	// 2. Substituir em atributos comuns
	const attrList = ['src', 'action', 'data-href', 'data-url', 'data-target'];
	document.querySelectorAll('*').forEach(el => {
		attrList.forEach(attr => {
			const original = el.getAttribute(attr);
			if (original && original.includes('[field-')) {
				let updated = original;
				Object.entries(values).forEach(([shortcode, val]) => {
					updated = updated.replaceAll(shortcode, val);
				});
				el.setAttribute(attr, updated);
			}
		});
	});
}


function resetActiveStates(fields) {
	fields.forEach(f => f.classList.remove('active'));
}

function restoreSavedValues(fields, savedData) {
	fields.forEach(field => {
		const inputs = field.querySelectorAll('input, select, textarea');
		if (!inputs.length) return;

		const input = inputs[0];
		const key = input.name || input.id || input.dataset.customId;
		if (!key) return;

		const value = savedData[key];
		if (value == null) return;

		if (input.type === 'checkbox') {
			// marca todos os checkboxes do mesmo nome que estiverem no valor
			const group = field.querySelectorAll(`input[type="checkbox"][name="${input.name}"]`);
			const values = Array.isArray(value) ? value : [value];
			group.forEach(cb => {
				cb.checked = values.includes(cb.value);
			});
		} else if (input.type === 'radio') {
			const group = field.querySelectorAll(`input[type="radio"][name="${input.name}"]`);
			group.forEach(r => {
				r.checked = r.value == value;
			});
		} else if (input.tagName === 'SELECT') {
			Array.from(input.options).forEach(option => {
				option.selected = option.value == value;
			});
		} else {
			input.value = value;
		}
		updateAllRadioButtonsVisibility();
	});
}

function activateLastVisitedStep(fields, saved) {
	if (!saved || !saved.lastQuest) return false;

	let fieldByLast = fields.find(f => {
		const input = f.querySelector('input, select, textarea');
		return input && (input.name === saved.lastQuest || input.id === saved.lastQuest);
	});

	if (!fieldByLast && saved.lastQuest.startsWith('__step_')) {
		const index = parseInt(saved.lastQuest.replace('__step_', ''));
		if (fields[index]) {
			fieldByLast = fields[index];
		}
	}


	if (fieldByLast) {
		fieldByLast.classList.add('active');
		const input = fieldByLast.querySelector('input:not([type="hidden"]), select, textarea');
		if (input) input.focus();
		return true;
	}

	return false;
}

function activateFirstStep(fields) {
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

function enableFormLeaveWarning(formIdSet) {
	window.addEventListener('beforeunload', e => {
		const storage = getAlphaStorage();

		const hasPending = Array.from(formIdSet).some(formId => {
			const s = storage[formId];
			return s?.startForm === 1 && s?.complete === 0;
		});

		if (hasPending) {
			const message = 'Você tem um formulário não enviado. Deseja mesmo sair?';
			e.preventDefault();
			e.returnValue = message;
			return message;
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

function saveAlphaStepToDB(formId, fieldKey, value, status = {}, stepIndex = 0, tempoJson = '{}') {
	const storage = getAlphaStorage();
	const sessionId = getOrCreateSessionId();

	if (!storage[formId]) {
		storage[formId] = {
			sessionId,
			pageView: 0,
			startForm: 0,
			complete: 0,
			lastQuest: null,
			data: {},
		};
	}

	saveAlphaStep(formId, fieldKey, value, stepIndex);

	// Atualiza status
	if (status.pageView) storage[formId].pageView = 1;
	if (status.startForm) storage[formId].startForm = 1;
	if (status.complete) storage[formId].complete = 1;

	// Dispara eventos
	if (status.startForm && window.alphaFormEvents?.facebook) {
		fbq('trackCustom', 'StartForm');
	}
	if (status.startForm && window.alphaFormEvents?.analytics) {
		gtag('event', 'start_form', {
			event_category: 'Alpha Form',
			event_label: 'Formulário iniciado'
		});
	}
	if (status.complete && window.alphaFormEvents?.facebook) {
		fbq('track', 'CompleteRegistration');
	}
	if (status.complete && window.alphaFormEvents?.analytics) {
		gtag('event', 'form_submit', {
			event_category: 'Alpha Form',
			event_label: 'Formulário enviado'
		});
	}

	// 🔁 GeoIP só uma vez por sessão
	if (!storage[formId]._geoSent) {
		storage[formId]._geoSent = true;
		saveAlphaStorage(storage);

		fetch('https://ipwho.is/')
			.then(res => res.json())
			.then(data => {
				storage[formId].ip = data.ip;
				storage[formId].city = data.city;
				storage[formId].region = data.region;
				storage[formId].country = data.country;
				saveAlphaStorage(storage);

				// Chamada AJAX com geolocalização
				sendAlphaFormAjax();
			});
	} else {
		sendAlphaFormAjax();
	}

	function sendAlphaFormAjax() {
		fetch(alphaFormVars.ajaxurl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				action: 'alpha_form_save_step',
				nonce: alphaFormVars.nonce,
				form_id: formId,
				post_id: getPostId(),
				session_id: sessionId,
				field_key: fieldKey,
				last_quest: stepIndex,
				value: value,
				status: JSON.stringify({
					pageView: storage[formId].pageView,
					startForm: storage[formId].startForm,
					complete: storage[formId].complete
				}),
				ip: storage[formId].ip || '',
				city: storage[formId].city || '',
				region: storage[formId].region || '',
				country: storage[formId].country || '',
				tempo_json: tempoJson,
			})
		});
	}
}

function saveAlphaStep(formId, key, value, stepIndex = 0) {
	const storage = getAlphaStorage();
	if (!storage[formId]) return;

	storage[formId].data[key] = value;

	if (key !== '__init') {
		storage[formId].lastQuest = `__step_${stepIndex}`;
	}

	saveAlphaStorage(storage);
}

function getPostId() {
	return parseInt(document.querySelector('[data-elementor-id]')?.dataset.elementorId || 0);
}

function saveAlphaStorage(data) {
	localStorage.setItem('alpha-form-data-response', JSON.stringify(data));
}

function getOrCreateSessionId() {
	let sessionId = localStorage.getItem('alpha-session-id');
	if (!sessionId) {
		sessionId = 'alpha_' + Math.random().toString(36).substring(2, 10);
		localStorage.setItem('alpha-session-id', sessionId);
	}
	return sessionId;
}

function getAlphaStorage() {
	return JSON.parse(localStorage.getItem('alpha-form-data-response') || '{}');
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

function isValid(field) {
	const inputs = field.querySelectorAll('input, select, textarea');
	if (!inputs.length) return true;

	let isFieldValid = true;

	inputs.forEach(input => {
		if (!input.hasAttribute('required') || input.type === 'hidden') return;

		const value = input.value.trim();
		const type = input.type;
		const mask = input.dataset.mask;

		if (mask) {
			switch (mask) {
				case 'cpf':
					if (value.length !== 14) isFieldValid = false;
					break;
				case 'cnpj':
					if (value.length !== 18) isFieldValid = false;
					break;
				case 'cep':
					if (value.length !== 9) isFieldValid = false;
					break;
				case 'cel':
					if (value.length < 14) isFieldValid = false;
					break;
				case 'currency':
					if (!/R\$ (\d{1,3}(\.\d{3})*|\d+),\d{2}/.test(value)) isFieldValid = false;
					break;
			}
			return; // se tiver máscara, ignora os próximos testes
		}

		if (type === 'email') {
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!emailRegex.test(value)) isFieldValid = false;
			return;
		}

		if (input.hasAttribute('pattern')) {
			const pattern = new RegExp(input.getAttribute('pattern'));
			if (!pattern.test(value)) isFieldValid = false;
			return;
		}

		if (type === 'radio') {
			const name = input.name;
			const group = field.querySelectorAll(`input[type="radio"][name="${name}"]`);
			if (!Array.from(group).some(r => r.checked)) isFieldValid = false;
			return;
		}

		if (type === 'checkbox') {
			const group = field.querySelectorAll('input[type="checkbox"]');
			if (!Array.from(group).some(c => c.checked)) isFieldValid = false;
			return;
		}

		if (!value) isFieldValid = false;
	});

	return isFieldValid;
}

function shakeInput(input) {
	if (!input) return;

	input.classList.add('alpha-shake');

	// Remove após a animação para permitir repetir no futuro
	input.addEventListener('animationend', () => {
		input.classList.remove('alpha-shake');
	}, { once: true });
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
	if (!errorMessage) return;

	const input = errorMessage.previousElementSibling;
	if (input && ['INPUT', 'SELECT', 'TEXTAREA'].includes(input.tagName)) {
		if (show) {
			input.classList.add('alpha-error');
			shakeInput(input);
		} else {
			input.classList.remove('alpha-error');
		}
	}

	errorMessage.style.display = show ? 'block' : 'none';
}

function goToNextField(formId, next = 1, absolute = false) {
	const form = document.querySelector(`.widget-alpha-form-n[data-id="${formId}"]`);
	if (!form) return;

	const fields = Array.from(form.querySelectorAll('.alpha-form-field'));
	const current = form.querySelector('.alpha-form-field.active');
	if (!current || !fields.length) return;

	const index = fields.indexOf(current);
	const targetIndex = absolute ? (parseInt(next) - 1) : (index + parseInt(next));
	const nextField = fields[targetIndex];

	const result = getAlphaFieldValue(current);
	const stepKey = result?.key || `__step_${index + 1}`;
	const value = result?.value ?? 1;

	// status dinâmico
	const status = {};
	if (index === 0) status.startForm = 1;
	if (targetIndex >= fields.length - 1) status.complete = 1;

	// Inicializa o mapa local de tempo (pode estar no window, session ou outro escopo)
	if (!window.alphaFormTime) window.alphaFormTime = {};
	if (!window.alphaFormTime[formId]) {
		window.alphaFormTime[formId] = {
			__last: Date.now(),
		};
	}

	const now = Date.now();
	const last = window.alphaFormTime[formId].__last;
	const elapsed = (now - last) / 1000;

	const tempo = window.alphaFormTime[formId];

	// Salva o tempo da etapa anterior
	tempo[stepKey] = elapsed;
	tempo.__last = now;

	// salva resposta e tempo corretamente
	saveAlphaStepToDB(formId, stepKey, value, status, index + 1, JSON.stringify(tempo));

	updateShortcodeText();

	if (nextField) {
		current.classList.remove('active');
		nextField.classList.add('active');

		const input = nextField.querySelector('input:not([type="hidden"]), select, textarea');
		if (input) input.focus();

		updateAlphaProgressBar(formId);
	}
}

function getAlphaFieldValue(field) {
	const inputs = field.querySelectorAll('input, select, textarea');
	if (!inputs.length) return null;

	const input = Array.from(inputs).find(el =>
		['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName)
	);
	if (!input) return null;

	const key = input.name || input.id || input.dataset.customId;
	if (!key) return null;

	let value = null;

	if (input.type === 'checkbox') {
		const checkboxes = field.querySelectorAll(`input[type="checkbox"][name="${input.name}"]:checked`);
		value = Array.from(checkboxes).map(cb => cb.value);
		if (!value.length) return null;
	} else if (input.type === 'radio') {
		const checked = field.querySelector(`input[type="radio"][name="${input.name}"]:checked`);
		if (!checked) return null;
		value = checked.value;

	} else {
		value = input.value?.trim();
		if (!value) return null;
	}

	return { key, value };
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
		updateAlphaProgressBar(formId);
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
			setTimeout(() => goToNextField(formId, nextAttr, true), 50);
		} else {
			setTimeout(() => goToNextField(formId), 50);
		}

		updateAllRadioButtonsVisibility();
	});
}

function updateAlphaProgressBar(formId) {
	const formWrapper = document.querySelector(`.widget-alpha-form-n[data-id="${formId}"]`);
	if (!formWrapper) return;

	const fields = Array.from(formWrapper.querySelectorAll('.alpha-form-field'));
	const total = fields.length;
	if (total < 2) return;

	const current = formWrapper.querySelector('.alpha-form-field.active');
	if (!current) return;

	const currentIndex = fields.indexOf(current);
	const percentage = Math.round((currentIndex / (total - 1)) * 100);

	const progressWrapper = document.querySelector(`.alpha-form-progress-wrapper[data-target="${formId}"]`);
	if (!progressWrapper) return;

	const fill = progressWrapper.querySelector('.alpha-form-progress-bar-fill');

	if (fill) {
		fill.style.transition = 'width 0.4s ease';
		fill.style.width = `${percentage}%`;
	}

	const percentText = progressWrapper.querySelector('.alpha-form-progress-percent');
	if (percentText) {
		percentText.textContent = `${percentage}%`;
	}
}


function updateAllRadioButtonsVisibility() {
	const fields = document.querySelectorAll('.alpha-form-field');

	fields.forEach(field => {
		const radios = field.querySelectorAll('input[type="radio"]');
		if (!radios.length) return;

		const anyChecked = Array.from(radios).some(r => r.checked);
		const wrapper = field.querySelector('.alpha-form-next.form');
		if (!wrapper) return;

		if (anyChecked) {
			wrapper.style.display = 'flex';
			wrapper.classList.add('alpha-fade-in');
		} else {
			wrapper.style.display = 'none';
			wrapper.classList.remove('alpha-fade-in');
		}
	});
}

function applyAlphaInputMasks() {
	document.querySelectorAll('input[data-mask]').forEach(input => {
		const mask = input.dataset.mask;

		input.removeEventListener('input', input._maskHandler);

		let handler;

		switch (mask) {
			case 'cpf':
				handler = e => {
					e.target.value = e.target.value
						.replace(/\D/g, '')
						.slice(0, 11)
						.replace(/(\d{3})(\d)/, '$1.$2')
						.replace(/(\d{3})(\d)/, '$1.$2')
						.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
				};
				break;

			case 'cnpj':
				handler = e => {
					e.target.value = e.target.value
						.replace(/\D/g, '')
						.slice(0, 14)
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
						.slice(0, 8)
						.replace(/^(\d{5})(\d)/, '$1-$2');
				};
				break;

			case 'currency':
				handler = e => {
					let val = e.target.value.replace(/\D/g, '').slice(0, 15);
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
						.slice(0, 11)
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

		if (label.classList.contains('alpha-letter-active')) return;

		const letter = label.getAttribute('data-letter');
		if (letter) {
			label.classList.add('alpha-letter-active');
			label.setAttribute('data-letter-display', letter);
		}
	});
}
async function initAlphaFormIntegrations() {
	const integrationsToCheck = ['facebook', 'analytics'];

	const res = await fetch(alphaFormVars.ajaxurl, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams({
			action: 'alpha_form_get_integrations',
			nonce: alphaFormVars.nonce,
			'integrations[]': integrationsToCheck
		})
	});

	const json = await res.json();
	if (!json.success || !json.integrations) return;

	const active = json.integrations;

	// 🟦 FACEBOOK PIXEL
	if (active.facebook?.pixel_id) {
		const pixelId = active.facebook.pixel_id;

		(function (f, b, e, v, n, t, s) {
			if (f.fbq) return;
			n = f.fbq = function () {
				n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
			};
			if (!f._fbq) f._fbq = n;
			n.push = n; n.loaded = !0; n.version = '2.0';
			n.queue = [];
			t = b.createElement(e); t.async = !0;
			t.src = v;
			s = b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t, s);
		})(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

		fbq('init', pixelId);
		fbq('track', 'PageView');

		window.alphaFormEvents = window.alphaFormEvents || {};
		window.alphaFormEvents.facebook = true;
	}

	// 🟧 GOOGLE ANALYTICS
	if (active.analytics?.measurement_id) {
		const gaId = active.analytics.measurement_id;

		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }

		const script = document.createElement('script');
		script.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
		script.async = true;
		document.head.appendChild(script);

		script.onload = () => {
			gtag('js', new Date());
			gtag('config', gaId);
			window.alphaFormEvents = window.alphaFormEvents || {};
			window.alphaFormEvents.analytics = true;
		};
	}
}


window.addEventListener('DOMContentLoaded', markRequiredFields);