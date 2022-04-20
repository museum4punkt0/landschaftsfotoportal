var menu = {
    init: function () {

        $('.sidebar .collapse').on('hide.bs.collapse', function () {
            //console.log(this);
            //console.log($(this).attr('id'));
            //$(this).removeClass("active");
        });

        $('.nav-collapse-icon').on('click', function () {
            console.log(this);
            var itemLink = $('.nav-link[data-item-id=' + $(this).data('item-id') + ']');
            var state = $(this).hasClass('active');
            console.log('icon' + $(this).data('item-id') + state);
            if (state) {
                itemLink.removeClass("active");
                $(this).removeClass("active");
                $(this).addClass("collapsed");
            }
            else {
                $(this).removeClass("collapsed");
                $(this).addClass("active");
                itemLink.addClass("active");
            }
        });
    },
}

export default menu;
