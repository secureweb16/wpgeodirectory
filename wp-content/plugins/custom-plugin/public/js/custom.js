function model_open(id){ 
  jQuery('#custom-poup-form .success').remove();
  jQuery('#custom-poup-form .error').remove();
  jQuery('#imageloader_'+id).show();
  jQuery.ajax({
    type:"POST",
    url:my_ajax_object.ajax_url,
    data: {
      action:'my_special_ajax_call_get_dental_data',
      post_id:id,
    },
    success:function(response){
      const obj = JSON.parse(response);          
      jQuery('input[name="cs_clinic_name"]').val(obj.clinic_name);
      jQuery('input[name="cs_dentist_name"]').val(obj.doctor_name);
      jQuery('input[name="cs_post_id"]').val(id);
      jQuery('#custom-poup-form').addClass('showform');
      jQuery('#imageloader_'+id).hide();
    }
  });
}

function closepoup(){
  jQuery('#custom-poup-form').removeClass('showform');
}

