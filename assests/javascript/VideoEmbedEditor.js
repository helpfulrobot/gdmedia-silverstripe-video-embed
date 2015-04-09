(function ($) {
    var $typeSelect = $("#Form_ItemEditForm_Type"),
            $thumbnailSelect = $("input[name=ThumbnailGroup]", "#Form_ItemEditForm"),
            $thumbnailURLField = $("#Form_ItemEditForm_ThumbnailURL"),
            $thumbnailURLPreview = $("#ThumbnailURLPreview"),
            $thumbnailURLHolder = $("#ThumbnailURLHolder"),
            $titleField = $("#Form_ItemEditForm_Title"),
            $codeField = $("#Form_ItemEditForm_Code"),
            $videoTypesField = $("#Form_ItemEditForm_VideoTypesHolder"),
            imageExt = ['jpg', 'jpeg', 'gif', 'png'],
            videoEmbedTypes = {};

    $videoTypesField.entwine({
        onmatch: function () {
            videoEmbedTypes = JSON.parse(this.val());
            entwineFields();
        }
    });

    function entwineFields() {
        $typeSelect.entwine({
            onmatch: function () {
                this.setupVideoEmbedFields();
            },
            onchange: function () {
                this.setupVideoEmbedFields();
            },
            setupVideoEmbedFields: function () {
                var type = this.val();
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
        });
        $thumbnailSelect.entwine({
            onchange: function () {
                var selectedVal = this.val();
                if (selectedVal === "File") {
                    $thumbnailURLField.val("").change();
                } else if (selectedVal === "URL") {
                    $(".ss-uploadfield-item-remove", $("#ThumbnailFile")).click();
                }
            }
        });
        $thumbnailURLField.entwine({
            onmatch: function () {
                this.data('timeout', null);
                if (this.val()) {
                    $($thumbnailURLPreview.selector).setSrc(this.val());
                }
            },
            onkeyup: function () {
                clearTimeout($(this).data('timeout'));
                $(this).data('timeout', setTimeout(function () {
                    $thumbnailURLPreview.setSrc($thumbnailURLField.val());
                }, 250));
            },
            change: function () {
                $thumbnailURLPreview.setSrc($thumbnailURLField.val());
            }
        });
        $thumbnailURLPreview.entwine({
            onmatch: function () {
                this.load(function () {
                    if ($(this).attr("src") !== "") {
                        $(this).showIt();
                    } else {
                        $(this).hideIt();
                    }
                });
                var urlFieldVal = $($thumbnailURLField.selector).val();
                if (urlFieldVal && urlFieldVal !== this.attr("src")) {
                    this.setSrc(urlFieldVal);
                }
            },
            setSrc: function (thumbUrl) {
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
                    if (this.attr("src") !== thumbUrl) {
                        this.attr("src", thumbUrl);
                    } else {
                        this.showIt();
                    }
                } else {
                    $thumbnailURLPreview.hideIt();
                }
            },
            showIt: function () {
                $($thumbnailURLHolder.selector).hide();
                $(this).show();
            },
            hideIt: function () {
                $(this).hide();
                $($thumbnailURLHolder.selector).show();
            }
        });
        $codeField.entwine({
            onchange: function () {
                var
                        videoCode = this.val(),
                        parsedUrl = urlParser.parse(videoCode),
                        videoEmbedType = parsedUrl && parsedUrl.provider ? videoEmbedTypes[parsedUrl.provider] : null,
                        baseHref = $("base").attr("href");
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
                    if (videoEmbedType && videoEmbedType.url) {
                        $.post(baseHref + 'videoEmbedController/getOembedData/', {url: videoEmbedType.url.replace("{CODE}", videoCode)}, function (data) {
                            if (data && data.type === "video") {
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
    }
})(jQuery);
