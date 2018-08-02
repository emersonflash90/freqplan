$(document).ready(function () {

    $('.ui.dropdown')
            .dropdown()
            ;
    $('.ui.accordion')
            .accordion({
            })
            ;

    $('.menu .item')
            .tab()
            ;

    $('.ui.sidebar')
            .sidebar({
                //context: $('.bottom.segment'),
                dimPage: true
            })
            .sidebar('setting', 'transition', 'overlay')
            .sidebar('attach events', '.toc.item')
            ;
    $('.popup')
            .popup()
            ;


    $('#login_form.ui.form')
            .form({
                fields: {
                    _username: {
                        identifier: '_username',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Veuillez votre email ou pseudo'
                            }
                        ]
                    },
                    _password: {
                        identifier: '_password',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Veuillez saisir votre mot de passe'
                            }
                        ]
                    }
                },
                inline: true,
                on: 'blur'

            }
            );

    $('#register_form.ui.form')
            .form({
                fields: {
                    lastname: {
                        identifier: 'lastname',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Enter your owner's name"
                            }
                        ]
                    },
                    username: {
                        identifier: 'username',
                        rules: [
                            {
                                type: 'empty',
                                prompt: "Enter your username"
                            }
                        ]
                    },
                    email: {
                        identifier: 'email',
                        rules: [
                            {
                                type: 'email',
                                prompt: 'Enter your email'
                            }
                        ]
                    },
                    password: {
                        identifier: 'password',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Enter your password'
                            }
                        ]
                    },
                    passwordConfirm: {
                        identifier: 'passwordConfirm',
                        rules: [
                            {
                                type: 'match[password]',
                                prompt: 'The passwords you have entered do not match'
                            }
                        ]
                    }
                },
                inline: true,
                on: 'submit'
            }
            );

    $('#submit_register_form').click(function (e) {
        if ($('#register_form').form('is valid')) {
//            $('#register_account_form').addClass('loading');
            $('#submit_register_form').addClass('loading');
        }
    });

    $('#submit_login_form').click(function (e) {
        if ($('#login_form').form('is valid')) {
//            $('#login_account_form').addClass('loading');
            $('#submit_login_form').addClass('loading');
        }
    });

//    $('#links_menu').click(function (e) {
//        e.preventDefault();
//        $('#modal_links.ui.modal')
//                .modal('show')
//                ;
//    });
//    $('#sites_menu').click(function (e) {
//        e.preventDefault();
//        $('#modal_sites.ui.modal')
//                .modal('show')
//                ;
//    });
//    

//    $('#add_site_btn').click(function (e) {
//        e.preventDefault();
//        $('#modal_add_site.ui.modal')
//                .modal('show')
//                ;
//    });
});

function edit_site(id_site) {
    $('#site_' + id_site).hide();
    $('#edit_site_' + id_site).show();
}

function cancel_edit_site(id_site) {
    $('#edit_site_' + id_site).hide();
    $('#site_' + id_site).show();
}