var menu = {
    element: null,
    level: null,
    parentId: null,

    init: function (ajaxChildrenUrl) {
        // Scroll to current menu item
        let sideBar = $('.sidebar-sticky');
        let parentNavItem = $('.nav-item-current').parent().parent().parent().parent().parent();
        // After rendering the page on desktop screens
        sideBar.animate({
            scrollTop: parentNavItem.offset().top - sideBar.offset().top + sideBar.scrollTop()
        }, 0);

        // After un-collapsing the menu on mobile screens
        $('#sidebarMenu').on('shown.bs.collapse', function () {
            sideBar.animate({
                scrollTop: parentNavItem.offset().top - sideBar.offset().top + sideBar.scrollTop()
            }, 500);
        })

        // On click on arrow icon
        $(document).on('click', '.nav-collapse-icon', function () {
            //console.log(this);
            var itemLink = $('.nav-link[data-item-id=' + $(this).data('item-id') + ']');
            var state = $(this).hasClass('active');
            //console.log('icon' + $(this).data('item-id') + state);
            // Parent div element with class 'nav-item-row'
            menu.element = itemLink.parent();
            menu.level = $(this).data('level');
            menu.parentId = $(this).data('item-id');

            // Check for active state
            if (state) {
                // Deactivate and hide
                itemLink.removeClass("active");
                $(this).removeClass("active");
                $(this).addClass("collapsed");
            }
            else {
                // Check if submenu content is already available
                if ($(this).parent().parent().children('ul').length) {
                    //console.log('expanding available menu...');
                }
                else {
                    // Otherwise load using AJAX request
                    var url = ajaxChildrenUrl + '?item=' + $(this).data('item-id');
                    url += '&level=' + $(this).data('level');
                    $.getJSON(url, function (data, status) {
                        //console.log(status);
                        //console.log(data);
                        menu.appendChildren(data.data);
                    });
                }

                // Activate and unhide
                $(this).removeClass("collapsed");
                $(this).addClass("active");
                itemLink.addClass("active");
            }
        });
    },

    appendChildren: function (items) {
        //console.log('append children');
        //console.log(menu.element);
        //console.log(items);
        // Load linked page if no children available
        if (!items.length) {
            window.location.href = menu.element.children('.nav-link').attr('href');
        }

        const itemsHtml = [];
        // Prepare HTML for all the list elements
        for (let i of items) {
            itemsHtml.push(this.prepareMenuItem(i));
        }
        // Prepare the HTML for the enclosing unordered list
        let listHtml = this.prepareMenuList(itemsHtml);
        // Append the complete HTML to the DOM
        menu.element.after(listHtml);
    },

    prepareMenuItem: function (item) {
        //console.log('prepare item');
        let html = '<li class="nav-item">';
        html += '<div class="nav-item-row d-flex">';
        html += '<a class="nav-link mr-auto pl-0" href="' + item.route_show_public + '" data-item-id="' + item.item_id + '">';
        html += item.title + '</a>';
        html += '<a href="#collapseMI' + item.item_id + '" class="nav-collapse-icon collapsed"';
        html += ' aria-expanded="false" data-item-id="' + item.item_id + '"';
        html += ' data-level="' + menu.level + '"';
        html += ' data-toggle="collapse" role="button"';
        html += ' aria-controls="collapseMI' + item.item_id + '">';
        html += '<i class="fa mr-0" aria-hidden="true"></i>';
        html += '</a></div></li>';

        return html;
    },

    prepareMenuList: function (itemsHtml) {
        //console.log('prepare list');
        let html = '<ul class="collapse show" id="collapseMI' + menu.parentId + '">';
        // Include HTML for each list element
        for (let i of itemsHtml) {
            html += i;
        }
        html += '</ul>';

        return html;
    },
}

export default menu;
