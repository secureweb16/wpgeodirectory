<?php if(count($finalData) > 0 ) { ?>
<table>
	<thead>
		<tr>
			<th> Dr. Name </th>
			<th> Clinic Name </th>
			<th> Patient Name </th>
			<th> Patient Age </th>
			<th> Date </th>
			<th> Gender </th>
			<th> Restoration Type </th>
			<th> Implant system </th>
			<th> Material Type </th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($finalData as $key => $value ){ ?>
			<tr class="page-pagination">
				<td> <?php echo get_post_meta($value->ID,'cs_dentist_name',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_clinic_name',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_patient_name',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_patient_age',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_date',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_gender',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_restoration_type',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_implant_system',true) ?> </td>
				<td> <?php echo get_post_meta($value->ID,'cs_material_type',true) ?> </td>
			</tr>
		<?php  } ?>
		
	</tbody>
</table>
<div class="bottomdiv">
	<h4>Total:  <?php echo count($finalData); ?></h4>
	<div class="pagination coinpaginaton">
		<span class="prev"><i class="fa fa-angle-double-left" aria-hidden="true"></i></span>
		<ul id="pagin"></ul>
		<span class="next"><i class="fa fa-angle-double-right" aria-hidden="true"></i></span>
	</div>
</div>
<?php } else{
	echo '<div> No form submited. </div>';
} ?>