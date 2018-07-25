
function add_site() {
    $('#site_type_add.ui.dropdown').dropdown({
        on: 'click'
    });
    
    $('#add_site_btn').click(function () {
        $('#add_site.ui.modal').modal('setting', {
            autofocus: false,
            inverted: true,
            closable: false
        });
        $('#add_site.ui.modal').modal('show');
    });

    $('#submit_site').click(function (e) {
        e.preventDefault();
        $('#server_error_message').hide();
        $('#message_error').hide();
        $('#message_success').hide();
        $('#error_name_message').hide();
        $('#error_name_message_edit').hide();
        $('#add_site_form.ui.form').submit();
    });
    $('#add_site_form.ui.form')
            .form({
                fields: {
                    t_number: {
                        identifier: 't-number',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le T-number"
                            }
                        ]
                    },
                    site_name: {
                        identifier: 'site-name',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le nom du site"
                            }
                        ]
                    },
                    site_latitude: {
                        identifier: 'site-latitude',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir la latitude du site"
                            }
                        ]
                    },
                    site_longitude: {
                        identifier: 'site-longitude',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir la longitude du site"
                            }
                        ]
                    },
                    site_type: {
                        identifier: 'site-type',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez selectionner la type du site"
                            }
                        ]
                    }  
                },
                inline: true,
                on: 'change',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'post',
                        url: Routing.generate('site_add'),
                        data: $('#add_site_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#submit_site').addClass('disabled');
                            $('#cancel_add_site').addClass('disabled');
                            $('#add_site_form.ui.form').addClass('loading');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#server_error_message').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#error_name_header').html("Echec de la validation");
                                    $('#error_name_list').html('<li>' + myerrors.message + '</li>');
                                    $('#error_name_message').show();
                                } else {
                                    $('#error_name_header').html("Echec de la validation. Veuillez verifier vos données");
                                    $('#error_name_message').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#cancel_add_site').removeClass('disabled');
                            $('#submit_site').removeClass('disabled');
                            $('#add_site_form.ui.form').removeClass('loading');
                            $('#add_site.ui.modal').modal('hide');
                            $('#message_success>div.header').html(response.message);
                            $('#message_success').show();
                            window.location.replace(Routing.generate('sites_home'));
                            setTimeout(function () {
                                $('#message_success').hide();
                            }, 4000);

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#cancel_add_site').removeClass('disabled');
                            $('#submit_site').removeClass('disabled');
                            $('#add_site_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );
}


$(function () {
    add_site();
    $('#cancel_add_site').click(function () {
//        window.location.reload();
    });
});

function edit_site(id) {
    $('#message_error').hide();
    $('#message_success').hide();
    $('.ui.dropdown').dropdown('remove active');
    $('.ui.dropdown').dropdown('remove visible');
    $('.ui.dropdown>div.menu').removeClass('visible');
    $('.ui.dropdown>div.menu').addClass('hidden');
    $('.ui.dropdown').dropdown({
        on: 'hover'
    });
    $.ajax({
        type: 'PUT',
        url: Routing.generate('site_update', {id: id}),
        dataType: 'json',
        beforeSend: function () {
            $('#message_loading').show();
        },
        statusCode: {
            500: function (xhr) {

            },
            404: function (response, textStatus, jqXHR) {
                $('#message_error>div.header').html(response.responseJSON.message);
                $('#message_error').show();
            }
        },
        success: function (response, textStatus, jqXHR) {
            $('#edit_site').remove();
            $('#edit_site_content').html(response.edit_site_form);

            $('#edit_site.ui.modal').modal('setting', {
                autofocus: false,
                inverted: true,
                closable: false
            });
            
            $('.ui.dropdown').dropdown({
                on: 'click'
            });
            $('#cancel_edit_site').click(function () {
//                window.location.reload();
                $('#edit_site_content').html("");
            });
            $('#edit_site.ui.modal').modal('show');
            execute_edit(id);

            $('#message_loading').hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#message_loading').hide();
        }
    });
}

function execute_edit(id) {
    $('#submit_edit_site').click(function (e) {
        e.preventDefault();
        $('#server_error_message').hide();
        $('#message_error').hide();
        $('#message_success').hide();
        $('#error_name_message').hide();
        $('#error_name_message_edit').hide();
        $('#edit_site_form.ui.form').submit();
    });
    $('#edit_site_form.ui.form')
            .form({
                fields: {
                    t_number: {
                        identifier: 't-number',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le T-number"
                            }
                        ]
                    },
                    site_name: {
                        identifier: 'site-name',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir le nom du site"
                            }
                        ]
                    },
                    site_latitude: {
                        identifier: 'site-latitude',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir la latitude du site"
                            }
                        ]
                    },
                    site_longitude: {
                        identifier: 'site-longitude',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez saisir la longitude du site"
                            }
                        ]
                    },
                    site_type: {
                        identifier: 'site-type',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Veuillez selectionner la type du site"
                            }
                        ]
                    }
                    
                },
                inline: true,
                on: 'change',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'PUT',
                        url: Routing.generate('site_update', {id: id}),
                        data: $('#edit_site_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#submit_edit_site').addClass('disabled');
                            $('#cancel_edit_site').addClass('disabled');
                            $('#edit_site_form.ui.form').addClass('loading');
                            $('#cancel_details_site').addClass('disabled');
                            $('#disable_site').addClass('disabled');
                            $('#enable_site').addClass('disabled');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#server_error_message_edit').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#error_name_header_edit').html("Echec de la validation");
                                    $('#error_name_list_edit').html('<li>' + myerrors.message + '</li>');
                                    $('#error_name_message_edit').show();
                                } else {
                                    $('#error_name_header_edit').html("Echec de la validation. Veuillez verifier vos données");
                                    $('#error_name_message_edit').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#submit_edit_site').removeClass('disabled');
                            $('#cancel_edit_site').removeClass('disabled');
                            $('#edit_site_form.ui.form').removeClass('loading');
                            $('#cancel_details_site').removeClass('disabled');
                            $('#disable_site').removeClass('disabled');
                            $('#enable_site').removeClass('disabled');
                            $('#edit_site.ui.modal').modal('hide');
                            $('#message_success>div.header').html(response.message);
                            $('#message_success').show();
                            window.location.reload();
                            setTimeout(function () {
                                $('#message_success').hide();
                            }, 4000);
                            $('#edit_site').remove();


                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#submit_edit_site').removeClass('disabled');
                            $('#cancel_edit_site').removeClass('disabled');
                            $('#edit_site_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );
}

function assign_node(id) {
    $('#message_error').hide();
    $('#message_success').hide();
    $('.ui.dropdown').dropdown('remove active');
    $('.ui.dropdown').dropdown('remove visible');
    $('.ui.dropdown>div.menu').removeClass('visible');
    $('.ui.dropdown>div.menu').addClass('hidden');
    $('.ui.dropdown').dropdown({
        on: 'hover'
    });
    $.ajax({
        type: 'GET',
        url: Routing.generate('assign_node_get', {id: id}),
        dataType: 'json',
        beforeSend: function () {
            $('#message_loading').show();
        },
        statusCode: {
            500: function (xhr) {

            },
            404: function (response, textStatus, jqXHR) {
                $('#message_error>div.header').html(response.responseJSON.message);
                $('#message_error').show();
            }
        },
        success: function (response, textStatus, jqXHR) {
            $('#assign_node').remove();
            $('#edit_site_content').html(response.assign_node_form);

            $('#assign_node.ui.modal').modal('setting', {
                autofocus: false,
                inverted: true,
                closable: false
            });
            
            $('.ui.dropdown').dropdown({
                on: 'click'
            });
            $('#cancel_assign_node').click(function () {
//                window.location.reload();
                $('#edit_site_content').html("");
            });
            $('#assign_node.ui.modal').modal('show');
            execute_assign_node(id);

            $('#message_loading').hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#message_loading').hide();
        }
    });
}

function execute_assign_node(id) {
    $('#submit_assign_node').click(function (e) {
        e.preventDefault();
        $('#server_error_message').hide();
        $('#message_error').hide();
        $('#message_success').hide();
        $('#error_name_message').hide();
        $('#error_name_message_edit').hide();
        $('#assign_node_form.ui.form').submit();
    });
    $('#assign_node_form.ui.form')
            .form({
                fields: {
                    node: {
                        identifier: 'node',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Please select a nodal site"
                            }
                        ]
                    }
                },
                inline: true,
                on: 'change',
                onSuccess: function (event, fields) {
                    $.ajax({
                        type: 'PUT',
                        url: Routing.generate('assign_node_put', {id: id}),
                        data: $('#assign_node_form.ui.form').serialize(),
                        dataType: 'json',
                        processData: false,
                        //contentType: false,
                        cache: false,
                        beforeSend: function () {
                            $('#submit_assign_node').addClass('disabled');
                            $('#cancel_assign_node').addClass('disabled');
                            $('#assign_node_form.ui.form').addClass('loading');
                            $('#cancel_details_site').addClass('disabled');
                            $('#disable_site').addClass('disabled');
                            $('#enable_site').addClass('disabled');
                        },
                        statusCode: {
                            500: function (xhr) {
                                $('#server_error_message_edit').show();
                            },
                            400: function (response, textStatus, jqXHR) {
                                var myerrors = response.responseJSON;
                                if (myerrors.success === false) {
                                    $('#error_name_header_edit').html("Echec de la validation");
                                    $('#error_name_list_edit').html('<li>' + myerrors.message + '</li>');
                                    $('#error_name_message_edit').show();
                                } else {
                                    $('#error_name_header_edit').html("Echec de la validation. Veuillez verifier vos données");
                                    $('#error_name_message_edit').show();
                                }

                            }
                        },
                        success: function (response, textStatus, jqXHR) {
                            $('#submit_assign_node').removeClass('disabled');
                            $('#cancel_assign_node').removeClass('disabled');
                            $('#assign_node_form.ui.form').removeClass('loading');
                            $('#assign_node.ui.modal').modal('hide');
                            $('#message_success>div.header').html(response.message);
                            $('#message_success').show();
                            window.location.reload();
                            setTimeout(function () {
                                $('#message_success').hide();
                            }, 4000);
                            $('#assign_node').remove();


                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#submit_assign_node').removeClass('disabled');
                            $('#cancel_assign_node').removeClass('disabled');
                            $('#assign_node_form.ui.form').removeClass('loading');
                        }
                    });
                    return false;
                }
            }
            );
}



function delete_site(id) {
    $('#confirm_delete_site.ui.modal')
            .modal('show')
            ;

    $('#execute_delete_site').click(function (e) {
        e.preventDefault();
        $('#confirm_disabe_site.ui.modal')
                .modal('hide')
                ;
        $('#message_error').hide();
        $('#message_success').hide();
        $('.ui.dropdown').dropdown('remove active');
        $('.ui.dropdown').dropdown('remove visible');
        $('.ui.dropdown>div.menu').removeClass('visible');
        $('.ui.dropdown>div.menu').addClass('hidden');
        $('.ui.dropdown').dropdown({
            on: 'hover'
        });
        $.ajax({
            type: 'DELETE',
            url: Routing.generate('site_delete', {id: id}),
            dataType: 'json',
            beforeSend: function () {
                $('#message_loading').show();
            },
            statusCode: {
                500: function (xhr) {
                    $('#message_error>div.header').html("Erreur s'est produite au niveau du serveur");
                    $('#message_error').show();

                },
                404: function (response, textStatus, jqXHR) {
                    $('#message_error>div.header').html(response.responseJSON.message);
                    $('#message_error').show();

                }
            },
            success: function (response, textStatus, jqXHR) {
                console.log(response);
                $('#site_grid' + id).remove();
                $('#site_list' + id).remove();
                $('#message_loading').hide();
                $('#message_success>div.header').html(response.message);
                $('#message_success').show();
                window.location.replace();
                setTimeout(function () {
                    $('#message_success').hide();
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#message_loading').hide();
            }
        });
    });
}

function show_site(id) {
    $('#message_error').hide();
    $('#message_success').hide();
    $('.ui.dropdown').dropdown('remove active');
    $('.ui.dropdown').dropdown('remove visible');
    $('.ui.dropdown>div.menu').removeClass('visible');
    $('.ui.dropdown>div.menu').addClass('hidden');
    $('.ui.dropdown').dropdown({
        on: 'hover'
    });
    $.ajax({
        type: 'GET',
        url: Routing.generate('site_get_one', {id: id}),
        dataType: 'json',
        beforeSend: function () {
            $('#message_loading').show();
        },
        statusCode: {
            500: function (xhr) {
                $('#message_error>div.header').html("Erreur s'est produite au niveau du serveur");
                $('#message_error').show();
            },
            404: function (response, textStatus, jqXHR) {
                $('#message_error>div.header').html(response.responseJSON.message);
                $('#message_error').show();
            }
        },
        success: function (response, textStatus, jqXHR) {
            $('#edit_site').remove();
            $('#edit_site_content').html(response.site_details);
            $('#edit_site.ui.modal').modal('setting', {
                autofocus: false,
                inverted: true,
                closable: false
            });
            $('.ui.dropdown').dropdown({
                on: 'click'
            });

            $('#cancel_details_site').click(function () {
//                window.location.reload();
                $('#edit_site_content').html("");
            });
            $('#edit_site.ui.modal').modal('show');
            execute_edit(id);
            $('#edit_site_btn').click(function () {
                $('#block_details').hide();
                $('#block_form_edit').show();
                $('#cancel_edit_site').show();
                $('#submit_edit_site').show();
                $(this).hide();
            });
            $('#cancel_edit_site').click(function () {
                $('#block_details').show();
                $('#block_form_edit').hide();
                $('#edit_site_btn').show();
                $('#submit_edit_site').hide();
                $(this).hide();
            });

            $('#message_loading').hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#message_loading').hide();
        }
    });
}

