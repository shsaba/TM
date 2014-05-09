
$(document).ready(function() {

    var form = $('#form-valida'),
            formReport = $('#form-report'),
            formTaskReport = $('#form-task-report'),
            type = $('h1').attr('id');

    form.submit(function(e) {
        e.preventDefault();

        $('[class^="form-group"]').removeClass('has-error');
        $('#form-valida .form-group').removeClass('has-success');
        $(".list-errors").fadeOut('slow', function() {
            $(this).remove();
        });
        $(".success-message").remove();
        $('.row.alert').remove();


        $.ajax({
            type: 'POST',
            url: 'add-for-' + type,
            data: form.serialize(),
            error: function(xhr, status, error) {
                showMessage(getErrorMessage(xhr, status, error), 'success');
            },
            success: function(htmlResponse) {
                var obj = $.parseJSON(htmlResponse),
                        objCount = 0;
                for (_obj in obj)
                    objCount++;

                if (objCount !== 0) {
                    $.each(obj, function(key, value) {
                        $('#group-' + key).addClass('has-error');

                        $('#group-' + key).after('<div class="form-group list-errors"><div class="col-lg-12"><div class="bs-component"><div class="alert alert-dismissable alert-danger">' + value + '</div></div></div>');
                    });
                    $(".list-errors").hide().fadeIn();
                } else {
                    $('#form-valida .form-group').addClass('has-success');
                    $('#form-valida')[0].reset();

                    $('.form-group:first-child').before('<div class="form-group success-message"><div class="col-lg-12"><div class="bs-component"><div class="alert alert-dismissable alert-success">OK pour l\'enregistrement</div></div></div>');
                    $(".alert-success").hide().fadeIn();

                    $.ajax({
                        type: 'GET',
                        url: 'last-from-' + type,
                        error: function(xhr, status, error) {
                            showMessage(getErrorMessage(xhr, status, error) + type, 'danger');
                        },
                        success: function(htmlResponse) {
                            $('tr.danger').remove();
                            $('tbody').prepend(htmlResponse);
                            $('tr:eq(1)').hide().fadeIn();
                        }
                    });
                    return false;
                }
            }
        });

    });


    formReport.submit(function(e) {

        $('.row.alert').remove();
        $(formReport.val() + '.form-group').removeClass('has-success');
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: '#',
            data: formReport.serialize(),
            error: function(xhr, status, error) {
                showMessage(getErrorMessage(xhr, status, error), 'danger');
            },
            success: function() {
                showMessage('Notes éditées', 'success');
                $(formReport.val() + '.form-group').addClass('has-success');
            }
        });

    });

    formTaskReport.submit(function(e) {
        e.preventDefault();

        alert('test');

    });


    $(".delete-element").click(function() {

        $('.row.alert').remove();
        var idv = $(this).attr('value');

        $.ajax({
            type: 'POST',
            url: 'delete/' + type + '/' + idv,
            error: function(xhr, status, error) {
                showMessage(getErrorMessage(xhr, status, error), 'danger');
            },
            success: function() {
                $('#element-' + idv).closest('tr').fadeOut('slow', function() {
                    $(this).remove();
                    showMessage('Element ' + idv + ' supprimé', 'success');
                });
            }
        });
    });




    $("#dateofreport").datepicker({
        altField: "#datepicker",
        closeText: 'Fermer',
        prevText: 'Précédent',
        nextText: 'Suivant',
        currentText: 'Aujourd\'hui',
        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
        dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        onSelect: function() {
            var date = $(this).val(),
                    form = $('#reportmanager');

            if (date) {
                form.submit();
            }
        }
    });


    $('#dateofreport').click(function() {
        $("#dateofreport").datepicker();
    });
});


(function($) {

    showMessage = function(message, type) {
        $.ajax({
            type: 'POST',
            url: 'call-indicator',
            data: {messages: message,
                type: type},
            error: function() {
                alert('La page que vous avez demandée n’a pas été trouvée.');
            },
            success: function(htmlResponse) {
                $('.container.content').append(htmlResponse);
            }
        });
    };

    getErrorMessage = function(xhr, status, error, type) {
        var message = status + ' ' + xhr.status + ' ' + error;
        return message;
    };

    getConfirmationBox = function() {
        $.ajax({
            type: 'POST',
            url: 'call-confirmation-box',
            error: function() {
                alert('La page que vous avez demandée n’a pas été trouvée.');
            },
            success: function(htmlResponse) {
                $('body').append(htmlResponse);
            }
        });
    };


    getConfirmation = function() {
        $("#ui-dialog-confirm").dialog({
            modal: true,
            autoOpen: !1,
            width: 600,
            buttons: {
                "Oui": function() {
                    $(this).dialog("close");
                    alert('marche');
                    return 'true';
                },
                "Non": function() {
                    $(this).dialog("close");
                    alert('marche pas');
                    return 'false';
                }
            }
        });
    };
})(jQuery);