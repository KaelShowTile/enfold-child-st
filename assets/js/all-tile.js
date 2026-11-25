jQuery(document).ready(function($) {
    'use strict';

    if (typeof all_tile_data === 'undefined') {
        return;
    }

    var PAGE_SIZE = parseInt(all_tile_data.page_size, 10) || 24; // 8 rows x 3 cards
    var IDB_NAME = 'st_tiles_feed_db';
    var IDB_STORE = 'feeds';
    var IDB_KEY = 'all_tiles_feed';
    var STATE_KEY = 'st_all_tiles_state';

    var tilesData = [];
    var filteredTiles = [];
    var activeOptionKeys = [];
    var currentDisplayed = 0;

    // Build lookup map of all configured ACF filter options with pre-normalized match values
    var filterOptionsMap = {};
    var filterGroups = Array.isArray(all_tile_data.filter_groups) ? all_tile_data.filter_groups : [];

    function normalizeText(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value).replace(/[\s\u00A0\u3000]+/g, '').toLowerCase();
    }

    function normalizeSize(value) {
        if (value === null || value === undefined) {
            return '';
        }
        var str = String(value);
        // Unify multiplication / cross symbols and dashes
        str = str.replace(/[×✕✖✗✘⨯∗*＊ＸｘХхΧχ]/g, 'x');
        str = str.replace(/[–—－−]/g, '-');
        return normalizeText(str);
    }

    filterGroups.forEach(function(group) {
        if (!group || !Array.isArray(group.options)) {
            return;
        }
        group.options.forEach(function(opt) {
            var isSize = (opt.field === 'tile_size');
            var normVals = [];
            if (Array.isArray(opt.values)) {
                opt.values.forEach(function(rawVal) {
                    var nv = isSize ? normalizeSize(rawVal) : normalizeText(rawVal);
                    if (nv !== '') {
                        normVals.push(nv);
                    }
                });
            }
            filterOptionsMap[opt.key] = {
                key: opt.key,
                field: opt.field,
                name: opt.name,
                normValues: normVals
            };
        });
    });

    // Escape helpers for safe HTML rendering
    function escapeHtml(str) {
        if (str === null || str === undefined) {
            return '';
        }
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // IndexedDB helpers (avoids 5MB localStorage quota limit as tile catalog grows)
    function openFeedDB() {
        return new Promise(function(resolve, reject) {
            if (!window.indexedDB) {
                reject(new Error('IndexedDB unavailable'));
                return;
            }
            var req = window.indexedDB.open(IDB_NAME, 1);
            req.onupgradeneeded = function(e) {
                var db = e.target.result;
                if (!db.objectStoreNames.contains(IDB_STORE)) {
                    db.createObjectStore(IDB_STORE);
                }
            };
            req.onsuccess = function(e) {
                resolve(e.target.result);
            };
            req.onerror = function(e) {
                reject(e.target.error || new Error('IndexedDB open error'));
            };
        });
    }

    function readFeedFromIDB() {
        return openFeedDB().then(function(db) {
            return new Promise(function(resolve, reject) {
                var tx = db.transaction(IDB_STORE, 'readonly');
                var store = tx.objectStore(IDB_STORE);
                var req = store.get(IDB_KEY);
                req.onsuccess = function() {
                    resolve(req.result || null);
                };
                req.onerror = function() {
                    reject(req.error);
                };
            });
        });
    }

    function writeFeedToIDB(version, data) {
        return openFeedDB().then(function(db) {
            return new Promise(function(resolve) {
                var tx = db.transaction(IDB_STORE, 'readwrite');
                var store = tx.objectStore(IDB_STORE);
                store.put({ version: version, data: data }, IDB_KEY);
                tx.oncomplete = function() { resolve(); };
                tx.onerror = function() { resolve(); };
            });
        }).catch(function() {
            // Silently ignore IndexedDB write errors (e.g. private browsing)
        });
    }

    // Precompute normalized values once per tile for ultra-fast client-side filtering
    function prepareTilesData(rawTiles) {
        if (!Array.isArray(rawTiles)) {
            return [];
        }
        return rawTiles.map(function(tile) {
            var finishes = Array.isArray(tile.f) ? tile.f.map(function(f) {
                var sizes = Array.isArray(f.sz) ? f.sz : [];
                return {
                    fn: f.fn || '',
                    c: f.c || '',
                    sr: f.sr || '',
                    img: f.img || '',
                    sz: sizes,
                    _fn: normalizeText(f.fn || ''),
                    _sr: normalizeText(f.sr || ''),
                    _sz: sizes.map(normalizeSize)
                };
            }) : [];

            return {
                id: tile.id,
                t: tile.t || '',
                u: tile.u || '#',
                img: tile.img || '',
                ind: tile.ind ? 1 : 0,
                v360: tile.v360 ? 1 : 0,
                f: finishes,
                _d: normalizeText(tile.d || ''),
                _th: normalizeText(tile.th || ''),
                _m: normalizeText(tile.m || '')
            };
        });
    }

    function loadTilesData() {
        var targetVersion = String(all_tile_data.feed_version || '1');

        readFeedFromIDB()
            .then(function(cached) {
                if (cached && cached.version === targetVersion && Array.isArray(cached.data)) {
                    tilesData = prepareTilesData(cached.data);
                    initializePage();
                    return;
                }
                return fetchTilesFeed(targetVersion);
            })
            .catch(function() {
                return fetchTilesFeed(targetVersion);
            });
    }

    function fetchTilesFeed(targetVersion) {
        return fetch(all_tile_data.feed_url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function(json) {
                tilesData = prepareTilesData(json);
                writeFeedToIDB(targetVersion, json);
                initializePage();
            })
            .catch(function(err) {
                console.error('Error loading tiles feed:', err);
                $('#all-tiles-list').html('<div class="all-tiles-empty-message">Failed to load tiles. Please refresh the page.</div>');
            });
    }

    // Group an array of option keys by field name
    function groupOptionsByField(keys) {
        var grouped = {};
        keys.forEach(function(key) {
            var opt = filterOptionsMap[key];
            if (!opt) {
                return;
            }
            if (!grouped[opt.field]) {
                grouped[opt.field] = [];
            }
            grouped[opt.field].push(opt);
        });
        return grouped;
    }

    // Substring contains check: true if target string contains ANY normalized value of ANY selected option in group (OR within group)
    function matchesOptionGroup(normalizedTarget, optionsInGroup) {
        if (!normalizedTarget) {
            return false;
        }
        for (var i = 0; i < optionsInGroup.length; i++) {
            var vals = optionsInGroup[i].normValues;
            for (var j = 0; j < vals.length; j++) {
                if (vals[j] !== '' && normalizedTarget.indexOf(vals[j]) !== -1) {
                    return true;
                }
            }
        }
        return false;
    }

    // Core filter predicate:
    // - OR within same filter group
    // - AND across different filter groups
    // - Method B for repeater fields (finish_name, tile_slip_rate, tile_size): at least one tile_finish row satisfies all active finish-level groups simultaneously
    function tileMatchesGroupedFilters(tile, activeByField) {
        if (activeByField.tile_design && !matchesOptionGroup(tile._d, activeByField.tile_design)) {
            return false;
        }
        if (activeByField.tile_thickness && !matchesOptionGroup(tile._th, activeByField.tile_thickness)) {
            return false;
        }
        if (activeByField.tile_material && !matchesOptionGroup(tile._m, activeByField.tile_material)) {
            return false;
        }

        var hasFinishFilter = !!(activeByField.finish_name || activeByField.tile_slip_rate || activeByField.tile_size);
        if (hasFinishFilter) {
            if (!tile.f || tile.f.length === 0) {
                return false;
            }
            var matchedAnyFinishRow = false;
            for (var i = 0; i < tile.f.length; i++) {
                var row = tile.f[i];
                if (activeByField.finish_name && !matchesOptionGroup(row._fn, activeByField.finish_name)) {
                    continue;
                }
                if (activeByField.tile_slip_rate && !matchesOptionGroup(row._sr, activeByField.tile_slip_rate)) {
                    continue;
                }
                if (activeByField.tile_size) {
                    var sizeMatched = false;
                    for (var s = 0; s < row._sz.length; s++) {
                        if (matchesOptionGroup(row._sz[s], activeByField.tile_size)) {
                            sizeMatched = true;
                            break;
                        }
                    }
                    if (!sizeMatched) {
                        continue;
                    }
                }
                matchedAnyFinishRow = true;
                break;
            }
            if (!matchedAnyFinishRow) {
                return false;
            }
        }

        return true;
    }

    // Generate card HTML matching single-product.php lines 344-376
    function generateTileCardHtml(tile) {
        var thumbUrl = tile.img || all_tile_data.placeholder_img;
        var safeTitle = escapeHtml(tile.t);
        var safeLink = escapeHtml(tile.u);

        // Build Finish | Size dropdown options
        var optionsHtml = '';
        if (tile.f && tile.f.length > 0) {
            tile.f.forEach(function(fin) {
                var finThumb = fin.img || thumbUrl;
                if (fin.sz && fin.sz.length > 0) {
                    fin.sz.forEach(function(szName) {
                        var uniqueVal = tile.t + ' - ' + fin.fn + ' - ' + szName + ' - ' + fin.c;
                        var prodName = tile.t + ' - ' + fin.fn + ' - ' + szName;
                        optionsHtml += '<option'
                            + ' value="' + escapeHtml(uniqueVal) + '"'
                            + ' data-product-name="' + escapeHtml(prodName) + '"'
                            + ' data-tile-name="' + safeTitle + '"'
                            + ' data-finish="' + escapeHtml(fin.fn) + '"'
                            + ' data-size="' + escapeHtml(szName) + '"'
                            + ' data-code="' + escapeHtml(fin.c) + '"'
                            + ' data-image-url="' + escapeHtml(finThumb) + '">'
                            + escapeHtml(fin.fn + ' | ' + szName)
                            + '</option>';
                    });
                }
            });
        }

        var html = '<div class="single-tile-card">';
        if (tile.ind) {
            html += '<span class="indent-only-icon">Indent Only</span>';
        }
        html += '<a href="' + safeLink + '">';
        if (tile.v360) {
            html += '<img src="' + escapeHtml(all_tile_data.icon_360) + '" class="tile-virtual-threater-icon" alt="360 view">';
        }
        html += '<img src="' + escapeHtml(thumbUrl) + '" alt="' + safeTitle + '" loading="lazy">';
        html += '</a>';

        html += '<div class="tile-card-detail">';
        html += '<div class="tile-card-left">';
        html += '<a href="' + safeLink + '"><h5>' + safeTitle + '</h5></a>';
        if (optionsHtml !== '') {
            html += '<select name="select-tile-to-basket" class="select-tile-dropdown">' + optionsHtml + '</select>';
        }
        html += '</div>';
        html += '<a class="add-tile-to-basket"><img src="' + escapeHtml(all_tile_data.basket_icon) + '" alt="add tile to basket"></a>';
        html += '</div>';
        html += '</div>';

        return html;
    }

    function renderActiveFilterPills() {
        var $container = $('#all-tiles-active-filters');
        $container.empty();

        if (activeOptionKeys.length === 0) {
            return;
        }

        activeOptionKeys.forEach(function(key) {
            var opt = filterOptionsMap[key];
            if (!opt) {
                return;
            }
            var pill = '<button type="button" class="active-cat" data-option-key="' + escapeHtml(key) + '">'
                + escapeHtml(opt.name)
                + ' <span class="delete-icon">&times;</span></button>';
            $container.append(pill);
        });

        if (activeOptionKeys.length > 1) {
            $container.append('<button type="button" class="active-cat clear-all-filters">Clear All &times;</button>');
        }
    }

    function syncFilterSelectionClasses() {
        $('.tile-filter-option').removeClass('selected');
        activeOptionKeys.forEach(function(key) {
            $('.tile-filter-option[data-option-key="' + key + '"]').addClass('selected');
        });
    }

    // Update live faceted result counts next to every option (OR-within-group faceted count)
    function updateFilterOptionCounts() {
        var activeByField = groupOptionsByField(activeOptionKeys);

        // Pre-filter tiles for each group excluding that group's own active selections
        var poolByField = {};
        filterGroups.forEach(function(group) {
            var field = group.field;
            if (poolByField[field]) {
                return;
            }
            var otherGroups = {};
            var hasOther = false;
            Object.keys(activeByField).forEach(function(f) {
                if (f !== field) {
                    otherGroups[f] = activeByField[f];
                    hasOther = true;
                }
            });
            poolByField[field] = hasOther
                ? tilesData.filter(function(tile) { return tileMatchesGroupedFilters(tile, otherGroups); })
                : tilesData;
        });

        // Calculate counts once per unique option key
        var countByKey = {};
        Object.keys(filterOptionsMap).forEach(function(key) {
            var opt = filterOptionsMap[key];
            var pool = poolByField[opt.field] || tilesData;
            var singleGroup = {};
            singleGroup[opt.field] = [opt];
            var cnt = 0;
            for (var i = 0; i < pool.length; i++) {
                if (tileMatchesGroupedFilters(pool[i], singleGroup)) {
                    cnt++;
                }
            }
            countByKey[key] = cnt;
        });

        // Update both desktop and mobile filter option labels
        $('.tile-filter-option').each(function() {
            var $el = $(this);
            var key = $el.data('option-key');
            var baseName = $el.data('option-name');
            var count = countByKey[key] !== undefined ? countByKey[key] : 0;

            $el.html('&nbsp;&nbsp;' + escapeHtml(baseName) + ' (' + count + ')');
            if (count > 0) {
                $el.removeClass('no-collection');
            } else {
                $el.addClass('no-collection');
            }
        });
    }

    function saveState() {
        try {
            sessionStorage.setItem(STATE_KEY, JSON.stringify({
                activeKeys: activeOptionKeys,
                displayed: currentDisplayed
            }));
        } catch (e) {
            // Ignore storage errors
        }
    }

    function updateStatusAndLoadMore() {
        var total = filteredTiles.length;
        if (total === 0) {
            $('#all-tiles-status').text('0 tiles found');
        } else {
            $('#all-tiles-status').text('Showing ' + currentDisplayed + ' of ' + total + ' tiles');
        }

        var $loadMoreWrap = $('#all-tiles-load-more');
        var $btn = $loadMoreWrap.find('.load-more-tiles-btn');
        if (currentDisplayed < total) {
            $btn.prop('disabled', false).text('Load More Tiles');
            $loadMoreWrap.show();
        } else {
            $loadMoreWrap.hide();
        }
    }

    function applyFilters(initialCount) {
        var activeByField = groupOptionsByField(activeOptionKeys);
        var hasAnyFilter = Object.keys(activeByField).length > 0;

        filteredTiles = hasAnyFilter
            ? tilesData.filter(function(tile) { return tileMatchesGroupedFilters(tile, activeByField); })
            : tilesData;

        var targetCount = initialCount ? Math.max(PAGE_SIZE, initialCount) : PAGE_SIZE;
        var batch = filteredTiles.slice(0, targetCount);
        currentDisplayed = batch.length;

        var $list = $('#all-tiles-list');
        if (batch.length === 0) {
            $list.html('<div class="all-tiles-empty-message">No tiles match the selected filters. Try removing one or more filters.</div>');
        } else {
            var html = '';
            for (var i = 0; i < batch.length; i++) {
                html += generateTileCardHtml(batch[i]);
            }
            $list.html(html);
        }

        renderActiveFilterPills();
        syncFilterSelectionClasses();
        updateFilterOptionCounts();
        updateStatusAndLoadMore();
        saveState();
    }

    function loadMoreTiles() {
        if (currentDisplayed >= filteredTiles.length) {
            return;
        }
        var nextBatch = filteredTiles.slice(currentDisplayed, currentDisplayed + PAGE_SIZE);
        var html = '';
        for (var i = 0; i < nextBatch.length; i++) {
            html += generateTileCardHtml(nextBatch[i]);
        }
        $('#all-tiles-list').append(html);
        currentDisplayed += nextBatch.length;

        updateStatusAndLoadMore();
        saveState();
    }

    function initializePage() {
        var restoredDisplayed = PAGE_SIZE;
        try {
            var savedRaw = sessionStorage.getItem(STATE_KEY);
            if (savedRaw) {
                var saved = JSON.parse(savedRaw);
                if (Array.isArray(saved.activeKeys)) {
                    activeOptionKeys = saved.activeKeys.filter(function(k) {
                        return !!filterOptionsMap[k];
                    });
                }
                if (saved.displayed && parseInt(saved.displayed, 10) > PAGE_SIZE) {
                    restoredDisplayed = parseInt(saved.displayed, 10);
                }
            }
        } catch (e) {
            // Ignore malformed state
        }

        applyFilters(restoredDisplayed);
    }

    // Event: Click on any filter option (desktop or mobile) to select/deselect
    $(document).on('click', '.tile-filter-option', function() {
        var key = $(this).data('option-key');
        if (!filterOptionsMap[key]) {
            return;
        }
        var idx = activeOptionKeys.indexOf(key);
        if (idx === -1) {
            activeOptionKeys.push(key);
        } else {
            activeOptionKeys.splice(idx, 1);
        }
        applyFilters(PAGE_SIZE);
    });

    // Event: Click on an active filter pill (or its delete icon) to remove
    $('#all-tiles-active-filters').on('click', '.active-cat', function(e) {
        e.preventDefault();
        if ($(this).hasClass('clear-all-filters')) {
            activeOptionKeys = [];
            applyFilters(PAGE_SIZE);
            return;
        }
        var key = $(this).data('option-key');
        var idx = activeOptionKeys.indexOf(key);
        if (idx !== -1) {
            activeOptionKeys.splice(idx, 1);
            applyFilters(PAGE_SIZE);
        }
    });

    // Event: Load More Tiles button
    $(document).on('click', '.load-more-tiles-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.prop('disabled', true).text('Loading...');
        setTimeout(function() {
            loadMoreTiles();
        }, 60);
    });

    // Event: Add to Basket (matches single-collection.js behavior identically)
    $(document).on('click', '.all-tiles-list .add-tile-to-basket', function(e) {
        e.preventDefault();

        var $cardDetail = $(this).closest('.tile-card-detail');
        var selectDropdown = $cardDetail.find('.select-tile-dropdown')[0];
        if (!selectDropdown) {
            return;
        }

        var selectedOption = selectDropdown.options[selectDropdown.selectedIndex];
        if (!selectedOption) {
            return;
        }

        var productUnqueName = selectedOption.getAttribute('data-product-name');
        var productName = selectedOption.getAttribute('data-tile-name');
        var productFinish = selectedOption.getAttribute('data-finish');
        var productSize = selectedOption.getAttribute('data-size');
        var productImageUrl = selectedOption.getAttribute('data-image-url');
        var productCode = selectedOption.getAttribute('data-code');

        if (productUnqueName) {
            var basketItems = JSON.parse(localStorage.getItem('idea-basket-items')) || [];
            var existingItem = basketItems.find(function(item) {
                return item.UnqueName === productUnqueName;
            });

            if (!existingItem) {
                basketItems.push({
                    UnqueName: productUnqueName,
                    name: productName,
                    finish: productFinish,
                    size: productSize,
                    code: productCode,
                    imageUrl: productImageUrl,
                    dateAdded: new Date().toISOString()
                });
                localStorage.setItem('idea-basket-items', JSON.stringify(basketItems));
                showBasketMessage('Item added to Idea Basket!', 'success');
            } else {
                showBasketMessage('Item already in Idea Basket!', 'info');
            }
        }
    });

    function showBasketMessage(message, type) {
        var existingMessage = document.querySelector('.basket-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        var messageEl = document.createElement('div');
        messageEl.className = 'basket-message ' + type;
        messageEl.textContent = message;
        messageEl.style.cssText = 'position:fixed;top:20px;right:20px;padding:10px 20px;border-radius:5px;color:white;font-weight:bold;z-index:9999;opacity:0;transition:opacity 0.3s ease;';
        messageEl.style.backgroundColor = (type === 'success') ? '#28a745' : '#17a2b8';

        document.body.appendChild(messageEl);
        setTimeout(function() { messageEl.style.opacity = '1'; }, 100);
        setTimeout(function() {
            messageEl.style.opacity = '0';
            setTimeout(function() {
                if (messageEl.parentNode) {
                    messageEl.parentNode.removeChild(messageEl);
                }
            }, 300);
        }, 3000);
    }

    // Boot
    loadTilesData();
});
