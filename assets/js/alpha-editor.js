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
		if (!['alpha-next', 'alpha-prev'].includes(model.get('widgetType'))) return;

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