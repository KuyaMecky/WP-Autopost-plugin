'use strict';

jQuery(function($) {

// This is used to upload more than one custom images when selecting image posting for facebook at post level

  var file_frame;

  $(document).on('click', '#wpw_auto_poster_meta a.gallery-add', function(e) {

    e.preventDefault();

    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: true
    });

    file_frame.on('select', function() {
      var listIndex = $('#gallery-metabox-list li').index($('#gallery-metabox-list li:last')),
          selection = file_frame.state().get('selection');
      var index;

      selection.map(function(attachment, i) {
        attachment = attachment.toJSON(),
        index      = listIndex + (i + 1);
        var src_url = "";
        if( attachment.sizes.thumbnail ){
          src_url = attachment.sizes.thumbnail.url;
        } else{
          src_url = attachment.url;
        }
        
        $('#gallery-metabox-list').append('<li><input type="hidden" name="wpw_auto_poster_gallery[]" value="' + attachment.id + '"><span class="image-container"><img class="image-preview" src="' + src_url + '"></span><a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br><small><a class="remove-image" href="#">Remove image</a></small></li>');
      });
    });

    makeSortable();
    
    file_frame.open();

  });

  $(document).on('click', '#wpw_auto_poster_meta a.change-image', function(e) {

    e.preventDefault();

    var that = $(this);

    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: false
    });

    file_frame.on( 'select', function() {
      attachment = file_frame.state().get('selection').first().toJSON();
      that.parent().find('input:hidden').attr('value', attachment.id);
      that.parent().find('img.image-preview').attr('src', attachment.sizes.thumbnail.url);
    });

    file_frame.open();

  });


  function makeSortable() {
    $('#gallery-metabox-list').sortable({
      opacity: 0.6,
      stop: function() {
      }
    });

    $('#gallery-metabox-list-insta').sortable({
        opacity: 0.6,
        stop: function() {
        }
      });
  }

  $(document).on('click', '#wpw_auto_poster_meta a.remove-image', function(e) {
    e.preventDefault();

    $(this).parents('li').animate({ opacity: 0 }, 200, function() {
      $(this).remove();
    });
  });

  makeSortable();


  	/* Insta Gallery */
      $(document).on('click', '#wpw_auto_poster_meta a.gallery-add-insta', function(e) {

        e.preventDefault();
    
        if (file_frame) file_frame.close();
    
        file_frame = wp.media.frames.file_frame = wp.media({
          title: $(this).data('uploader-title'),
          button: {
            text: $(this).data('uploader-button-text'),
          },
          multiple: true,
        });
        
        file_frame.on('select', function() {
          var listIndex = $('#gallery-metabox-list li').index($('#gallery-metabox-list-insta li:last')),
              selection = file_frame.state().get('selection');
          var index;
        
          selection.map(function(attachment, i) {
            attachment = attachment.toJSON(),
            index      = listIndex + (i + 1);
            var src_url = "";
            
            if( attachment.sizes.thumbnail ){
                src_url = attachment.sizes.thumbnail.url;
            } else{
            src_url = attachment.url;
            }   
            
            $('#gallery-metabox-list-insta').append('<li><input type="hidden" name="wpw_auto_poster_gallery_insta[]" value="' + attachment.id + '"><span class="image-container-insta"><img class="image-preview" src="' + src_url + '"></span><a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br><small><a class="remove-image" href="#">Remove image</a></small></li>');
          });
        });
 
        makeSortable();
        
        file_frame.open();
    
      });
  	/* Insta Gallery */

});