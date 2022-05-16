function model_open(id){
  console.log('id',id);
      jQuery.ajax({
        type:"POST",
        url:my_ajax_object.ajax_url,
        data: {
          action:'my_special_ajax_call_get_dental_data',
          post_id:id,
        },
        success:function(response){
          const obj = JSON.parse(response);
          console.log('response',obj)
          jQuery('input[name="cs_clinic_name"]').val(obj.clinic_name)
          jQuery('input[name="cs_dentist_name"]').val(obj.doctor_name)
          // jQuery('.loadershow').hide();
          // jQuery('.updatetoken').removeClass('showloader');
          // if(response !='success'){
          //   jQuery('.errordiv').html(response);
          // }else{
          //   window.location = permlink;
          // }
        }
      });
    }