$(document).ready(function () {
    $('.ui.dropdown')
            .dropdown()
            ;
    $('.ui.accordion')
            .accordion({
            })
            ;

    var d = new Date();
//    $('.ui.menu.top_menu .ui.container .home_left_menu .item:last-child').html(d.toDateString());
    $('.ui.sidebar')
            .sidebar({
                //context: $('.bottom.segment'),
                dimPage: true
            })
            .sidebar('setting', 'transition', 'overlay')
            .sidebar('attach events', '.toc.item')
            ;

//    $('.ui.sidebar').click(function(){
//        if($(this).sidebar("isvisible"))
//        $('.ui.sidebar').sidebar("show");
//    });

});