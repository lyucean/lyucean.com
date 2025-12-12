<!-- Блок с телеграм-каналом -->
<div class="telegram-channel-post mb-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="telegram-block p-4">
                    <div class="row align-items-center g-3">
                        <!-- Иконка слева -->
                        <div class="col-auto d-none d-md-block">
                            <div class="telegram-icon-wrapper">
                                🍳
                            </div>
                        </div>
                        
                        <!-- Контент по центру -->
                        <div class="col">
                            <h3 class="telegram-title mb-2">
                                <span class="d-md-none">🍳 </span>Запустил телеграм-канал 
                                <a href="https://t.me/cio_kitchen" target="_blank" class="channel-link">CIO Kitchen</a>
                            </h3>
                            <p class="telegram-description mb-0">
                                Анонсы статей • Запуск проектов • Болталка
                            </p>
                        </div>
                        
                        <!-- Кнопка справа -->
                        <div class="col-12 col-md-auto text-center text-md-start">
                            <a href="https://t.me/cio_kitchen" target="_blank" class="btn btn-primary telegram-btn">
                                <i class="bi bi-telegram"></i> Подписаться
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Основной блок */
.telegram-block {
    border-radius: 0.375rem;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid var(--bs-border-color) !important;
    background-color: var(--bs-body-bg);
    color: var(--bs-body-color);
    font-size: 1.125rem;
    box-shadow: -1px 5px 35px -9px rgba(0, 0, 0, .05);
}

.telegram-block:hover {
    box-shadow: -1px 5px 35px -9px rgba(0, 0, 0, .15);
    border-color: var(--bs-primary);
}

/* Иконка */
.telegram-icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    background: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 50%;
}

/* Заголовок */
.telegram-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    color: var(--bs-body-color);
}

.channel-link {
    color: var(--bs-primary);
    text-decoration: none;
    transition: color 0.2s ease;
}

.channel-link:hover {
    color: var(--bs-primary);
    text-decoration: underline;
}

/* Описание */
.telegram-description {
    font-size: 0.95rem;
    color: var(--bs-secondary);
}

/* Кнопка */
.telegram-btn {
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.telegram-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.3);
}

/* Темная тема */
[data-bs-theme="dark"] .telegram-block {
    border-color: var(--bs-gray-700);
    background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.08) 0%, transparent 100%);
}

[data-bs-theme="dark"] .telegram-block:hover {
    border-color: var(--bs-primary);
}

[data-bs-theme="dark"] .telegram-icon-wrapper {
    background: rgba(var(--bs-primary-rgb), 0.2);
}

/* Адаптивность */
@media screen and (max-width: 768px) {
    .telegram-block {
        padding: 1.25rem !important;
    }

    .telegram-title {
        font-size: 1.1rem;
    }

    .telegram-description {
        font-size: 0.9rem;
    }

    .telegram-btn {
        width: 100%;
        padding: 0.65rem 1rem;
    }
}

@media screen and (max-width: 576px) {
    .telegram-block {
        padding: 1rem !important;
    }

    .telegram-title {
        font-size: 1rem;
    }

    .telegram-description {
        font-size: 0.85rem;
    }
}
</style>