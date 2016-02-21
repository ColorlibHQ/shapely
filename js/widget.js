jQuery(document).ready( function($) {
  
  /* Media uploader */
  media_upload('.upload_image_button');
    
  /* Clonning of Logo Client Widgets */
  jQuery(document).on('widget-updated', function(e, widget){
    jQuery('.clone-wrapper').cloneya();
    flexibleSort();
  });
  
  /* Cloning */
  jQuery('.clone-wrapper').cloneya().on('after_append.cloneya after_delete.cloneya', function (toClone, newClone) {
      jQuery('.client-sortable').trigger('sortupdate');
      jQuery(newClone).next('li').find('img').attr('src', '');
  });
  
  flexibleSort();/* Client widget sorting */
    
});

/* 
 * Function for sorting
 */
function flexibleSort(){
    jQuery('.client-sortable').sortable({
       handle: '.logo_heading',
       update: function(event, ui) {
                 var index = 0;
                 var attrname = jQuery(this).find('input:first').attr('name');
                 var attrbase = attrname.substring(0, attrname.indexOf('][') + 1);
                 jQuery(this).find('li').each(function() {
                   jQuery(this).find('.count').html(index+1);
                   jQuery(this).find('.image_src').attr('id', 'image_src-'+ index).attr('name', attrbase +'[client_logo][img]'+'[' + index + ']');
                   jQuery(this).find('.client-link').attr('id', 'link-'+ index).attr('name', attrbase +'[client_logo][link]'+'[' + index + ']').trigger('change');
                   index++;
                 });
               }
    });
}

/*
 * Function of media upload
 */
function media_upload(button_class) {
  var image_field;
  $("body").on('click', button_class, function(evt){
    image_field = $(this).siblings('.image_src');
    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
    return false;
  });
  window.send_to_editor = function(html) {
    imgurl = $(html).find('img').attr('src');console.log(imgurl);
    image_field.val(imgurl).change();
    image_field.siblings(".image_demo").attr("src", imgurl );
    tb_remove();
  }
}