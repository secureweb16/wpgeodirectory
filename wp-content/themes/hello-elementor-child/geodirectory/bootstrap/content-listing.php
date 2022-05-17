<?php
/**
 * The template for displaying listing content within loops
 *
 * This template can be overridden by copying it to yourtheme/geodirectory/content-listing.php.
 *
 * HOWEVER, on occasion GeoDirectory will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wpgeodirectory.com/article/346-customizing-templates/
 * @author  AyeCode
 * @package GeoDirectory/Templates
 * @version 1.0.0
 *
 * @var int $row_gap_class The row gap class setting.
 * @var int $column_gap_class The column gap class setting.
 * @var int $card_border_class The card border class setting.
 * @var int $card_shadow_class The card shadow class setting.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $gd_post;

?>

<div <?php GeoDir_Post_Data::post_class("col ".$row_gap_class." ".$column_gap_class); ?> data-post-id="<?php echo esc_attr( $gd_post->ID ); ?>">
	<div class="card h-100 p-0 m-0 mw-100 <?php echo sanitize_html_class($card_border_class); echo " ".sanitize_html_class($card_shadow_class);?>">


           <?php
           if(get_post_type() === 'gd_place' ){ ?>

            <div class=" geodir-image-container">
                <?= do_shortcode("[gd_post_images]"); ?> 
            </div>

            <div class="wp-block-geodirectory-geodir-widget-post-title">
                <?= do_shortcode("[gd_post_title tag='h2' font_size_class='h5' overflow='ellipsis' ]"); ?>
                <span onClick="model_open(<?php echo get_the_ID(); ?>)"> LAB SHEET 
                    <img src="<?php echo plugins_url(); ?>\custom-plugin\public\css\ajax-loader.gif" id="imageloader_<?php echo get_the_ID(); ?>" style="display: none;">
                </span>
            </div>

            <div class="wp-block-geodirectory-geodir-widget-output-location">
                <?= do_shortcode("[gd_output_location location='listing' list_style='wrap']"); ?>
            </div>

            <div class="wp-block-geodirectory-geodir-widget-post-content">
                <?= do_shortcode("[gd_post_content limit='20' max_height='' read_more='' alignment='' strip_tags='false' ]"); ?>
            </div>

           <?php }else{
		          echo GeoDir_Template_Loader::archive_item_template_content( $gd_post->post_type );
              }
           ?>

       </div>
   </div>