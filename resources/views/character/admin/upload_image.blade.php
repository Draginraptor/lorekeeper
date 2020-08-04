@extends('character.layout')

@section('profile-title') {{ $character->fullName }} - Upload New Image @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url]) !!}

@include('character._header', ['character' => $character])

<p>This will add a new image to the character's gallery. The character's active image will be changed to the new one automatically. If the character is marked as visible, the owner of the character will be notified of the upload.</p> 

{!! Form::open(['url' => 'admin/character/'.$character->slug.'/image', 'files' => true]) !!}

<h3>Validity</h3>

<div class="form-group">
    {!! Form::checkbox('is_valid', 1, 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_valid', 'Is Valid', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, the image will still be visible, but displayed with a note that the image is not a valid reference.') !!}
</div>

<h3>Image Upload</h3>

<div class="form-group">
    {!! Form::label('Character Image') !!} {!! add_help('This is the full masterlist image. Note that the image is not protected in any way, so take precautions to avoid art/design theft.') !!}
    <div>{!! Form::file('image', ['id' => 'mainImage']) !!}</div>
    ---OR---
    <div>{!! Form::text('ext_url', null, ['class' => 'form-control', 'id' => 'extMainImage', 'placeholder' => 'Add a link to a dA or sta.sh upload']) !!}</div>
</div>
<div class="form-group">
    {!! Form::checkbox('use_custom_thumb', 1, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'useCustomThumbnail']) !!}
    {!! Form::label('use_custom_thumb', 'Upload Custom Thumbnail', ['class' => 'form-check-label ml-3']) !!} {!! add_help('A thumbnail is required for the upload (used for the masterlist). You can use the image cropper (crop dimensions can be adjusted in the site code), or upload a custom thumbnail.') !!}
</div>
<div class="card mb-3" id="thumbnailSelect">
    <div class="card-body">
        Select an image to use the thumbnail cropper, or add a dA link to see a preview.
    </div>
</div>
<div class="card mb-3" id="thumbnailCrop">
    <div class="card-body">
        <img src="#" id="cropper" class="hide" />
        {!! Form::hidden('x0', null, ['id' => 'cropX0']) !!}
        {!! Form::hidden('x1', null, ['id' => 'cropX1']) !!}
        {!! Form::hidden('y0', null, ['id' => 'cropY0']) !!}
        {!! Form::hidden('y1', null, ['id' => 'cropY1']) !!}
    </div>
</div>
<div class="card mb-3" id="thumbnailDaPreview">
    <div class="card-body">
        <p id="previewMessage"></p>
        <img src="#" id="thumbnailDa"/>
    </div>
</div>
<div class="card mb-3" id="thumbnailUpload">
    <div class="card-body">
        {!! Form::label('Thumbnail Image') !!} {!! add_help('This image is shown on the masterlist page.') !!}
        <div>{!! Form::file('thumbnail') !!}</div>
        <div class="text-muted">Recommended size: {{ Config::get('lorekeeper.settings.masterlist_thumbnails.width') }}px x {{ Config::get('lorekeeper.settings.masterlist_thumbnails.height') }}px</div>
    </div>
</div>
<p class="alert alert-info">
    This section is for crediting the image creators. The first box is for the designer's deviantART name (if any). If the designer has an account on the site, it will link to their site profile; if not, it will link to their dA page. The second is for a custom URL if they don't use dA. Both are optional - you can fill in the alias and ignore the URL, or vice versa. If you fill in both, it will link to the given URL, but use the alias field as the link name.
</p>
<div class="form-group">
    {!! Form::label('Designer(s)') !!}
    <div id="designerList">
        <div class="mb-2 d-flex">
            {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Alias']) !!}
            {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
            <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
        </div>
    </div>
    <div class="designer-row hide mb-2">
        {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Alias']) !!}
        {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
        <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
    </div>
</div>
<div class="form-group">
    {!! Form::label('Artist(s)') !!}
    <div id="artistList">
        <div class="mb-2 d-flex">
            {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
            {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
            <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist">+</a>
        </div>
    </div>
    <div class="artist-row hide mb-2">
        {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
        {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
        <a href="#" class="add-artist btn btn-link mb-2" data-toggle="tooltip" title="Add another artist">+</a>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Image Notes (Optional)') !!} {!! add_help('This section is for making additional notes about the image.') !!}
    {!! Form::textarea('image_description', old('image_description'), ['class' => 'form-control wysiwyg']) !!}
</div>

<h3>
    {{-- <div class="float-right"><a href="#" class="btn btn-info btn-sm" data-toggle="tooltip" title="This will fill the below fields with the same data as the character's current image. Note that this will overwrite any changes made below.">Fill Data</a></div> --}}
Traits
</h3>

<div class="form-group">
    {!! Form::label('Species') !!}
    {!! Form::select('species_id', $specieses, old('species_id') ? : $character->image->species_id, ['class' => 'form-control', 'id' => 'species']) !!}
</div>

<div class="form-group">
    {!! Form::label('Build') !!} 
    {!! Form::select('subtype_id', $subtypes, old('subtype_id') ? : $character->image->subtype_id, ['class' => 'form-control', 'id' => 'subtype']) !!}
</div>

<div class="form-group">
    {!! Form::label('Character Rarity') !!}
    {!! Form::select('rarity_id', $rarities, old('rarity_id') ? : $character->image->rarity_id, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Traits') !!}
    <div id="featureList">
    </div>
    <div><a href="#" class="btn btn-primary" id="add-feature">Add Trait</a></div>
    <div class="feature-row hide mb-2">
        {!! Form::select('feature_id[]', $features, null, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Select Trait']) !!}
        {!! Form::text('feature_data[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Extra Info (Optional)']) !!}
        <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
    </div>
</div>

<div class="text-right">
    {!! Form::submit('Create Image', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {

    // Cropper ////////////////////////////////////////////////////////////////////////////////////

    var $useCustomThumbnail = $('#useCustomThumbnail');
    var $thumbnailSelect = $('#thumbnailSelect');
    var $thumbnailCrop = $('#thumbnailCrop');
    var $thumbnailUpload = $('#thumbnailUpload');
    var $thumbnailDaPreview = $('#thumbnailDaPreview');

    var useCustomThumbnail = $useCustomThumbnail.is(':checked');

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
            if(($('#mainImage')[0] && $('#mainImage')[0].value)) { unhideCrop(); }
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
                updateCropValues();
                $('#cropSelect').addClass('hide');
                $cropper.removeClass('hide');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#mainImage").change(function() {
        $('#extMainImage')[0].value = null;
        $useCustomThumbnail.bootstrapToggle('off');
        readURL(this);
    });

    var embed_route = "/embed?url="

    function fetchEmbeds(url) {
        // Set current embed and error as loading
        $('#previewMessage').html('Loading...');
        $('#thumbnailDa').attr('src', '/images/loading.gif');
        if(typeof url !== 'undefined') {
            $.get(embed_route + url, function(data, status) {
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

    $("#extMainImage").focusout(function() {
        $("#mainImage")[0].value = null;
        $useCustomThumbnail.bootstrapToggle('off');
        fetchEmbeds(this.value);
    });

    function updateCropValues() {
        var values = c.get();
        $x0.val(values.points[0]);
        $y0.val(values.points[1]);
        $x1.val(values.points[2]);
        $y1.val(values.points[3]);
    }
});
    
</script>
@endsection