jQuery(document).ready(function ($) {
    const $modal = $('#servicesCtaModal');
    const $form = $('#servicesCtaForm');
    const $contact = $('#servicesCtaContact');
    const $message = $('#servicesCtaMessage');
    const $honeypot = $('#servicesCtaWebsite');
    const $submitBtn = $('#servicesCtaSubmitBtn');
    const $alert = $('#servicesCtaModalMessage');

    if (!$modal.length || typeof servicesCtaData === 'undefined') {
        return;
    }

    function trackGoal(goal) {
        if (!goal || typeof ym !== 'function' || !servicesCtaData.metrika_id) {
            return;
        }

        ym(servicesCtaData.metrika_id, 'reachGoal', goal);
    }

    function resetForm() {
        $form[0].reset();
        $alert.addClass('d-none').removeClass('alert-success alert-danger').text('');
        $submitBtn.prop('disabled', false).text('Отправить заявку');
    }

    $('.services-cta__open-btn').on('click', function () {
        trackGoal(servicesCtaData.goal_open);
        resetForm();
    });

    $submitBtn.on('click', function () {
        const contact = $contact.val().trim();
        const message = $message.val().trim();

        $alert.addClass('d-none');

        if (contact.length < 3) {
            $alert.removeClass('d-none alert-success').addClass('alert-danger');
            $alert.text('Укажите, как с вами связаться.');
            $contact.trigger('focus');
            return;
        }

        if (message.length < 10) {
            $alert.removeClass('d-none alert-success').addClass('alert-danger');
            $alert.text('Опишите задачу чуть подробнее.');
            $message.trigger('focus');
            return;
        }

        $submitBtn.prop('disabled', true).text('Отправка...');

        $.ajax({
            url: servicesCtaData.ajax_url,
            type: 'POST',
            data: {
                action: 'submit_services_cta',
                nonce: servicesCtaData.nonce,
                contact: contact,
                message: message,
                website: $honeypot.val(),
                source_url: window.location.href,
            },
            success: function (response) {
                if (response.success) {
                    trackGoal(servicesCtaData.goal_submit);

                    $alert.removeClass('d-none alert-danger').addClass('alert-success');
                    $alert.text(response.data.message || 'Заявка отправлена.');

                    setTimeout(function () {
                        bootstrap.Modal.getOrCreateInstance($modal[0]).hide();
                        resetForm();
                    }, 1800);
                } else {
                    $alert.removeClass('d-none alert-success').addClass('alert-danger');
                    $alert.text(response.data || 'Не удалось отправить. Попробуйте ещё раз.');
                    $submitBtn.prop('disabled', false).text('Отправить заявку');
                }
            },
            error: function () {
                $alert.removeClass('d-none alert-success').addClass('alert-danger');
                $alert.text('Ошибка сети. Попробуйте ещё раз.');
                $submitBtn.prop('disabled', false).text('Отправить заявку');
            },
        });
    });

    $modal.on('hidden.bs.modal', resetForm);

    $form.on('keydown', function (e) {
        if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            $submitBtn.trigger('click');
        }
    });
});
