'use strict';

function getGtmDateTimeString(datetext) {
    var d = new Date();
    d = new Date(d.valueOf() + d.getTimezoneOffset() * 60000);

    var h = d.getHours();
    h = h < 10 ? '0' + h : h;

    var m = d.getMinutes();
    m = m < 10 ? '0' + m : m;

    var s = d.getSeconds();
    s = s < 10 ? '0' + s : s;

    return datetext + ' ' + h + ':' + m + ':' + s;
}

$(document).ready(function() {
    var $expiryDateTime = $('.js-expiry-date');

    if ($expiryDateTime.length) {
        $expiryDateTime.datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            numberOfMonths: 3,
            defaultDate: 0,
            onSelect: function (datetext) {
                $expiryDateTime.val(getGtmDateTimeString(datetext));
            },
        });
    }
});

module.exports = {};
