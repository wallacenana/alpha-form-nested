console.log("Alpha Forms iniciado")
document.addEventListener('DOMContentLoaded', function () {
	if (typeof elementor !== 'undefined') {
		elementor.on('preview:loaded', function () {
			const iframe = elementor.$preview[0];
			const previewDocument = iframe.contentDocument || iframe.contentWindow.document;

			if (!previewDocument) return;

			const observer = new MutationObserver(() => {
				const accordions = previewDocument.querySelectorAll('.alpha-f-n');
				if (accordions.length > 0) {

					// Aqui roda sua lógica só uma vez
					accordions.forEach((accordion) => {
						const items = accordion.querySelectorAll('.alpha-f-n-item');

						items.forEach(item => {
							const summary = item.querySelector('.alpha-f-n-item-title');

							if (summary) {
								summary.addEventListener('click', () => {
									// Fecha os outros
									items.forEach(i => {
										if (i !== item) {
											i.removeAttribute('open');
										}
									});

									// Toggle o atual
									if (item.hasAttribute('open')) {
										item.removeAttribute('open');
									} else {
										item.setAttribute('open', 'true');
									}
								});
							}
						});
					});

					observer.disconnect(); // para de observar depois que encontrar
				}
			});

			observer.observe(previewDocument.body, {
				childList: true,
				subtree: true,
			});
		});
	}
});
