
$(document).ready(function() {
    var form = $('#form-valida');

    form.submit(function(e) {
        e.preventDefault();
        $('[class^="form-group"]').removeClass('has-error');
        $(".list-errors").fadeOut('slow', function() {
            $(this).remove();
        });
        $(".alert-success").remove();
        $('#form-valida .form-group').removeClass('has-success');
        $.ajax({
            type: 'POST',
            url: '#',
            data: form.serialize(),
            error: function(jqXHR, textStatus, errorThrown) {
                $('#conent').html('Erreur...');
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
                    $('.form-group:first-child').before('<div class="form-group"><div class="col-lg-12"><div class="bs-component"><div class="alert alert-dismissable alert-success">OK pour l\'enregistrement</div></div></div>');
                    alert('Enregistrement');
                    $(".alert-success").hide().fadeIn();



                    $.ajax({
                        type: 'GET',
                        url: 'get-last-category',
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('#conent').html('Erreur...');
                        },
                        success: function(htmlResponse) {
                            $('tbody').prepend(htmlResponse);
                            $('tr:eq(1)').hide().fadeIn();



                        }
                    });


                    return false;
                }
            }
        });

    });
});
