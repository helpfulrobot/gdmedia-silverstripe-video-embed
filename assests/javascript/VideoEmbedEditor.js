(function($) {

    var $typeSelect = $("#Form_ItemEditForm_Type"),
            $thumbnailSelect = $("input[name=ThumbnailGroup]", "#Form_ItemEditForm"),
            $thumbnailURLField = $("#Form_ItemEditForm_ThumbnailURL"),
            $thumbnailURLPreview = $("#ThumbnailURLPreview"),
            $thumbnailURLHolder = $("#ThumbnailURLHolder"),
            $titleField = $("#Form_ItemEditForm_Title"),
            $codeField = $("#Form_ItemEditForm_Code"),
            imageExt = ['jpg', 'jpeg', 'gif', 'png'];
    function setupVideoEmbedFields(type) {
        console.log('setupVideoEmbedFields', type);
        for (var videoEmbedType in videoEmbedTypes) {
            if (videoEmbedTypes[videoEmbedType].label && videoEmbedTypes[videoEmbedType].label === type) {
                if (videoEmbedTypes[videoEmbedType].hide && videoEmbedTypes[videoEmbedType].hide.length) {
                    $(videoEmbedTypes[videoEmbedType].hide).hide();
                }
                if (videoEmbedTypes[videoEmbedType].show && videoEmbedTypes[videoEmbedType].show.length) {
                    $(videoEmbedTypes[videoEmbedType].show).show();
                }
            }
        }
    }

    $typeSelect.entwine({
        onmatch: function() {
            console.log('$typeSelect onmatch');
            setupVideoEmbedFields(this.val());
        },
        onchange: function() {
            console.log('$typeSelect onchange');
            setupVideoEmbedFields(this.val());
        }
    });
    $thumbnailSelect.entwine({
        onchange: function() {
            console.log('$thumbnailSelect onchange');
            var selectedVal = this.val();
            if (selectedVal === "File") {
                $thumbnailURLField.val("").change();
            } else if (selectedVal === "URL") {
                $(".ss-uploadfield-item-remove", $("#ThumbnailFile")).click();
            }
        }
    });
    $thumbnailURLField.entwine({
        onmatch: function() {
            console.log('$thumbnailURLField onmatch');
            this.data('timeout', null);
            if (this.val()) {
                $thumbnailURLPreview.setSrc(this.val());
            }
        },
        onkeyup: function() {
            console.log('$thumbnailURLField onkeyup');
            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function() {
                $thumbnailURLPreview.setSrc($thumbnailURLField.val());
            }, 250));
        },
        change: function() {
            console.log('$thumbnailURLField change');
            $thumbnailURLPreview.setSrc($thumbnailURLField.val());
        }
    });
    $thumbnailURLPreview.entwine({
        onmatch: function() {
            console.log('$thumbnailURLPreview onmatch');
            this.load(function() {
                if ($(this).attr("src") !== "") {
                    $(this).showIt();
                } else {
                    $(this).hideIt();
                }
            });
        },
        setSrc: function(thumbUrl) {
            console.log('$thumbnailURLPreview setSrc');
            var update = false;
            if (thumbUrl) {
                for (i = 0; i < imageExt.length; i++) {
                    if (thumbUrl.indexOf(imageExt[i], thumbUrl.length - imageExt[i].length) !== -1) {
                        update = true;
                        break;
                    }
                }
            }
            if (update) {
                if ($(this).attr("src") !== thumbUrl) {
                    this.attr("src", thumbUrl);
                } else {
                    this.showIt();
                }
            } else {
                $thumbnailURLPreview.hideIt();
            }
        },
        showIt: function() {
            console.log('$thumbnailURLPreview showIt');
            $thumbnailURLHolder.hide();
            $(this).show();
        },
        hideIt: function() {
            console.log('$thumbnailURLPreview hideIt');
            $(this).hide();
            $thumbnailURLHolder.show();
        }
    });
    $codeField.entwine({
        onchange: function() {
            var
                    videoCode = this.val(),
                    parsedUrl = urlParser.parse(videoCode),
                    videoEmbedType = parsedUrl && parsedUrl.provider ? videoEmbedTypes[parsedUrl.provider] : null,
                    baseHref = $("base").attr("href");
            console.log(parsedUrl);
            if (videoCode) {
                if (parsedUrl && videoEmbedType) {
                    videoEmbedType = videoEmbedTypes[parsedUrl.provider];
                    $typeSelect.val(videoEmbedType.label).trigger("liszt:updated");
                    videoCode = parsedUrl.id;
                    this.val(videoCode);
                }
                for (var item in videoEmbedTypes) {
                    if ($typeSelect.val() === item.label) {
                        videoEmbedType = item;
                        break;
                    }
                }
                if (videoEmbedType.url) {
                    $.post(baseHref + 'videoEmbedController/getOembedData/', {url: videoEmbedType.url.replace("{CODE}", videoCode)}, function(data) {
                        if (data && data.type === "video") {
                            console.log(data.type);
                            console.log(data.thumbnail_url);
                            console.log(data.title);
                            console.log($thumbnailSelect.val());
                            if (data.thumbnail_url && $thumbnailSelect.val() === "URL") {
                                $thumbnailURLField.val(data.thumbnail_url).change();
                            }
                            if (data.title) {
                                $titleField.val(data.title).change();
                            }
                        }
                    });
                }
            }
        }
    });

    setupVideoEmbedFields($typeSelect.val());
})(jQuery);
