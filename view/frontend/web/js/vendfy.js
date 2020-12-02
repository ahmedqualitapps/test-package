'use strict';
require(["jquery", "domReady!"], function($) {
    $(document).ready(function() {
        var searchParams = new URLSearchParams(window.location.search);

        var ref = searchParams.get('ref');

        var couponCode = searchParams.get('code');

        if (ref) {
            document.cookie = 'ref=' + ref + ';max-age=3600';
        }

        if (couponCode) {
            document.cookie = 'couponCode=' + couponCode + ';max-age=3600';
        }
    });
});