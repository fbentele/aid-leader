(function ($) {
    'use strict';
    $(document).ready(function () {
        var debug = true;
        $('#aid-sync-midata').click(function () {
            log('start sync');
            $('#ajax-response').append('<h2>Synchronisation läuft...</h2>');
            $.post(
                ajaxurl,
                {
                    'action': 'sync_midata',
                    'data': 'do-stuff'
                },
                function (response) {
                    response = JSON.parse(response);
                    $('#ajax-response').append('Resultat enthält ' + response.size + ' Personen.');
                    $('#ajax-response').append('<h2>Neu erstellt</h2>');
                    $.each(response.created, function (index, item) {
                        log(item);
                        $('#ajax-response').append('<p>' + item.first_name + ' ' + item.last_name + ' (' + item.nickname + ')</p>');
                    });
                    $('#ajax-response').append('<h2>Aktualisiert</h2>');
                    $.each(response.updated, function (index, item) {
                        log(item);
                        $('#ajax-response').append('<p>' + item.first_name + ' ' + item.last_name + ' (' + item.nickname + ')</p>');
                    });
                    log('end sync');
                }
            );
        });

        function log(text) {
            if (debug) {
                console.log(text);
            }
        }
    });
})(jQuery);
