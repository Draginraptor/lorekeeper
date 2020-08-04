<script>
$( document ).ready(function() {

    // Cropper ////////////////////////////////////////////////////////////////////////////////////

    var $useCustomThumbnail = $('#useCustomThumbnail');
    var $thumbnailSelect = $('#thumbnailSelect');
    var $thumbnailCrop = $('#thumbnailCrop');
    var $thumbnailUpload = $('#thumbnailUpload');
    var $thumbnailDaPreview = $('#thumbnailDaPreview');

    var useCustomThumbnail = $useCustomThumbnail.is(':checked');
    var useUploaded = false;

    updatePreviewArea();

    $useCustomThumbnail.on('change', function(e) {
        useCustomThumbnail = $useCustomThumbnail.is(':checked');
        updatePreviewArea();
    });

    function updatePreviewArea() {
        if(useCustomThumbnail) {
            unhideUpload();
        }
        else {
            if(($('#mainImage')[0] && $('#mainImage')[0].value) || useUploaded) { unhideCrop(); }
            else if($('#extMainImage')[0] && $('#extMainImage')[0].value) { unhideDaPreview(); }
            else { unhideSelect(); }
        }
    }
    
    function unhideSelect() {
        $thumbnailUpload.addClass('hide');
        $thumbnailSelect.removeClass('hide');
        $thumbnailCrop.addClass('hide');
        $thumbnailDaPreview.addClass('hide');
    }

    function unhideCrop() {
        $thumbnailUpload.addClass('hide');
        $thumbnailSelect.addClass('hide');
        $thumbnailCrop.removeClass('hide');
        $thumbnailDaPreview.addClass('hide');
    }

    function unhideDaPreview() {
        $thumbnailUpload.addClass('hide');
        $thumbnailSelect.addClass('hide');
        $thumbnailCrop.addClass('hide');
        $thumbnailDaPreview.removeClass('hide');
    }

    function unhideUpload() {
        $thumbnailUpload.removeClass('hide');
        $thumbnailSelect.addClass('hide');
        $thumbnailCrop.addClass('hide');
        $thumbnailDaPreview.addClass('hide');
    }

    // Designers and artists //////////////////////////////////////////////////////////////////////

    $('.add-designer').on('click', function(e) {
        e.preventDefault();
        addDesignerRow($(this));
    });
    function addDesignerRow($trigger) {
        var $clone = $('.designer-row').clone();
        $('#designerList').append($clone);
        $clone.removeClass('hide designer-row');
        $clone.addClass('d-flex');
        $clone.find('.add-designer').on('click', function(e) {
            e.preventDefault();
            addDesignerRow($(this));
        })
        $trigger.css({ visibility: 'hidden' });
    }
    
    $('.add-artist').on('click', function(e) {
        e.preventDefault();
        addArtistRow($(this));
    });
    function addArtistRow($trigger) {
        var $clone = $('.artist-row').clone();
        $('#artistList').append($clone);
        $clone.removeClass('hide artist-row');
        $clone.addClass('d-flex');
        $clone.find('.add-artist').on('click', function(e) {
            e.preventDefault();
            addArtistRow($(this));
        })
        $trigger.css({ visibility: 'hidden' });
    }

    // Traits /////////////////////////////////////////////////////////////////////////////////////
    
    $('.initial.feature-select').selectize({
        render: {
            item: featureSelectedRender
        }
    });
    $('#add-feature').on('click', function(e) {
        e.preventDefault();
        addFeatureRow();
    });
    $('.remove-feature').on('click', function(e) {
        e.preventDefault();
        removeFeatureRow($(this));
    })
    function addFeatureRow() {
        var $clone = $('.feature-row').clone();
        $('#featureList').append($clone);
        $clone.removeClass('hide feature-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-feature').on('click', function(e) {
            e.preventDefault();
            removeFeatureRow($(this));
        })
        $clone.find('.feature-select').selectize({
            render: {
                item: featureSelectedRender
            }
        });
    }
    function removeFeatureRow($trigger) {
        $trigger.parent().remove();
    }
    function featureSelectedRender(item, escape) {
        return '<div><span>' + escape(item["text"].trim()) + ' (' + escape(item["optgroup"].trim()) + ')' + '</span></div>';
    }

    // Croppie ////////////////////////////////////////////////////////////////////////////////////

    var thumbnailWidth = {{ Config::get('lorekeeper.settings.masterlist_thumbnails.width') }};
    var thumbnailHeight = {{ Config::get('lorekeeper.settings.masterlist_thumbnails.height') }};
    var $cropper = $('#cropper');
    var c = null;
    var $x0 = $('#cropX0');
    var $y0 = $('#cropY0');
    var $x1 = $('#cropX1');
    var $y1 = $('#cropY1');
    var zoom = 0;

    @if(isset($useUploaded) && $useUploaded)
        useUploaded = true; 
        unhideCrop();
        // This is for modification of an existing image:
        c = new Croppie($cropper[0], {
            viewport: {
                width: thumbnailWidth,
                height: thumbnailHeight
            },
            boundary: { width: thumbnailWidth + 100, height: thumbnailHeight + 100 },
            update: function() {
                updateCropValues();
            }
        });
        c.bind({
            url: $cropper.data('url'),
            // points: [$x0.val(),$x1.val(),$y0.val(),$y1.val()], // this does not work
        }).then(function() {
            updateCropValues();
        });
        //console.log(($x1.val() - $x0.val()) / thumbnailWidth);
    @else
        function readURL(input) {
            // First reset croppie
            if(c) {
                c.destroy();
                c.element.outerHTML = c.element.innerHTML;
            }
            c = null;
            $cropper = $('#cropper');
            $cropper.attr('src', '#');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $cropper.attr('src', e.target.result);
                    c = new Croppie($cropper[0], {
                        viewport: {
                            width: thumbnailWidth,
                            height: thumbnailHeight
                        },
                        boundary: { width: thumbnailWidth + 100, height: thumbnailHeight + 100 },
                        update: function() {
                            updateCropValues();
                        }
                    });
                    //console.log(c);
                    updateCropValues();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#mainImage").change(function() {
            $('#extMainImage')[0].value = null;
            $useCustomThumbnail.bootstrapToggle('off');
            readURL(this);
        });

        function fetchEmbeds(url) {
            // Set current embed and error as loading
            $('#previewMessage').html('Loading...');
            $('#thumbnailDa').attr('src', '/images/loading.gif');
            if(typeof url !== 'undefined') {
                $.get("{{ url('embed') }}?url="+url, function(data, status) {
                    if(typeof data['error'] !== 'undefined') {
                        $('#previewMessage').html('Error: ' + data['error']);
                        $('#thumbnailDa').attr('src', '#');
                    }
                    else
                    {
                        $('#previewMessage').html('Image found: <a href=' + url + '>' + url + '</a>');
                        $('#thumbnailDa').attr('src', data['thumbnail_url']);
                    }
                    updatePreviewArea();
                }).catch(function() {
                    $('#previewMessage').html('Error: Server failed to process request');
                    $('#thumbnailDa').attr('src', '#');
                });
            }
            else {
                $('#previewMessage').html('Error: URL is undefined');
                $('#thumbnailDa').attr('src', '#');
            }
        }

        $("#extMainImage").on('input', function() {
            $("#mainImage")[0].value = null;
            $useCustomThumbnail.bootstrapToggle('off');
            fetchEmbeds(this.value);
        });
    @endif

    function updateCropValues() {
        var values = c.get();
        console.log(values);
        //console.log([$x0.val(),$x1.val(),$y0.val(),$y1.val()]);
        $x0.val(values.points[0]);
        $y0.val(values.points[1]);
        $x1.val(values.points[2]);
        $y1.val(values.points[3]);
    }

    
});
    
</script>