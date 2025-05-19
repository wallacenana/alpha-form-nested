document.addEventListener('DOMContentLoaded', function () {
	const form = document.getElementById('alpha-form-license-form');
	if (!form) return;

	const input = document.getElementById('alpha_form_license_key');
	const statusDisplay = document.getElementById('alpha_form_status_message');

	form.addEventListener('submit', async function (e) {
		e.preventDefault();
		const license = input.value.trim();

		statusDisplay.textContent = 'Validando...';

		try {
			const res = await fetch(alphaFormVars.ajaxurl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: new URLSearchParams({
					action: 'alpha_hook_trigger',
					license: license,
					nonce: alphaFormVars.nonce
				})
			});

			const data = await res.json();

			if (data.success) {
				location.reload(); // ✅ recarrega se for sucesso
			} else {
				statusDisplay.innerHTML = `❌ ${data.message || 'Licença inválida.'}`;
			}
		} catch (err) {
			statusDisplay.textContent = `❌ Erro: ${err.message}`;
		}
	});
});
