jQuery(document).ready(function($) {
    'use strict';

    // Handle load more button clicks
    $(document).on('click', '.load-more-filtered-btn', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $loadMoreContainer = $button.parent('.load-more-container');
        var $spinner = $button.siblings('.loading-spinner');

        // Get data attributes
        var offset = parseInt($button.data('offset'));
        var limit = parseInt($button.data('limit'));
        var termIds = $button.data('term-ids');

        // Show loading state
        $button.prop('disabled', true).text('Loading...');
        $spinner.show();

        // Prepare AJAX data
        var ajaxData = {
            action: 'load_more_projects',
            offset: offset,
            limit: limit,
        };

        if (termIds) {
            ajaxData.term_ids = termIds;
        }

        // Make AJAX request
        $.ajax({
            url: st_ajax_object ? st_ajax_object.ajax_url : (ajax_object ? ajax_object.ajax_url : '/wp-admin/admin-ajax.php'),
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                if (response.success) {
                    // Hide loading state
                    $spinner.hide();
                    $button.prop('disabled', false).text('Load More Projects');

                    // Parse the returned HTML
                    var $newContent = $(response.data);

                    // Find the new project cards (exclude the load more container)
                    var $newCards = $newContent.filter('.single-project-card');

                    // Find if there's a new load more button
                    var $newLoadMore = $newContent.filter('.load-more-project');

                    // Insert new project cards before the load-more-container
                    $loadMoreContainer.before($newCards);

                    // Replace or remove the load more button
                    if ($newLoadMore.length) {
                        // There are more items, replace the button
                        $loadMoreContainer.replaceWith($newLoadMore);
                    } else {
                        // No more items, remove the load more container
                        $loadMoreContainer.remove();
                    }

                    // Trigger custom event for other scripts
                    $(document).trigger('projects:loaded', [$newCards]);

                } else {
                    console.error('AJAX error:', response.data);
                    // Reset button state
                    $spinner.hide();
                    $button.prop('disabled', false).text('Load More Projects');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', error);
                // Reset button state
                $spinner.hide();
                $button.prop('disabled', false).text('Load More Projects');
            }
        });
    });
});
