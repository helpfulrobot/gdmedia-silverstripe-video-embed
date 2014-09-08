(function($) {

    var $typeSelect = $('select[name="Type"]');

    function setupAlbumItemFields(val) {
        if (val == "Video") {
            $("div#Photo").hide();
            $("div#VideoItemID").show();
        } else {
            $("div#Photo").show();
            $("div#VideoItemID").hide();
        }
    }

    $typeSelect.entwine({
        onmatch: function() {
            setupAlbumItemFields(this.val());
        },
        onchange: function() {
            setupAlbumItemFields(this.val());
        }
    });
    setupAlbumItemFields($typeSelect.val());

})(jQuery);
