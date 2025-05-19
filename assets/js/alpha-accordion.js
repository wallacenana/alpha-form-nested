function initAlphaAccordion(container) {
	const accordions = container.querySelectorAll('.alpha-f-n');

	accordions.forEach((accordion) => {
		const items = accordion.querySelectorAll('.alpha-f-n-item');

		items.forEach(item => {
			if (item) {
				item.addEventListener('click', (e) => {
					const titu = e.target.closest('.alpha-f-n-item-title');

					// Só executa se o clique foi na própria .alpha-f-n-item-title, não em filhos
					if (!titu || e.target !== titu) return;

					const wrapper = titu.closest('.alpha-f-n-item');
					if (wrapper) {
						wrapper.toggleAttribute('open');
					}
				});
			}

			item.setAttribute('data-alpha-init', '1'); // marca apenas esse item
		});

		// Abre o primeiro não inicializado se nenhum tiver open
		const anyOpen = accordion.querySelector('.alpha-f-n-item[open]');
		const firstUninitialized = accordion.querySelector('.alpha-f-n-item:not([data-alpha-init])');
		if (!anyOpen && firstUninitialized) {
			firstUninitialized.setAttribute('open', 'true');
		}
	});
}


elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
	if (model.attributes.widgetType === 'alpha-form') {

		// Espera o preview atualizar (pequeno delay)
		setTimeout(() => {
			const iframe = elementor.$preview?.[0];
			const previewDocument = iframe?.contentDocument || iframe?.contentWindow?.document;
			if (!previewDocument) return;

			initAlphaAccordion(previewDocument);
		}, 300);
	}
});


