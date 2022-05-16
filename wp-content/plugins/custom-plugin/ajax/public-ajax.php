<?php


add_action('wp_ajax_my_special_ajax_call_get_dental_data', 'get_dental_data');
add_action('wp_ajax_nopriv_my_special_ajax_call_get_dental_data', 'get_dental_data');

function get_dental_data(){        
    echo json_encode(
        array(
            'clinic_name' => get_the_title($_POST['post_id']),
            'doctor_name' => get_post_meta($_POST['post_id'],'cs_doctor_name',true),
        )
    );
    exit;
}