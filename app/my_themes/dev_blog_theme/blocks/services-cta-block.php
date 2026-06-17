<?php
/**
 * Баннер «Проекты и консалтинг»: заявка через модалку → Telegram.
 *
 * @package dev_blog_theme
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @param string $contact    .
 * @param string $message    .
 * @param string $source_url .
 * @param string $user_ip    .
 */
function dev_blog_schedule_services_cta_telegram($contact, $message, $source_url, $user_ip) {
    add_action(
        'shutdown',
        static function () use ($contact, $message, $source_url, $user_ip) {
            dev_blog_send_services_cta_telegram($contact, $message, $source_url, $user_ip);
        },
        999
    );
}

/**
 * @param string $contact    .
 * @param string $message    .
 * @param string $source_url .
 * @param string $user_ip    .
 */
function dev_blog_send_services_cta_telegram($contact, $message, $source_url, $user_ip) {
    $telegram_token = get_option('telegram_alert_token', null);
    $chat_id        = get_option('telegram_alert_chat_id', null);

    if (! $telegram_token || ! $chat_id) {
        error_log('Telegram: не удалось отправить заявку с баннера, токен или chat_id отсутствуют.');
        return;
    }

    $text  = "📩 *Новая заявка с баннера*\n\n";
    $text .= "*Контакт:*\n{$contact}\n\n";
    $text .= "*Задача:*\n{$message}\n\n";
    $text .= "Страница: {$source_url}\n";
    $text .= "IP: *{$user_ip}*";

    $url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";

    dev_blog_telegram_curl_post(
        $url,
        array(
            'chat_id'    => $chat_id,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        )
    );
}

/**
 * @param string $ip .
 */
function dev_blog_services_cta_rate_limit_ok($ip) {
    $key   = 'services_cta_' . md5($ip);
    $count = (int) get_transient($key);

    if ($count >= 5) {
        return false;
    }

    set_transient($key, $count + 1, HOUR_IN_SECONDS);

    return true;
}

function dev_blog_submit_services_cta() {
    check_ajax_referer('services_cta_request', 'nonce');

    if (! empty($_POST['website'])) {
        wp_send_json_error('Ошибка отправки.');
    }

    $contact = isset($_POST['contact']) ? sanitize_text_field(wp_unslash($_POST['contact'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    if (mb_strlen(trim($contact)) < 3) {
        wp_send_json_error('Укажите, как с вами связаться.');
    }

    if (mb_strlen(trim($message)) < 10) {
        wp_send_json_error('Опишите задачу чуть подробнее (от 10 символов).');
    }

    $user_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

    if ($user_ip !== '' && ! dev_blog_services_cta_rate_limit_ok($user_ip)) {
        wp_send_json_error('Слишком много заявок. Попробуйте позже.');
    }

    $source_url = isset($_POST['source_url']) ? esc_url_raw(wp_unslash($_POST['source_url'])) : home_url('/');

    dev_blog_schedule_services_cta_telegram($contact, $message, $source_url, $user_ip);

    wp_send_json_success(
        array(
            'message' => 'Заявка отправлена. Свяжусь с вами в ближайшее время.',
        )
    );
}
add_action('wp_ajax_submit_services_cta', 'dev_blog_submit_services_cta');
add_action('wp_ajax_nopriv_submit_services_cta', 'dev_blog_submit_services_cta');

function dev_blog_enqueue_services_cta_script() {
    $script_path = get_template_directory() . '/blocks/services-cta-block.js';

    if (! file_exists($script_path)) {
        return;
    }

    $version = date('Ymd.His', filemtime($script_path));

    wp_enqueue_script(
        'services-cta-script',
        get_template_directory_uri() . '/blocks/services-cta-block.js',
        array('jquery'),
        $version,
        true
    );

    wp_localize_script(
        'services-cta-script',
        'servicesCtaData',
        array(
            'ajax_url'     => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('services_cta_request'),
            'metrika_id'   => 89564639,
            'goal_open'    => 'services_cta_open',
            'goal_submit'  => 'services_cta_submit',
        )
    );
}
add_action('wp_enqueue_scripts', 'dev_blog_enqueue_services_cta_script');

/**
 * Модалка заявки — один раз на страницу.
 */
function dev_blog_render_services_cta_modal() {
    static $rendered = false;

    if ($rendered) {
        return;
    }

    $rendered = true;
    ?>
    <div class="modal fade services-cta-modal" id="servicesCtaModal" tabindex="-1" aria-labelledby="servicesCtaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button type="button"
                        class="btn-close services-cta-modal__close"
                        data-bs-dismiss="modal"
                        aria-label="Закрыть"></button>
                <div class="modal-body services-cta-modal__body">
                    <p class="services-cta-modal__eyebrow">Открыт к проектам</p>
                    <h2 class="services-cta-modal__title" id="servicesCtaModalLabel">Обсудить задачу</h2>
                    <p class="services-cta-modal__intro">
                        Оставьте контакт и кратко опишите задачу. Отвечу лично - как удобнее связаться.
                    </p>
                    <form id="servicesCtaForm" novalidate>
                        <div class="visually-hidden" aria-hidden="true">
                            <label for="servicesCtaWebsite">Сайт</label>
                            <input type="text" id="servicesCtaWebsite" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="services-cta-modal__field">
                            <label for="servicesCtaContact" class="services-cta-modal__label">Как с вами связаться</label>
                            <div class="services-cta-modal__input-wrap">
                                <i class="bi bi-person-lines-fill services-cta-modal__input-icon" aria-hidden="true"></i>
                                <input type="text"
                                       class="form-control services-cta-modal__input"
                                       id="servicesCtaContact"
                                       name="contact"
                                       required
                                       autocomplete="tel"
                                       placeholder="+7..., @username или email">
                            </div>
                        </div>
                        <div class="services-cta-modal__field">
                            <label for="servicesCtaMessage" class="services-cta-modal__label">Что нужно</label>
                            <div class="services-cta-modal__input-wrap services-cta-modal__input-wrap--textarea">
                                <i class="bi bi-chat-left-text services-cta-modal__input-icon" aria-hidden="true"></i>
                                <textarea class="form-control services-cta-modal__input services-cta-modal__textarea"
                                          id="servicesCtaMessage"
                                          name="message"
                                          rows="4"
                                          required
                                          placeholder="Аудит, стратегия, part-time CIO - в двух словах"></textarea>
                            </div>
                        </div>
                        <div id="servicesCtaModalMessage" class="alert d-none services-cta-modal__alert" role="alert"></div>
                        <div class="services-cta-modal__actions">
                            <button type="button" class="btn btn-primary services-cta-modal__submit" id="servicesCtaSubmitBtn">
                                Отправить заявку
                            </button>
                            <button type="button" class="btn btn-link services-cta-modal__cancel" data-bs-dismiss="modal">
                                Не сейчас
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}
