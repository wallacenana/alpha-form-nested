elementorCommon.elements.$window.on('elementor/nested-element-type-loaded', function () {
	const type = 'alpha-form';

	// Evita registrar se já existir
	if (elementor.elementsManager.getElementTypes && elementor.elementsManager.getElementTypes(type)) {
		return;
	}

	class NestedAlphaFormView extends $e.components.get('nested-elements').exports.NestedView {
		onAddChild(childView) {
			childView.$el.addClass('alpha-f-n-item-content');
		}
	}

	class NestedAlphaFormElement extends elementor.modules.elements.types.NestedElementBase {
		getType() {
			return type;
		}

		getView() {
			return NestedAlphaFormView;
		}
	}

	elementor.elementsManager.registerElementType(new NestedAlphaFormElement());
});

jQuery(window).on('elementor:init', function () {
	elementor.channels.editor.on('alphaform:editor:load_widget_id', function () {
		const panel = elementor.getPanelView();
		const model = panel?.getCurrentPageView()?.model;
		const view = panel?.getCurrentPageView();

		if (!model || !view) return;
		if (!['alpha-next', 'alpha-prev', 'alpha-progress'].includes(model.get('widgetType'))) return;

		const iframe = elementor.$preview?.[0];
		const previewDocument = iframe?.contentDocument || iframe?.contentWindow?.document;
		if (!previewDocument) return;

		const $select = panel.$el.find('[data-setting="form_target"]');
		if (!$select.length) return;

		const forms = previewDocument.querySelectorAll('.widget-alpha-form-n[data-id]');
		if (!forms.length) {
			return;
		}

		const current = model.getSetting('form_target');
		if ($select.data('alpha-init')) return;
		$select.data('alpha-init', true); // ✅ evita repetição

		$select.empty();
		$select.append('<option value="">Selecione o formulário...</option>');

		forms.forEach(form => {
			const id = form.getAttribute('data-id');
			const inner = form.querySelector('.alpha-f-n');
			const name = inner?.getAttribute('data-form-name') || `Alpha Form – ${id}`;
			const selected = current === id ? 'selected' : '';

			$select.append(`<option value="${id}" ${selected}>${name}</option>`);
		});

		elementor.notifications.showToast({
			message: 'Lista de formulários atualizada!',
			type: 'success',
			timeout: 2000
		});
	});

});

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

elementor.hooks.addAction('panel/open_editor/widget', function (panel, model) {
	if (!['alpha-field'].includes(model.get('widgetType'))) return;

	const $input = panel.$el.find('[data-setting="field_name"]');
	if (!$input.length) return;

	// Pega o ID real do widget (tipo e123abc)
	const widgetId = model.id;

	// Usa isso como valor padrão se o campo estiver vazio
	if (!$input.val() && widgetId) {
		$input.val(widgetId).trigger('input');
	}
});

(function ($) {
	function alphaformLoadIntegrationFields(prefix, options = {}) {
		const model = elementor.getPanelView()?.getCurrentPageView()?.model;
		const apiKey = $(`input[data-setting="${prefix}_custom_api_key"]`).val();
		const server = $(`input[data-setting="${prefix}_custom_server"]`).val();
		const $selectList = $(`select[data-setting="${prefix}_list_id"]`);
		const $selectListCustom = $(`select[data-setting="${prefix}_list_id_custom"]`);
		const typeDataLine = $(`select[data-setting="${prefix}_source_type"]`).val();

		if (typeDataLine === 'custom') {
			if (!apiKey) {
				elementor.notifications.showToast({
					message: 'Preencha os dados da API!',
					type: 'error',
					timeout: 2000
				});
				$selectListCustom.empty().append('<option value=""></option>').val('');
				return;

			} else if (!server) {
				elementor.notifications.showToast({
					message: 'Preencha os dados da API!',
					type: 'error',
					timeout: 2000
				});
				$selectListCustom.empty().append('<option value=""></option>').val('');
			}
			else
				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'alphaform_load_lists',
						prefix: prefix,
						_ajax_nonce: alphaFormVars.nonce
					},
					success: function (response) {
						console.log(response)
						if (!response.success || !response.data) return;


						const listas = response.data.lists;
						const campo = $selectList.data('setting');
						const valorAtual = model?.get('settings')?.get(campo);

						$selectListCustom.empty().append('<option value="">Selecione uma lista</option>');

						$.each(listas, function (val, label) {
							const selected = val === valorAtual ? 'selected' : '';
							$selectListCustom.append(`<option value="${val}" ${selected}>${label}</option>`);
						});

						if (valorAtual) $selectListCustom.val(valorAtual);

						elementor.notifications.showToast({
							message: 'Listas carregadas!',
							type: 'success',
							timeout: 1000
						});

						const listId = $selectListCustom.val();
						if (!listId) return;

					}
				});
		}


		const settings = model.get('settings').attributes;

		// 🔁 Coleta campos do formulário
		const inputFields = [];
		function traverseInputs(element) {
			if (!element) return;
			if (element.get('elType') === 'widget' && element.get('widgetType') === 'alpha-inputs') {
				const fieldName = element.attributes.settings.attributes.field_name || element.get('id');
				inputFields.push({
					value: element.get('id'),
					label: 'Field-' + fieldName
				});
			}
			const children = element.get('elements');
			if (children?.length) {
				children.forEach(traverseInputs);
			}
		}
		elementor.getPreviewView().model.get('elements').models.forEach(traverseInputs);

		const integrationFields = {
			mc: ["email_address", "FNAME", "LNAME", "PHONE", "BIRTHDAY", "ADDRESS", "COMPANY"],
			ac: ["email", "first_name", "last_name", "phone"],
			gr: ["email", "name", "phone", "city"],
			drip: ["email", "first_name", "last_name"],
			ck: ["email", "first_name"],
			ml: ["email", "name", "phone"],
			cs: ["email", "name", "phone", "address"]
		};

		if (prefix === 'mailchimp')
			prefix = 'mc'
		if (prefix === 'active-campaign')
			prefix = 'ac'
		if (prefix === 'drip')
			prefix = 'drip'
		if (prefix === 'getresponse')
			prefix = 'gr'
		if (prefix === 'convertkit')
			prefix = 'ck'
		if (prefix === 'clicksend')
			prefix = 'cs'
		if (prefix === 'mailerlite')
			prefix = 'ml'

		if (!integrationFields[prefix]) return;

		integrationFields[prefix].forEach(function (fieldKeyBase) {
			const selectName = `map_field_${fieldKeyBase}_${prefix}`;
			const $select = $(`select[data-setting="${selectName}"]`);
			const valorAtual = settings[selectName] || '';

			$select.empty().append('<option value="">— Nenhum campo —</option>');

			inputFields.forEach(field => {
				const selected = field.value === valorAtual ? 'selected' : '';
				$select.append(`<option value="${field.value}" ${selected}>${field.label}</option>`);
			});


			if (valorAtual) $select.val(valorAtual);
		});



		elementor.notifications.showToast({
			message: 'Campos da lista sincronizados!',
			type: 'success',
			timeout: 1500
		});


	}

	jQuery(document).ready(function ($) {
		const integrations = ['mailchimp', 'drip', 'active-campaign', 'getresponse', 'convertkit', 'mailerlite', 'clicksend'];

		integrations.forEach(function (integration) {
			elementor.channels.editor.on(`alphaform:editor:load_data_${integration}`, function () {
				alphaformLoadIntegrationFields(integration);
			});
		});
	});

})(jQuery);
