var oTable;
jQuery(document).ready(function($){

var _custom_media = true,
      _orig_send_attachment = wp.media.editor.send.attachment;

  jQuery('.meta_upload').click(function(e) {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    var id = button.attr('id').replace('button_', '');
    _custom_media = true;
    wp.media.editor.send.attachment = function(props, attachment){
      if ( _custom_media ) {
        $("#"+id).val(attachment.url).select();
      } else {
        return _orig_send_attachment.apply( this, [props, attachment] );
      };
    }

    wp.media.editor.open(button);
    return false;
  });


	jQuery('#jci-submit').click(function(){
		var stat = 1;
		jQuery('.data-required').each(function() {
			var val = jQuery(this).val();
			if(val == '') {
				jQuery(this).addClass('jci-not-valid');
				stat = 0;
			}
			else {
				jQuery(this).removeClass('jci-not-valid');
			}
		});
	
		if(stat == 1) {
			return true;
		}
		else {
			return false;
		}

	});

	jQuery('.data-required').keyup(function(){
		var val = jQuery(this).val();
			if(val == '') {
			jQuery(this).addClass('jci-not-valid');
		}
		else {
			jQuery(this).removeClass('jci-not-valid');
		}
	});
	

	jQuery('[data-dismiss="alert"]').click(function(){
		jQuery(this).parent().remove();
	});


});
