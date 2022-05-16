<?php



/* ADMIN SECTION AJEX */
add_action('wp_ajax_my_special_ajax_call_update_promotion_date', 'update_promotion_date');
add_action('wp_ajax_nopriv_my_special_ajax_update_promotion_date', 'update_promotion_date');

function update_promotion_date(){
	
	$status = the_coin_listed_Promottion(null);
	$coinId = $_POST['postid'];
	$start_date = date('Y-m-d',strtotime($_POST['pro_start_date']));
	$end_date 	= date('Y-m-d',strtotime($_POST['pro_end_date']));
	update_post_meta($coinId,'pro_start_date',$start_date);
	update_post_meta($coinId,'pro_end_date',$end_date);
	update_post_meta($coinId,'prmotaion_status',$status);
	echo "Promation is update";
	exit;
}
