/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

require('../../sass/main.scss');

$(document).ready(function () {

    /**
     * @param data
     * @param params
     * @returns {{results: *, pagination: {more: (boolean|number)}}}
     */
    function processAjaxResult(data, params) {
        params.page = params.page || 1;

        return {
            results: data.values,
            pagination: {
                more: params.page * 30 < data.total || 0,
            },
        };
    }

    /**
     * @param $select
     * @param term
     */
    function select2_search($select, term) {
        $select.select2('open');

        var $search = $select.data('select2').dropdown.$search || $select.data('select2').selection.$search;

        $search.val(term);
        $search.trigger('keyup');
    }

    /**
     * Register global event listeners
     */
    $('.spryker-form-select2combobox:not([class=".tags"]):not([class=".ajax"])').select2({});

    $('.spryker-form-select2combobox.tags:not([class=".ajax"])').select2({
        tags: true,
    });

    const dropdownWithParent = $('.spryker-form-select2combobox[dropdown-parent]');

    if (dropdownWithParent) {
        dropdownWithParent.select2({
            dropdownParent: dropdownWithParent.closest(dropdownWithParent.attr('dropdown-parent')),
        });
    }

    document.querySelectorAll('.spryker-form-select2combobox--table-filter-form').forEach((select) => {
        const selectConfig = {};

        if (select.dataset.clearable) {
            selectConfig.allowClear = true;
        }

        if (select.dataset.disableSearch) {
            selectConfig.minimumResultsForSearch = Infinity;
        }

        $(select).select2(selectConfig);
    });
});

module.exports = {};
