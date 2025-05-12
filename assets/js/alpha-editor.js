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


