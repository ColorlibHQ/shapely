jQuery(document).ready( function($) {
  
  /* Media uploader */
  media_upload('.button.custom_media_button.button-primary');
    
  /* Clonning of Logo Client Widgets */
  jQuery(document).on('widget-added', function(e, widget){
    shapelySort();
  });
  jQuery(document).on('widget-updated', function(e, widget){
    shapelySort();
  });
  
  
  shapelySort();/* Client widget sorting and cloning*/
  
  /* Font awsome selector */
  jQuery('select.shapely-icon').change( function(){
    jQuery(this).siblings('span').removeClass().addClass('fa ' +jQuery(this).val());console.log(jQuery(this).val());
  });
  
  /* 
   * Function for sorting
   */
  function shapelySort(){
      jQuery('.client-sortable').sortable({
         handle: '.logo_heading' })
         .bind( 'sortupdate', function(event, ui) {
           var index = 0;
           var attrname = jQuery(this).find('input:first').attr('name');
           var attrbase = attrname.substring(0, attrname.indexOf('][') + 1);
           
           var attrid = jQuery(this).find('input:first').attr('id');
           var attrbaseid = attrid.substring(0, attrid.indexOf('-image_src') + 11);
           jQuery(this).find('li').each(function() {
             jQuery(this).find('.count').html(index+1);
             jQuery(this).find('.image_src').attr('id', attrbaseid+''+ index).attr('name', attrbase +'[client_logo][img]'+'[' + index + ']');
             jQuery(this).find('.custom_media_button').attr('data-fieldid', attrbaseid+''+ index );
             jQuery(this).find('.image_demo').attr('id', 'img_demo_'+attrbaseid+''+ index);
             jQuery(this).find('.client-link').attr('id', 'link-'+ index).attr('name', attrbase +'[client_logo][link]'+'[' + index + ']').trigger('change');
             index++;
           });
         });
         
         /* Cloning */
      jQuery('.clone-wrapper').cloneya().on('after_append.cloneya after_delete.cloneya', function (toClone, newClone) {
          jQuery('.client-sortable').trigger('sortupdate');
          jQuery(newClone).next('li').find('img').attr('src', '');
      });
  }
  
  /*
   * Function of media upload
   */
  function media_upload(button_class) {
        var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;


        $('body').on('click', button_class, function(e) {
            var button_id ='#'+$(this).attr('id');            
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(button_id);
            var field_id = $(this).attr('data-fieldid');
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment){
                if ( _custom_media  ) {
                    console.log(attachment.url);
                    //$('.custom_media_id').val(attachment.id);
                    $('#'+field_id).val(attachment.url).change();
                    $('#img_demo_'+field_id).attr('src', attachment.url);
                } else {
                    return _orig_send_attachment.apply( button_id, [props, attachment] );
                }
            }

            wp.media.editor.open(button);

            return false;
        });
    }  
});


