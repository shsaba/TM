
$(document).ready(function() {
    var form = $('#form-valida'),
            type = $('h1').attr('id');

    form.submit(function(e) {
        e.preventDefault();
        $('[class^="form-group"]').removeClass('has-error');
        $(".list-errors").fadeOut('slow', function() {
            $(this).remove();
        });
        $(".success-message").remove();
        $('.row.alert').remove();



        $('#form-valida .form-group').removeClass('has-success');
        $.ajax({
            type: 'POST',
            url: '',
            data: form.serialize(),
            error: function(xhr, status, error) {
                showMessage(getErrorMessage(xhr, status, error), 'danger');
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
                        url: 'get-last-' + type,
                        error: function(xhr, status, error) {
                            showMessage(getErrorMessage(xhr, status, error), 'danger');
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
})(jQuery);