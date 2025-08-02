jQuery(document).ready(function($){
    function initSortable(){
        $('#ezmm-columns-list').sortable({
            handle: '.handle'
        });
    }
    initSortable();

    $('#ezmm-add-column').on('click', function(e){
        e.preventDefault();
        var item = $('<li class="ezmm-column-item"><span class="handle">\u2630</span>' +
            '<input type="text" class="ezmm-col-title" placeholder="'+ezmm_admin.col_title+'" />' +
            '<input type="text" class="ezmm-col-icon" placeholder="'+ezmm_admin.col_icon+'" />' +
            '<button class="button ezmm-col-upload">'+ezmm_admin.upload+'</button>' +
            '<button class="button ezmm-remove-column">&times;</button></li>');
        $('#ezmm-columns-list').append(item);
    });

    $('#ezmm-columns-list').on('click', '.ezmm-remove-column', function(e){
        e.preventDefault();
        $(this).closest('.ezmm-column-item').remove();
    });

    function openMedia(button){
        var frame = wp.media({
            title: ezmm_admin.media_title,
            button: { text: ezmm_admin.media_button },
            multiple: false
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            button.prev('input').val(attachment.url);
        });
        frame.open();
    }

    $('.ezmm-upload').on('click', function(e){
        e.preventDefault();
        openMedia($(this));
    });

    $('#ezmm-columns-list').on('click', '.ezmm-col-upload', function(e){
        e.preventDefault();
        openMedia($(this));
    });

    $('#ezmm-menu-form').on('submit', function(){
        var structure = [];
        $('#ezmm-columns-list .ezmm-column-item').each(function(){
            structure.push({
                title: $(this).find('.ezmm-col-title').val(),
                icon: $(this).find('.ezmm-col-icon').val()
            });
        });
        $('#ezmm_structure').val(JSON.stringify(structure));
    });
});
