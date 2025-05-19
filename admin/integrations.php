<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';

$integrations = $wpdb->get_results("SELECT name, data, status FROM $table", OBJECT_K);

// Configurações base de cada integração
$services = [
    'facebook' => [
        'title' => 'Facebook Pixel',
        'fields' => [
            'pixel_id' => [
                'label' => 'Pixel ID',
                'type' => 'text',
                'placeholder' => 'Ex: 123456789012345'
            ]
        ],
        'help' => 'https://www.facebook.com/events_manager2/list/pixel/'
    ],
    'analytics' => [
        'title' => 'Google Analytics',
        'fields' => [
            'measurement_id' => [
                'label' => 'Measurement ID (GA4)',
                'type' => 'text',
                'placeholder' => 'Ex: G-XXXXXXXXXX'
            ]
        ],
        'help' => 'https://support.google.com/analytics/answer/9539598'
    ],
    'active-campaign' => [
        'title' => 'ActiveCampaign',
        'fields' => [
            'api_url' => [
                'label' => 'API URL',
                'type' => 'text',
                'placeholder' => 'https://EXEMPLO.api-us1.com'
            ],
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'placeholder' => 'Chave secreta da API'
            ],
        ],
        'help' => 'https://help.activecampaign.com/hc/en-us/articles/207317590-Getting-started-with-the-API'
    ],
    'mailchimp' => [
        'title' => 'Mailchimp',
        'fields' => [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'placeholder' => 'Chave da API do Mailchimp'
            ],
            'server_prefix' => [
                'label' => 'Data Center',
                'type' => 'text',
                'placeholder' => 'Inicio do link no seu Mailchimp. Ex: us21, us5...'
            ],
        ],
        'help' => 'https://kb.mailchimp.com/integrations/api-integrations/about-api-keys'
    ],
    'drip' => [
        'title' => 'Drip',
        'fields' => [
            'api_key' => [
                'label' => 'API Token',
                'type' => 'password',
                'placeholder' => 'Token da API do Drip'
            ]
        ],
        'help' => 'http://kb.getdrip.com/general/where-can-i-find-my-api-token/'
    ],
    'getresponse' => [
        'title' => 'GetResponse',
        'fields' => [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'placeholder' => 'Chave da API do GetResponse'
            ]
        ],
        'help' => 'https://www.getresponse.com/'
    ],
    'convertkit' => [
        'title' => 'ConvertKit',
        'fields' => [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'placeholder' => 'Chave da API do ConvertKit'
            ]
        ],
        'help' => 'https://app.convertkit.com/account/edit'
    ],
    'mailerlite' => [
        'title' => 'MailerLite',
        'fields' => [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'placeholder' => 'Chave da API do MailerLite'
            ]
        ],
        'help' => 'https://help.mailerlite.com/article/show/35040-where-can-i-find-the-api-key'
    ],
    'clicksend' => [
        'title' => 'ClickSend',
        'fields' => [
            'username' => [
                'label' => 'Usuário',
                'type' => 'text',
                'placeholder' => 'Seu nome de usuário ClickSend'
            ],
            'api_key' => [
                'label' => 'API Key',
                'type' => 'password',
                'placeholder' => 'Chave da API do ClickSend'
            ]
        ],
        'help' => 'https://dashboard.clicksend.com/#/account/subaccount/api-credentials'
    ],

];
?>

<div class="wrap alpha-integrations">
    <h1>Integrações – Alpha Form</h1>
    <form>
        <?php foreach ($services as $name => $config):
            $row = $integrations[$name] ?? (object)['data' => '{}', 'status' => 0];
            $data = json_decode($row->data ?? '{}', true);
            $status = intval($row->status ?? 0);
            $status_msg = $status ? '✅ Ativa' : '❌ Inativa';
        ?>

            <h2><?= esc_html($config['title']) ?> </h2>
            <?php if (!empty($config['help'])): ?>
                <span>
                    <a href="<?= esc_url($config['help']) ?>" target="_blank" style="text-decoration: none;">
                        🔗 Ajuda
                    </a>
                </span>
            <?php endif; ?>
            <div class="alpha-integration-block">
                <table class="form-table">
                    <?php foreach ($config['fields'] as $field => $field_config):
                        $value = esc_attr($data[$field] ?? '');
                        $type = $field_config['type'] ?? 'text';
                        $id = $name . '_' . $field;
                    ?>
                        <tr>
                            <th scope="row"><?= esc_html($field_config['label']) ?></th>
                            <td>
                                <input
                                    type="<?= esc_attr($type) ?>"
                                    id="<?= esc_attr($id) ?>"
                                    data-name="<?= esc_attr($name) ?>"
                                    name="<?= esc_attr($field) ?>"
                                    value="<?= $value ?>"
                                    placeholder="<?= esc_attr($field_config['placeholder']) ?>" style="min-width: 350px;" />

                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <th scope="row">Status</th>
                        <td>
                            <button type="button"
                                id="validate_<?= $name ?>_btn"
                                class="button validate-integration"
                                data-name="<?= $name ?>">
                                Validar Conexão
                            </button>
                            <span id="<?= $name ?>_status_msg"><?= $status_msg ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            <hr>
        <?php endforeach; ?>
        <button type="submit" class="button validate-integration" id="save-integrations">Salvar</button>
    </form>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validação individual
        document.querySelectorAll('.validate-integration').forEach(button => {
            button.addEventListener('click', async () => {
                const name = button.dataset.name;
                const inputs = document.querySelectorAll(`input[data-name="${name}"]`);
                const statusSpan = document.getElementById(`${name}_status_msg`);

                const data = {
                    action: 'alpha_form_validate_integration',
                    integration: name,
                    nonce: alphaFormVars.nonce,
                };

                inputs.forEach(input => {
                    data[input.name] = input.value.trim();
                });

                statusSpan.textContent = 'Validando...';

                const res = await fetch(alphaFormVars.ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(data)
                });

                const json = await res.json();
                statusSpan.textContent = json.success ? '✅ Conexão válida' : `❌ ${json.message}`;
            });
        });

        // Salvamento de todas as integrações
        const saveAllBtn = document.getElementById('save-integrations');
        saveAllBtn?.addEventListener('click', async (e) => {
            e.preventDefault();

            const groupedData = {};

            document.querySelectorAll('input[data-name]').forEach(input => {
                const name = input.dataset.name;
                if (!groupedData[name]) groupedData[name] = {
                    action: 'alpha_form_save_integration',
                    integration: name,
                    nonce: alphaFormVars.nonce
                };

                groupedData[name][input.name] = input.value.trim();
            });

            for (const name in groupedData) {
                const res = await fetch(alphaFormVars.ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(groupedData[name])
                });

                const json = await res.json();
                const statusSpan = document.getElementById(`${name}_status_msg`);
                if (statusSpan) {
                    statusSpan.textContent = json.success ? '✅ Salvo com sucesso' : `❌ ${json.message}`;
                }
            }
        });
    });
</script>