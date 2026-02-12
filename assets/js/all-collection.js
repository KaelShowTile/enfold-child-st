jQuery(document).ready(function($) {
    var collectionsData = [];

    // Load XML data on page load
    function loadCollectionsData() {
        var storedData = localStorage.getItem('collectionsData');
        var today = new Date().toDateString();

        if (storedData) {
            var parsedData = JSON.parse(storedData);
            if (parsedData.timestamp && parsedData.timestamp === today) {
                collectionsData = parsedData.data;
                fetchNewData();//testing
                //initializePage();
                console.log('Loaded collections data from localStorage (today)');
            } else {
                console.log('LocalStorage data is outdated, fetching new data...');
                fetchNewData();
            }
        } else {
            console.log('No localStorage data found, fetching new data...');
            fetchNewData();
        }
    }

    function fetchNewData() {
        fetch(collection_data.xml_url)
            .then(response => response.text())
            .then(xmlText => {
                var parser = new DOMParser();
                var xmlDoc = parser.parseFromString(xmlText, 'text/xml');
                collectionsData = parseXML(xmlDoc);
                var today = new Date().toDateString();
                var dataToStore = {
                    data: collectionsData,
                    timestamp: today
                };
                localStorage.setItem('collectionsData', JSON.stringify(dataToStore));
                initializePage();
                console.log('Fetched and stored new collections data');
            })
            .catch(error => {
                console.error('Error loading XML:', error);
            });
    }

    function parseXML(xmlDoc) {
        var collections = [];
        var collectionNodes = xmlDoc.getElementsByTagName('collection');
        console.log('Parsing XML, found', collectionNodes.length, 'collections');
        for (var i = 0; i < collectionNodes.length; i++) {
            var coll = collectionNodes[i];
            var id = coll.getAttribute('id');
            var title = coll.getElementsByTagName('title')[0].textContent;
            var permalink = coll.getElementsByTagName('permalink')[0].textContent;
            var categories = [];
            var catNodes = coll.getElementsByTagName('category');
            for (var j = 0; j < catNodes.length; j++) {
                categories.push(parseInt(catNodes[j].getAttribute('id')));
            }
            var images = {};
            var imgNodes = coll.getElementsByTagName('image');
            for (var k = 0; k < imgNodes.length; k++) {
                var type = imgNodes[k].getAttribute('type');
                var url = imgNodes[k].getAttribute('url');
                if (!images[type]) images[type] = [];
                images[type].push(url);
            }
            collections.push({
                id: id,
                title: title,
                permalink: permalink,
                categories: categories,
                images: images
            });
        }
        console.log('Parsed collections:', collections.length);
        return collections;
    }

    function initializePage() {
        // Load initial collections (first 9)
        var initialCollections = collectionsData.slice(0, 9);
        var initialHtml = generateCollectionsHtml(initialCollections);

        // Add load more button if there are more collections
        if (collectionsData.length > 9) {
            initialHtml += '<div class="load-more-container" style="text-align: center; margin: 20px 0;">';
            initialHtml += '<button class="load-more-filtered-btn btn st-link-button small-style" data-offset="9" data-term-ids="" data-total="' + collectionsData.length + '">Load More Collections</button>';
            initialHtml += '</div>';
        }

        $('.collection-list-container.fliter-collection').html(initialHtml);

        // Initialize Swiper for the initial content
        $('.collection-inner-slider-thumb').each(function() {
            var thumbEl = this;
            var previewEl = $(this).siblings('.collection-inner-slider-preview')[0];
            if (previewEl) {
                var swiperThumb = new Swiper(thumbEl, {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                });
                var swiperPreview = new Swiper(previewEl, {
                    spaceBetween: 10,
                    thumbs: {
                        swiper: swiperThumb,
                    },
                });
            }
        });

        // Store original category texts and update counts
        $('.child-category').each(function() {
            $(this).data('original-text', $(this).text().trim());
        });
        updateCategoryCounts();
    }

    // Function to filter collections based on active categories
    function filterCollections(offset = 0) {
        var termIds = $('.active-categories .active-cat').map(function() {
            return parseInt($(this).data('term-id'));
        }).get();

        console.log('Term IDs:', termIds);

        var filteredCollections;
        var allFilteredCollections;
        if (termIds.length === 0) {
            allFilteredCollections = collectionsData;
            filteredCollections = collectionsData.slice(offset, offset + 9);
        } else {
            allFilteredCollections = collectionsData.filter(function(collection) {
                return termIds.every(function(termId) {
                    return collection.categories.includes(termId);
                });
            });
            filteredCollections = allFilteredCollections.slice(offset, offset + 9);
        }

        console.log('Filtered collections:', filteredCollections.length, 'of', allFilteredCollections.length);
        console.log(filteredCollections);

        var html = generateCollectionsHtml(filteredCollections);

        // Add load more button if there are more results
        var currentCount = offset + filteredCollections.length;
        if (currentCount < allFilteredCollections.length) {
            var nextOffset = currentCount;
            html += '<div class="load-more-container" style="text-align: center; margin: 20px 0;">';
            html += '<button class="load-more-filtered-btn btn st-link-button small-style" data-offset="' + nextOffset + '" data-term-ids="' + termIds.join(',') + '" data-total="' + allFilteredCollections.length + '">Load More Collections</button>';
            html += '</div>';
        }

        console.log('Generated HTML length:', html.length);
        console.log('Container found:', $('.collection-list-container.fliter-collection').length);

        if (offset === 0) {
            // First page, replace all content
            $('.collection-list-container.fliter-collection').html(html);
        } else {
            // Load more, append to existing content and replace load more button
            var $container = $('.collection-list-container.fliter-collection');
            var $existingCards = $container.find('.collection-card');
            var $newContent = $(html);

            // Remove the load more button from new content and get just the cards
            var $newCards = $newContent.filter('.collection-card');
            var $newLoadMore = $newContent.filter('.load-more-container');

            // Append new cards before the existing load more button
            var $existingLoadMore = $container.find('.load-more-container');
            if ($existingLoadMore.length) {
                $existingLoadMore.before($newCards);
                if ($newLoadMore.length) {
                    $existingLoadMore.replaceWith($newLoadMore);
                } else {
                    $existingLoadMore.remove();
                }
            } else {
                $container.append($newCards);
                if ($newLoadMore.length) {
                    $container.append($newLoadMore);
                }
            }
        }

        // Re-initialize Swiper for the new content
        $('.collection-inner-slider-thumb').each(function() {
            var thumbEl = this;
            var previewEl = $(this).siblings('.collection-inner-slider-preview')[0];
            if (previewEl) {
                var swiperThumb = new Swiper(thumbEl, {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                });
                var swiperPreview = new Swiper(previewEl, {
                    spaceBetween: 10,
                    thumbs: {
                        swiper: swiperThumb,
                    },
                });
            }
        });

        // Update category counts
        updateCategoryCounts();
    }

    function generateCollectionsHtml(collections) {
        var html = '';
        collections.forEach(function(collection) {
            html += '<div class="collection-card">';
            html += '<div class="collection-inner-slider">';

            if (collection.images.collection_medium || collection.images.tile_medium) {
                html += '<div class="swiper collection-inner-slider-preview">';
                html += '<div class="swiper-wrapper">';
                if (collection.images.collection_medium) {
                    html += '<div class="swiper-slide"><img src="' + collection.images.collection_medium[0] + '" alt="' + collection.title + '"></div>';
                }
                if (collection.images.tile_medium) {
                    collection.images.tile_medium.forEach(function(url) {
                        html += '<div class="swiper-slide"><img src="' + url + '" alt="' + collection.title + '"></div>';
                    });
                }
                html += '</div>';
                html += '</div>';

                html += '<div thumbsSlider="" class="swiper collection-inner-slider-thumb">';
                html += '<div class="swiper-wrapper">';
                if (collection.images.collection_thumb) {
                    html += '<div class="swiper-slide slider-thumbnail"><img src="' + collection.images.collection_thumb[0] + '" alt="' + collection.title + '"></div>';
                }
                if (collection.images.tile_thumb) {
                    collection.images.tile_thumb.forEach(function(url) {
                        html += '<div class="swiper-slide slider-thumbnail"><img src="' + url + '" alt="' + collection.title + '"></div>';
                    });
                }
                html += '</div>';
                html += '</div>';
            }

            html += '</div>';
            html += '<a href="' + collection.permalink + '">';
            html += '<h3>' + collection.title + '</h3>';
            html += '</a>';
            html += '</div>';
        });
        return html;
    }

    // Click on child categories to add to active categories
    $('.category-display-list .child-category').on('click', function() {
        var catName = $(this).data('term-name');
        var catId = $(this).data('term-id');

        // Check if already added
        if ($('.active-categories .active-cat[data-term-id="' + catId + '"]').length === 0) {
            var button = '<button class="active-cat" data-term-id="' + catId + '">' + catName + ' <span class="delete-icon">&times;</span></button>';
            $('.active-categories').append(button);
            $(this).addClass('selected');
            filterCollections(); // Filter after adding
        }
    });

    // Click on delete icon to remove from active categories
    $('.active-categories').on('click', '.delete-icon', function(e) {
        e.stopPropagation();
        var catId = $(this).parent('.active-cat').data('term-id');
        $(this).parent('.active-cat').remove();
        $('.child-category[data-term-id="' + catId + '"]').removeClass('selected');
        filterCollections(); // Filter after removing
    });

    function updateCategoryCounts() {
        var currentTermIds = $('.active-categories .active-cat').map(function() {
            return parseInt($(this).data('term-id'));
        }).get();

        $('.child-category').each(function() {
            var catId = parseInt($(this).data('term-id'));
            var count;
            if (currentTermIds.includes(catId)) {
                count = collectionsData.filter(function(c) {
                    return currentTermIds.every(function(t) {
                        return c.categories.includes(t);
                    });
                }).length;
            } else {
                var testIds = currentTermIds.concat([catId]);
                count = collectionsData.filter(function(c) {
                    return testIds.every(function(t) {
                        return c.categories.includes(t);
                    });
                }).length;
            }
            $(this).text($(this).data('original-text') + ' (' + count + ')');
            //add class to 0 product category
            if(count != 0){
                $(this).removeClass('no-collection');
            }else{
                $(this).addClass('no-collection');
            }
        });
    }

    // Handle load more filtered button clicks
    $(document).on('click', '.load-more-filtered-btn', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $container = $button.closest('.collection-list-container');
        var $loadMoreContainer = $button.parent('.load-more-container');

        // Get data attributes
        var offset = parseInt($button.data('offset'));
        var termIds = $button.data('term-ids');

        // Show loading state
        $button.prop('disabled', true).text('Loading...');

        // Load more filtered results
        setTimeout(function() {
            filterCollections(offset);
        }, 100); // Small delay to show loading state
    });

    // Load data on page load
    loadCollectionsData();
});
