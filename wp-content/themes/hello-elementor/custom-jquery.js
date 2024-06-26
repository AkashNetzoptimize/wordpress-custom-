jQuery(function($) {
    var page = 1;

    $(document).on('click', '#pagination a', function(e) {
        e.preventDefault(); 

        if ($(this).text() === "Next →") {
            page++;
        } else if ($(this).text() === "← Previous") {
            if (page > 1) {
                page--;
            }
        } else {
            page = parseInt($(this).text());
        }

        $.ajax({
            url: ajaxpagination.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'ajax_filter_posts',
                page: page,
                category_id: $('#category_id').val()
            },
            success: function(response) {
                if (response.success) {
                    var $data = $(response.data);
                    console.log(response.data.posts);
                    $('.main-aja').empty().html(response.data.posts);
                    $('#pagination').empty().html(response.data.paginate); 
                    $('html, body').animate({ scrollTop: $('.main-aja').offset().top }, 'slow');
                } else {
                    console.log('Error:', response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('AJAX Error:', textStatus, errorThrown);
            }
        });
    });

    $(document).on('change', '#category_id', function() {
        $('#category-filter').submit();
    });
});
