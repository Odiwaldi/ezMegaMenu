jQuery(document).ready(function($){
    $('.ezmm-menu').each(function(){
        var $menu = $(this);
        var eventType = $menu.data('open-event');
        var $columns = $menu.find('.ezmm-columns');
        var hoverIntent;

        function openMenu(){
            $menu.addClass('open');
            $columns.stop(true,true).slideDown(200);
        }
        function closeMenu(){
            $menu.removeClass('open');
            $columns.stop(true,true).slideUp(200);
        }

        function bindDesktop(){
            if(eventType === 'hover'){
                $menu.on('mouseenter.ezmm', function(){
                    hoverIntent = setTimeout(openMenu, 150);
                }).on('mouseleave.ezmm', function(){
                    clearTimeout(hoverIntent);
                    closeMenu();
                });
            }else{
                $menu.on('click.ezmm', function(e){
                    e.preventDefault();
                    $menu.hasClass('open') ? closeMenu() : openMenu();
                });
            }
        }

        function bindMobile(){
            $menu.off('.ezmm');
            $menu.on('click.ezmm', function(e){
                e.preventDefault();
                $menu.toggleClass('open');
                $columns.stop(true,true).slideToggle(200);
            });
        }

        function setup(){
            if(window.matchMedia('(max-width:768px)').matches){
                bindMobile();
            }else{
                bindDesktop();
            }
        }

        setup();
        $(window).on('resize', function(){
            setup();
        });
    });
});
