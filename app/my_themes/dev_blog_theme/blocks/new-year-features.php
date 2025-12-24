<?php
// ============================================================================
// НОВОГОДНИЕ ФИШКИ 🎄
// Чтобы отключить - закомментируйте строку require_once в functions.php
// ============================================================================

// Функция для отправки пожеланий в Telegram
function send_telegram_wishes($wishes, $user_ip) {
    // Используем те же настройки, что и для feedback
    $telegram_token = get_option('telegram_alert_token', null);
    $chat_id = get_option('telegram_alert_chat_id', null);
    
    if (!$telegram_token || !$chat_id) {
        error_log("Telegram: не удалось отправить пожелание, так как токен или chat_id отсутствуют.");
        return;
    }
    
    // Формируем сообщение для Telegram
    $message = "🎄 *Новогоднее пожелание!*\n\n";
    $message .= "Пожелание:\n{$wishes}\n\n";
    $message .= "IP пользователя: *{$user_ip}*";
    
    // URL для отправки сообщения через Telegram Bot API
    $url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";
    
    // Параметры запроса
    $args = array(
        'body' => array(
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ),
    );
    
    // Отправляем запрос
    wp_remote_post($url, $args);
}

// AJAX обработчик для отправки пожеланий
function send_new_year_wishes() {
    if (!isset($_POST['wishes']) || empty(trim($_POST['wishes']))) {
        wp_send_json_error('Пожалуйста, введите пожелание');
    }
    
    $wishes = sanitize_textarea_field($_POST['wishes']);
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    // Отправляем в Telegram
    send_telegram_wishes($wishes, $user_ip);
    
    wp_send_json_success(array(
        'message' => 'И тебе всего прекрасного! 🎉'
    ));
}
add_action('wp_ajax_send_new_year_wishes', 'send_new_year_wishes');
add_action('wp_ajax_nopriv_send_new_year_wishes', 'send_new_year_wishes');

function activate_new_year_features() {
    // Добавляем стили
    add_action('wp_enqueue_scripts', function() {
        wp_add_inline_style('dev_blog_theme-style', '
            /* Новогодняя иконка елки в логотипе */
            body.new-year-active header .logo-tree-icon {
                display: inline-block;
                animation: tree-shake 3s ease-in-out infinite;
                margin-right: 8px;
            }
            
            @keyframes tree-shake {
                0%, 100% { transform: rotate(0deg); }
                25% { transform: rotate(-5deg); }
                75% { transform: rotate(5deg); }
            }
            
            /* Легкий снег - везде кроме article-content */
            .snow-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 9999;
                overflow: hidden;
            }
            
            /* Исключаем снег в article-content */
            .article-content {
                position: relative;
                z-index: 10000;
            }
            
            .snowflake {
                position: absolute;
                color: rgba(255, 255, 255, 0.6);
                font-size: 1em;
                font-family: Arial, sans-serif;
                text-shadow: 0 0 3px rgba(255, 255, 255, 0.5);
                animation: snowfall linear infinite;
                top: -10px;
                user-select: none;
            }
            
            @keyframes snowfall {
                0% {
                    transform: translateY(0vh) translateX(0px) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 0.8;
                }
                90% {
                    opacity: 0.8;
                }
                100% {
                    transform: translateY(100vh) translateX(var(--snow-drift)) rotate(360deg);
                    opacity: 0;
                }
            }
            
            /* Новогодний баннер - мягкий праздничный стиль */
            .new-year-banner {
                background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 50%, #fff3e0 100%);
                color: #2e7d32;
                text-align: center;
                padding: 15px;
                font-size: 0.9rem;
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                border: 2px solid rgba(76, 175, 80, 0.2);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }
            
            .new-year-banner::before {
                content: "";
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 15px,
                    rgba(76, 175, 80, 0.03) 15px,
                    rgba(76, 175, 80, 0.03) 30px
                );
                animation: shimmer 8s linear infinite;
            }
            
            @keyframes shimmer {
                0% { transform: translateX(-50%) translateY(-50%) rotate(0deg); }
                100% { transform: translateX(-50%) translateY(-50%) rotate(360deg); }
            }
            
            .new-year-banner-content {
                position: relative;
                z-index: 1;
                font-weight: 500;
            }
            
            /* Кнопка в баннере */
            .new-year-banner .btn-success {
                background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
                border: none;
                color: white;
                font-weight: 600;
                padding: 5px 24px;
                width: -webkit-fill-available;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
                transition: all 0.3s ease;
            }
            
            .new-year-banner .btn-success:hover {
                background: linear-gradient(135deg, #43a047 0%, #4caf50 100%);
                box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
                transform: translateY(-1px);
            }
            
            [data-bs-theme="dark"] .new-year-banner {
                background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #388e3c 100%);
                color: #e8f5e9;
                border-color: rgba(129, 199, 132, 0.3);
            }
            
            [data-bs-theme="dark"] .new-year-banner::before {
                background: repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 15px,
                    rgba(129, 199, 132, 0.05) 15px,
                    rgba(129, 199, 132, 0.05) 30px
                );
            }
            
            @media (max-width: 768px) {
                .new-year-banner {
                    font-size: 0.8rem;
                    padding: 8px 10px;
                }
            }
        ');
    }, 99);
    
    // Добавляем класс к body
    add_filter('body_class', function($classes) {
        $classes[] = 'new-year-active';
        return $classes;
    });
    
    // Баннер после кнопки подписаться (в меню)
    add_action('after_telegram_button', function() {
        echo '<div class="new-year-banner mt-3">
            <div class="new-year-banner-content">
                 С Новым Годом!🥳
                 <br/>Пусть 2026ой будет продуктивным и управляемым!
            </div>
            <div class="text-center text-md-start mt-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newYearWishesModal">
                Ответить
            </button>
        </div>
        </div>';
        
        // Модалка для пожеланий
        echo '<div class="modal fade" id="newYearWishesModal" tabindex="-1" aria-labelledby="newYearWishesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newYearWishesModalLabel">Тут ты можешь оставить мне пожелание 🎄</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="newYearWishesForm">
                            <div class="mb-3">
                                <label for="wishesText" class="form-label">Твой текст:</label>
                                <textarea class="form-control" id="wishesText" rows="5" placeholder="Напиши что душа хочет..." required></textarea>
                            </div>
                            <div id="wishesMessage" class="alert d-none" role="alert"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="sendWishesBtn">Отправить</button>
                    </div>
                </div>
            </div>
        </div>';
    });
    
    // Подключаем скрипт для обработки формы
    add_action('wp_enqueue_scripts', function() {
        wp_enqueue_script('new-year-wishes', get_template_directory_uri() . '/blocks/new-year-wishes.js', array('jquery'), '1.0', true);
        wp_localize_script('new-year-wishes', 'newYearWishesData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    });
    
    // Снег и JavaScript (медленнее)
    add_action('wp_footer', function() {
        echo '<div class="snow-container" id="snowContainer"></div>';
        echo '<script>
        (function() {
            if (!document.getElementById("snowContainer")) return;
            const container = document.getElementById("snowContainer");
            const snowflakes = ["❄", "❅", "❆"];
            const count = window.innerWidth > 768 ? 30 : 15;
            
            function createSnowflake() {
                const snowflake = document.createElement("div");
                snowflake.className = "snowflake";
                snowflake.textContent = snowflakes[Math.floor(Math.random() * snowflakes.length)];
                snowflake.style.left = Math.random() * 100 + "%";
                // Медленнее падение: 8-12 секунд вместо 2-5
                snowflake.style.animationDuration = (Math.random() * 4 + 8) + "s";
                snowflake.style.animationDelay = Math.random() * 2 + "s";
                snowflake.style.setProperty("--snow-drift", (Math.random() * 50 - 25) + "px");
                snowflake.style.fontSize = (Math.random() * 0.5 + 0.8) + "em";
                container.appendChild(snowflake);
                
                // Увеличиваем время жизни снежинки
                setTimeout(() => {
                    snowflake.remove();
                    createSnowflake();
                }, 12000);
            }
            
            for (let i = 0; i < count; i++) {
                setTimeout(() => createSnowflake(), i * 300);
            }
        })();
        </script>';
    });
}

// Активируем новогодние фишки
activate_new_year_features();

