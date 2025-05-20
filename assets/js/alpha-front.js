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
	initAlphaEnterNavigation();

	updateShortcodeText();
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

function markFormCompleted(formId) {
	const storage = getAlphaStorage();
	if (!storage[formId]) return;

	storage[formId].complete = 1;
	saveAlphaStorage(storage);

	// 🔁 Dispara eventos
	if (window.alphaFormEvents?.facebook) {
		fbq('track', 'CompleteRegistration');
	}
	if (window.alphaFormEvents?.analytics) {
		gtag('event', 'form_submit', {
			event_category: 'Alpha Form',
			event_label: 'Formulário enviado'
		});
	}

	// Envia pro servidor
	fetch(alphaFormVars.ajaxurl, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams({
			action: 'alpha_form_mark_complete',
			nonce: alphaFormVars.nonce,
			form_id: formId,
			session_id: getOrCreateSessionId()
		})
	});
}

function resolveAlphaShortcodesInText(text, form) {
	if (!text || typeof text !== 'string') return text;
	if (!form) form = document;

	const inputs = form.querySelectorAll('input, select, textarea');
	const map = {};

	inputs.forEach(input => {
		const key = input.name || input.id;
		if (!key) return;

		let value = '';
		if (input.type === 'checkbox') {
			const checked = form.querySelectorAll(`input[name="${input.name}"]:checked`);
			value = Array.from(checked).map(cb => cb.value).join(', ');
		} else if (input.type === 'radio') {
			const selected = form.querySelector(`input[name="${input.name}"]:checked`);
			value = selected ? selected.value : '';
		} else {
			value = input.value || '';
		}

		map[key] = value;
	});

	return text.replace(/\[field-([^\|\]]+)(\|([^\]]+))?\]/g, (_, key, __, fallback = '') => {
		return map[key] || fallback || '';
	});
}

function initAlphaForm() {
	const wrappers = document.querySelectorAll('.widget-alpha-form-n[data-id]');
	if (!wrappers.length) return;

	const globalStorage = getAlphaStorage();

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
	Object.entries(savedData).forEach(([stepKey, stepData], index) => {
		if (!/^\d+$/.test(stepKey)) return; // Só entra se for step numérico

		const stepIndex = parseInt(stepKey, 10);
		const field = fields[stepIndex - 1];
		if (!field || typeof stepData !== 'object') return;

		const inputs = field.querySelectorAll('input, select, textarea');

		inputs.forEach(input => {
			const key = input.name || input.id || input.dataset.customId;
			if (!key) return;

			const value = stepData[key];
			if (value == null) return;

			if (input.type === 'checkbox') {
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
		});
	});
	updateAllRadioButtonsVisibility();
}

function activateLastVisitedStep(fields, saved) {
	if (!saved || !saved.lastQuest) return false;

	let fieldByLast = fields.find(f => {
		const input = f.querySelector('input, select, textarea');
		return input && (input.name === saved.lastQuest || input.id === saved.lastQuest);
	});

	if (!fieldByLast && !isNaN(saved.lastQuest)) {
		const index = parseInt(saved.lastQuest);
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

function initAlphaEnterNavigation() {
	document.addEventListener('keydown', e => {
		// Só segue se for Enter
		if (e.key !== 'Enter') return;

		const field = e.target.closest('.alpha-form-field');
		if (!field) return;

		// Não avança se for textarea (Enter serve para quebra de linha)
		if (e.target.tagName === 'TEXTAREA') return;

		const wrapper = field.closest('.widget-alpha-form-n[data-id]');
		if (!wrapper) return;

		const formId = wrapper.getAttribute('data-id');
		if (!formId) return;

		// Valida antes de avançar
		if (!isValid(field)) {
			toggleErrorMessage(field, true);
			return;
		}

		e.preventDefault();
		toggleErrorMessage(field, false);
		goToNextField(formId);
	});
}

function toggleAlphaOverlay(show = true) {
	const overlay = document.querySelector('.alpha-form-overlay');
	if (!overlay) return;
	overlay.style.display = show ? 'flex' : 'none';
}

function showAlphaToast(message = 'Tudo certo!', duration = 5000) {
	let toast = document.querySelector('.alpha-form-toast');

	if (!toast) {
		toast = document.createElement('div');
		toast.className = 'alpha-form-toast';
		document.body.appendChild(toast);
	}

	toast.textContent = message;
	toast.classList.add('visible');

	// Remove a classe após o tempo + transição
	setTimeout(() => {
		toast.classList.remove('visible');

		// Aguarda a transição terminar antes de esconder
		setTimeout(() => {
			toast.style.display = 'none';
		}, 4000); // deve ser igual à duração do transition no CSS
	}, duration);

	// Garante que aparece se estava escondido
	toast.style.display = 'block';
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
			lastQuest: 0,
			data: {},
		};
	}
	else {
		const last = parseInt(storage[formId].lastQuest || '0');
		stepIndex = last + 1;
		status.startForm = 1;
	}

	saveAlphaStep(formId, fieldKey, value, stepIndex, JSON.parse(tempoJson || '{}'));

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
		// 🔄 Merge dos tempos antigos com os atuais
		const fullTempo = {
			...(storage[formId].tempo || {}),
			...(JSON.parse(tempoJson || '{}'))
		};

		// Atualiza o localStorage também com o tempo completo
		storage[formId].tempo = fullTempo;

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
				value: typeof value === 'object' ? JSON.stringify(value) : value,
				status: JSON.stringify({
					pageView: storage[formId].pageView,
					startForm: storage[formId].startForm,
					complete: storage[formId].complete
				}),
				ip: storage[formId].ip || '',
				city: storage[formId].city || '',
				region: storage[formId].region || '',
				country: storage[formId].country || '',
				tempo_json: JSON.stringify(fullTempo)
			})
		});
	}
}

function saveAlphaStep(formId, key, value, stepIndex = null, tempo = null) {
	const storage = getAlphaStorage();
	if (!storage[formId]) return;

	storage[formId].data[key] = value;
	if (key !== "__init") {
		storage[formId].startForm = 1;
		storage[formId].lastQuest = key;
	}

	if (tempo && typeof tempo === 'object') {
		storage[formId].tempo = {
			...(storage[formId].tempo || {}),
			...tempo,
			__last: tempo.__last || storage[formId].tempo?.__last
		};
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
	const errorMessages = field.querySelectorAll('.alpha-error-message');

	errorMessages.forEach(errorMessage => {
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
	});
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
	const stepKey = index + 1;
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
	saveAlphaStepToDB(formId, stepKey, { ...value }, status, index + 1, JSON.stringify(tempo));

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

	const values = {};
	let firstValidKey = null;

	inputs.forEach(input => {
		const key = input.name || input.id || input.dataset.customId;
		if (!key) return;

		let value = null;

		if (input.type === 'radio') {
			const checked = field.querySelector(`input[type="radio"][name="${input.name}"]:checked`);
			if (checked) value = checked.value;
		} else if (input.type === 'checkbox') {
			const group = field.querySelectorAll(`input[type="checkbox"][name="${input.name}"]:checked`);
			value = Array.from(group).map(i => i.value);
		} else {
			value = input.value;
		}

		values[key] = value;
		if (!firstValidKey) firstValidKey = key;
	});

	return { key: firstValidKey || Date.now(), value: values };
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

async function handleAlphaFormSubmit(form) {
	toggleAlphaOverlay(true);

	const formId = form.getAttribute('data-alpha-widget-id');
	const hiddenInput = form.querySelector('input[type="hidden"][data-alpha-submit]');
	if (!formId || !hiddenInput) {
		toggleAlphaOverlay(false);
		return;
	}

	let integrations = {};
	try {
		integrations = JSON.parse(hiddenInput.value);
	} catch (e) {
		console.error('Erro ao parsear integrações', e);
		toggleAlphaOverlay(false);
		return;
	}

	// Loop por integração
	for (const [name, config] of Object.entries(integrations)) {
		if (!config || Object.keys(config).length === 0) continue;

		// Mapeia os campos se houver 'fields' definidos
		if (config.fields && typeof config.fields === 'object') {
			const mappedFields = {};
			for (const [finalKey, inputId] of Object.entries(config.fields)) {
				const input = form.querySelector(`#${inputId}`);
				if (input) {
					mappedFields[finalKey] = input.value;
				}
			}
			// Substitui os campos mapeados
			config.data = mappedFields;
			delete config.fields;
		}

		// Envia para o PHP central
		const response = await fetch(alphaFormVars.ajaxurl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				action: 'alpha_form_handle_integration',
				nonce: alphaFormVars.nonce,
				integration: name,
				form_id: formId,
				data: JSON.stringify(config)
			})
		});

		const result = await response.json();
		if (result.success && result.result === true) {
			showAlphaToast(`✅ Enviado com sucesso para ${name}`);
		} else {
			showAlphaToast(`❌ Falha ao enviar para ${name}`, true);
			console.warn(`Falha ao enviar para ${name}`, result);
		}
	}

	// Marca como completo
	markFormCompleted(formId);
	toggleAlphaOverlay(false);

	// Redirecionamento, se configurado
	if (integrations.redirect?.url) {
		const finalUrl = resolveAlphaShortcodesInText(integrations.redirect.url, form);
		setTimeout(() => window.location.href = finalUrl, 500);
	} else {
		showAlphaToast('Formulário enviado com sucesso!');
	}
}


window.addEventListener('DOMContentLoaded', markRequiredFields);

document.addEventListener('submit', async e => {
	const form = e.target.closest('.alpha-form');
	if (form) {
		e.preventDefault();
		await handleAlphaFormSubmit(form);
	}
});


