console.log("[Alpha Forms] - iniciado");

document.addEventListener('DOMContentLoaded', () => {
	const forms = document.querySelectorAll('.alpha-form[data-alpha-widget-id]');
	let formId = [];
	forms.forEach(form => {
		const id = form.getAttribute('data-alpha-widget-id');
		if (id) {
			formId.push(id);
			initAlphaFormSession(id);
		}
	});

	initAlphaForm();
	initAlphaNavigation();
	initAlphaRadioNavigation();
	applyAlphaLetters();
	applyAlphaInputMasks();
	// initAlphaFormIntegrations();
	initShortcodeTextBindings();

	// Atualiza com dados do localStorage ao carregar
	updateShortcodeTextWithLocalStorage();

	// Atualiza ao clicar no botão "Próximo"
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

function updateShortcodeTextWithLocalStorage(scope = document.body) {
	try {
		const storage = JSON.parse(localStorage.getItem('alpha-form-data-response'));
		if (!storage) return;

		Object.values(storage).forEach(formData => {
			const data = formData.data || {};
			Object.keys(data).forEach(key => {
				const value = data[key];
				const spans = scope.querySelectorAll(`.alpha-shortcode[data-key="${key}"]`);
				spans.forEach(span => {
					span.textContent = value || span.dataset.default;
				});
			});
		});
	} catch (e) {
		console.warn('Alpha Form: erro ao recuperar dados do localStorage.', e);
	}
}

function initAlphaForm() {
	const wrappers = document.querySelectorAll('.widget-alpha-form-n[data-id]');
	if (!wrappers.length) return;

	const globalStorage = getAlphaStorage();

	// Grupos de controle por atributo
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

		// Marca permissões por atributo
		if (form.hasAttribute('data-exit-block')) exitBlockForms.add(formId);
		if (form.hasAttribute('data-location')) locationForms.add(formId);
		if (form.hasAttribute('data-return')) returnForms.add(formId);

		// Ativa o step
		const shouldReturn = returnForms.has(formId);
		const stepActivated = shouldReturn ? activateLastVisitedStep(fields, saved) : false;

		if (!stepActivated) {
			activateFirstStep(fields);
		}

		initAlphaFormSession(formId);

		if (locationForms.has(formId)) {
			fetchCityFromIP(formId);
		}

		updateAlphaProgressBar(formId);
		updateShortcodeText()
	});

	// Só ativa alerta se necessário
	if (exitBlockForms.size > 0) {
		enableFormLeaveWarning(exitBlockForms);
	}
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

function initAlphaFormSession(formId) {
	const storage = getAlphaStorage();
	const sessionId = getOrCreateSessionId();

	if (!storage[formId]) {
		storage[formId] = {
			sessionId,
			pageView: 1,
			startForm: 0,
			complete: 0,
			lastQuest: null,
			data: {},
		};
		saveAlphaStorage(storage);
	}
}


function saveAlphaStep(formId, key, value) {
	const storage = getAlphaStorage();
	if (!storage[formId]) return;

	storage[formId].data[key] = value;
	storage[formId].lastQuest = key;
	saveAlphaStorage(storage);
}

function markFormStarted(formId) {
	const storage = getAlphaStorage();
	if (storage[formId] && storage[formId].startForm === 0) {
		storage[formId].startForm = 1;
		saveAlphaStorage(storage);

		if (window.alphaFormEvents?.facebook) {
			fbq('trackCustom', 'StartForm');
		}
		if (window.alphaFormEvents?.analytics) {
			gtag('event', 'start_form', {
				event_category: 'Alpha Form',
				event_label: 'Formulário iniciado'
			});
		}
	}
}

function markFormCompleted(formId) {
	const storage = getAlphaStorage();
	if (storage[formId]) {
		storage[formId].complete = 1;
		saveAlphaStorage(storage);
		if (window.alphaFormEvents?.facebook) {
			fbq('track', 'CompleteRegistration');
		}
		if (window.alphaFormEvents?.analytics) {
			gtag('event', 'form_submit', {
				event_category: 'Alpha Form',
				event_label: 'Formulário enviado'
			});
		}
	}
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

function fetchCityFromIP(formId) {
	const storage = getAlphaStorage();
	if (!storage[formId]) return;

	fetch('https://ipwho.is/')
		.then(res => res.json())
		.then(data => {
			storage[formId].ip = data.ip;
			storage[formId].city = data.city;
			storage[formId].region = data.region;
			storage[formId].country = data.country;

			saveAlphaStorage(storage);
		})
		.catch(() => {
			console.warn('Falha ao obter cidade por IP');
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

function isValid(field) {
	const input = field.querySelector('input, select, textarea');
	if (!input) return true;
	else if (input.type === 'hidden') return true;

	else if (!input.hasAttribute('required')) return true;

	const type = input.type;

	const mask = input.dataset.mask;
	if (mask) {
		const value = input.value.trim();

		switch (mask) {
			case 'cpf':
				if (value.length !== 14) return false;
				break;
			case 'cnpj':
				if (value.length !== 18) return false;
				break;
			case 'cep':
				if (value.length !== 9) return false;
				break;
			case 'cel':
				if (value.length < 14) return false;
				break;
			case 'currency':
				if (!/R\$ (\d{1,3}(\.\d{3})*|\d+),\d{2}/.test(value)) return false;
				break;
		}
	}

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

	let saved = false;

	const result = getAlphaFieldValue(current);
	if (result && result.key && result.value != null && result.value !== '') {
		saveAlphaStep(formId, result.key, result.value);
		saved = true;
		updateShortcodeText()
	}

	if (!saved) {
		// Força o salvamento mesmo sem input
		const index = fields.indexOf(current);
		const stepKey = index >= 0 ? `__step_${index + 1}` : `__step_${Date.now()}`;
		saveAlphaStep(formId, stepKey, 1);
		updateShortcodeText()
	}

	markFormStarted(formId);

	const index = fields.indexOf(current);
	const targetIndex = absolute ? (parseInt(next) - 1) : (index + parseInt(next));
	const nextField = fields[targetIndex];

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

	// Encontra o primeiro input visível válido
	let input = Array.from(inputs).find(el =>
		['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName)
	);

	if (!input) return null;

	const key = input.name || input.id || input.dataset.customId;
	if (!key) return null;

	let value = null;

	if (input.tagName === 'SELECT') {
		value = input.value;
	} else if (input.type === 'radio') {
		const checked = field.querySelector(`input[type="radio"][name="${input.name}"]:checked`);
		if (checked) value = checked.value;
	} else if (input.type === 'checkbox') {
		const group = field.querySelectorAll(`input[type="checkbox"][name="${input.name}"]:checked`);
		value = Array.from(group).map(i => i.value);
	} else {
		value = input.value;
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
			// Trata como índice absoluto (campo 3 → índice 2)
			setTimeout(() => goToNextField(formId, nextAttr, true), 50);
		} else {
			setTimeout(() => goToNextField(formId), 50); // Padrão: próximo item
		}

		updateAllRadioButtonsVisibility();
	});
}

function updateAlphaProgressBar(formId) {
	const formWrapper = document.querySelector(`.widget-alpha-form-n[data-id="${formId}"]`);
	if (!formWrapper) return;

	const fields = Array.from(formWrapper.querySelectorAll('.alpha-form-field'));
	const total = fields.length;
	if (total < 2) return; // evita divisão por zero

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
		// Evita duplicação
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


// Chama a função no carregamento
window.addEventListener('DOMContentLoaded', markRequiredFields);