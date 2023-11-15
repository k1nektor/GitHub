jQuery(document).ready(function($) {
    // Функція для завантаження та відновлення термінів
    function updateTermsSelect(taxonomy, selectedTerms) {
        if (!taxonomy) return;

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                'action': 'get_terms_for_taxonomy',
                'taxonomy': taxonomy
            },
            success: function(response) {
                if (response.success) {
                    var termsSelect = $('.elementor-control-terms select');
                    termsSelect.empty();
                    $.each(response.data, function(id, term) {
                        termsSelect.append($('<option>', {
                            value: term.term_id,
                            text: term.name,
                            selected: selectedTerms.includes(term.term_id.toString())
                        }));
                    });
                } else {
                    console.error('No terms found for taxonomy:', taxonomy);
                }
            },
            error: function(error) {
                console.error('AJAX error:', error);
            }
        });
    }

    // Оновлення термінів при зміні таксономії
    $(document).on('change', '.elementor-control-taxonomy select', function() {
        var taxonomy = $(this).val();
        updateTermsSelect(taxonomy, []);
    });

    // Відновлення вибраних термінів при ініціалізації віджета
    elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
        var widgetType = model.get('widgetType');
        if (widgetType === 'my_custom_widget') {
            var settings = model.get('settings'),
                selectedTaxonomy = settings.get('taxonomy'),
                selectedTerms = settings.get('terms') || [];

            if (selectedTaxonomy) {
                updateTermsSelect(selectedTaxonomy, selectedTerms);
            }
        }
    });
});
