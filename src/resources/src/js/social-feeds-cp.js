// ==========================================================================

// Social Feeds Plugin for Craft CMS
// Author: Verbb - https://verbb.io/

// ==========================================================================

if (typeof Craft.SocialFeeds === typeof undefined) {
    Craft.SocialFeeds = {};
}

(function($) {

$(document).on('click', '[data-refresh-settings]', function(e) {
    e.preventDefault();

    const $btn = $(this);
    const $container = $btn.parent().parent();
    const $select = $container.find('select');
    const source = $btn.data('source');
    const setting = $btn.data('refresh-settings');

    const data = {
        source: source,
        setting: setting,
    }

    const setError = function(text) {
        let $error = $container.find('.sf-error');

        if (!text) {
            $error.remove();
        }

        if (!$error.length) {
            $error = $('<div class="sf-error error"></div>').appendTo($container);
        }

        $error.html(text);
    }

    const setSelect = function(values) {
        let currentValue = $select.val();
        let options = '';

        $.each(values, (key, option) => {
            options += '<option value="' + option.value + '">' + option.label + '</option>';
        });

        $select.html(options);

        // Set any original value back
        if (currentValue) {
            $select.val(currentValue);
        }
    }

    $btn.addClass('sf-loading sf-loading-sm');

    setError(null);

    Craft.sendActionRequest('POST', 'social-feeds/sources/refresh-settings', { data })
        .then((response) => {
            if (response.data.error) {
                let errorMessage = Craft.t('social-feeds', 'An error occurred.');

                if (response.data.error) {
                    errorMessage += `<br><code>${response.data.error}</code>`;

                    if (response.data.file && response.data.line) {
                        errorMessage += `<br><code>${response.data.file}:${response.data.line}</code>`;
                    }
                }

                setError(errorMessage)

                return;
            }

            setSelect(response.data);
        })
        .catch((error) => {
            let errorMessage = error;

            if (error.response && error.response.data && error.response.data.error) {
                errorMessage += `<br><code>${error.response.data.error}</code>`;

                if (error.response.data.file && error.response.data.line) {
                    errorMessage += `<br><code>${error.response.data.file}:${error.response.data.line}</code>`;
                }
            }

            setError(errorMessage);
        })
        .finally(() => {
            $btn.removeClass('sf-loading sf-loading-sm');
        });
});

function toggleLightswitches(el, toggleGroup) {
    const $switch = $(el).find('.lightswitch');
    const lightswitch = $switch.data('lightswitch');
    const $allGroups = $('.sf-switch-group[data-toggle-group="' + toggleGroup + '"]')

    $allGroups.each(function(index, element) {
        $(element).removeClass('disabled');
        
        if (element !== el) {
            if (lightswitch.on) {
                $(element).addClass('disabled');
                $(element).find('.lightswitch').data('lightswitch').turnOff();
            }
        }
    });
}

$(document).on('click', '.sf-switch-group', function(e) {
    e.preventDefault();

    const $switch = $(this).find('.lightswitch');
    const toggleGroup = $(this).data('toggle-group');

    if ($switch.length) {
        const lightswitch = $switch.data('lightswitch');

        if (lightswitch) {
            lightswitch.toggle();

            // Disable all others in the group
            if (toggleGroup) {
                toggleLightswitches(e.currentTarget, toggleGroup);
            }
        }
    }
});

Garnish.$doc.ready(() => {
    $('.sf-switch-group[data-toggle-group] .lightswitch.on').each(function(index, element) {
        const $btn = $(this).parents('.sf-switch-group');
        const toggleGroup = $btn.data('toggle-group');

        toggleLightswitches($btn[0], toggleGroup);
    });
});

$(document).on('click', '.sf-refresh-btn', function(e) {
    e.preventDefault();

    const $btn = $(this);
    const $container = $('.sf-feed-preview');

    $btn.addClass('sf-loading sf-loading-sm');

    const setError = function(text) {
        let $error = $container.find('.sf-error');

        if (!text) {
            $error.remove();
        }

        if (!$error.length) {
            $error = $('<div class="sf-error error"></div>').prependTo($container);
        }

        $error.html(text);
    }

    var postData = Garnish.getPostData($('#main-form')),
        params = Craft.expandPostArray(postData);

    const data = {
        sources: params.sources,
    };

    setError(null);

    Craft.sendActionRequest('POST', 'social-feeds/feeds/preview', { data })
        .then((response) => {
            if (response.data.error) {
                let errorMessage = Craft.t('social-feeds', 'An error occurred.');

                if (response.data.error) {
                    errorMessage += `<br><code>${response.data.error}</code>`;

                    if (response.data.file && response.data.line) {
                        errorMessage += `<br><code>${response.data.file}:${response.data.line}</code>`;
                    }
                }

                setError(errorMessage)

                return;
            }

            $container.html(response.data);
        })
        .catch((error) => {
            let errorMessage = error;

            if (error.response && error.response.data && error.response.data.error) {
                errorMessage += `<br><code>${error.response.data.error}</code>`;

                if (error.response.data.file && error.response.data.line) {
                    errorMessage += `<br><code>${error.response.data.file}:${error.response.data.line}</code>`;
                }
            }

            setError(errorMessage);
        })
        .finally(() => {
            $btn.removeClass('sf-loading sf-loading-sm');
        });

});

})(jQuery);
