<div id="custom-poup-form" class="custom-poup-form <?php if(isset($responce['status']) && $responce['status'] === 403) { echo 'showform'; } ?>">
	<div class="popup_wrap">
		<span class="close_btn" onClick="closepoup()">x</span>

		<?php if(isset($responce['status']) && $responce['status'] === 403) {

				echo '<div class="error"> Please fill the required fields!  </div>';

		} ?>

		<form method="post">
			<div class="form_step">
				<h4>Order Details<span>*</span></h4>
				<input type="hidden" name="cs_post_id">
				<div class="d-flex">		
					<div class="form-group w-50">
						<input class="form-control" type="text" name="cs_dentist_name" readonly placeholder="Dentist Name" value="<?php if(isset($responce['value']['cs_dentist_name'])) { echo @$responce['value']['cs_dentist_name']; } ?>">
					</div>
					
					<div class="form-group w-50">
						<input class="form-control" type="text" name="cs_clinic_name" readonly placeholder="Clinic Name" value="<?php if(isset($responce['value']['cs_clinic_name'])) { echo @$responce['value']['cs_clinic_name']; } ?>">
					</div>

					<div class="form-group w-50">
						<input class="form-control" type="text" name="cs_email" placeholder="Email" value="<?php if(isset($responce['value']['cs_email'])) { echo @$responce['value']['cs_email']; } ?>">
						<?php if(isset($responce['error']['cs_email'])) { ?> <div class="error"><?php echo @$responce['error']['cs_email']; ?></div> <?php } ?>
					</div>

					<div class="form-group w-50">
						<input class="form-control" type="text" name="cs_phone" placeholder="Phone" value="<?php if(isset($responce['value']['cs_phone'])) { echo @$responce['value']['cs_phone']; } ?>">
						<?php if(isset($responce['error']['cs_phone'])) { ?> <div class="error"><?php echo @$responce['error']['cs_phone']; ?></div> <?php } ?>
					</div>

					<div class="form-group w-100">
						<input class="form-control" type="date" name="cs_date" placeholder="Date" value="<?php if(isset($responce['value']['cs_date'])) { echo @$responce['value']['cs_date']; } ?>">
						<?php if(isset($responce['error']['cs_date'])) { ?> <div class="error"><?php echo @$responce['error']['cs_date']; ?></div> <?php } ?>
					</div>

					<div class="form-group w-50">
						<input class="form-control" type="text" name="cs_patient_name" placeholder="Patient Name" value="<?php if(isset($responce['value']['cs_patient_name'])) { echo @$responce['value']['cs_patient_name']; } ?>">
						<?php if(isset($responce['error']['cs_patient_name'])) { ?> <div class="error"><?php echo @$responce['error']['cs_patient_name']; ?></div> <?php } ?>
					</div>

					<div class="form-group w-50">
						<input class="form-control" type="text" name="cs_patient_age" placeholder="Patient Age" value="<?php if(isset($responce['value']['cs_patient_age'])) { echo @$responce['value']['cs_patient_age']; } ?>">
						<?php if(isset($responce['error']['cs_patient_age'])) { ?> <div class="error"><?php echo @$responce['error']['cs_patient_age']; ?></div> <?php } ?>
					</div>

					<div class="form-group">
						<p class="w-100">Gender<span>*</span></p>
						<div class="d-flex">
							<div class="gender">
								<label class="gender_filed">Male
								  <input class="form-control" type="radio" name="cs_gender" value="Male" <?php if(@$responce['value']['cs_gender'] == 'Male') { echo'checked'; } ?>>
								  <span class="checkmark"></span>
								</label>
							</div>
							<div class="gender">
								<label class="gender_filed">Female
								  <input class="form-control" type="radio" name="cs_gender" value="Female" value="Male" <?php if(@$responce['value']['cs_gender'] == 'Female') { echo'checked'; } ?> >
								  <span class="checkmark"></span>
								</label>
							</div>
						</div>
						<?php if(isset($responce['error']['cs_gender'])) { ?> <div class="error"><?php echo @$responce['error']['cs_gender']; ?></div> <?php } ?>
					</div>
				</div>
			</div><!--form_step-->

			<div class="form_step">
				<h4>Restoration Type<span>*</span></h4>
				<div class="form-group">
					<div class="d-flex">
						<div class="w-50">
							<label class="gender_filed">Crown
								<input class="form-control" type="radio" name="cs_restoration_type" value="Crown" <?php if(@$responce['value']['cs_restoration_type'] == 'Crown') { echo'checked'; } ?> >
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Post and core
								<input class="form-control" type="radio" name="cs_restoration_type" value="Post and core" <?php if(@$responce['value']['cs_restoration_type'] == 'Post and core') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Largest
								<input class="form-control" type="radio" name="cs_restoration_type" value="Largest" <?php if(@$responce['value']['cs_restoration_type'] == 'Largest') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Bridge
								<input class="form-control" type="radio" name="cs_restoration_type" value="Bridge" <?php if(@$responce['value']['cs_restoration_type'] == 'Bridge') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Dignstatic wax up
								<input class="form-control" type="radio" name="cs_restoration_type" value="Dignstatic wax up" <?php if(@$responce['value']['cs_restoration_type'] == 'Dignstatic wax up') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Webnar
								<input class="form-control" type="radio" name="cs_restoration_type" value="Webnar" <?php if(@$responce['value']['cs_restoration_type'] == 'Webnar') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Way
								<input class="form-control" type="radio" name="cs_restoration_type" value="Way" <?php if(@$responce['value']['cs_restoration_type'] == 'Way') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Guide
								<input class="form-control" type="radio" name="cs_restoration_type" value="Guide" <?php if(@$responce['value']['cs_restoration_type'] == 'Guide') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Other
								<input class="form-control" type="radio" name="cs_restoration_type" value="Other" <?php if(@$responce['value']['cs_restoration_type'] == 'Other') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>
					</div>
					<?php if(isset($responce['error']['cs_restoration_type'])) { ?> <div class="error"><?php echo @$responce['error']['cs_restoration_type']; ?></div> <?php } ?>
				</div>

				<div class="form-group">
					<label>Please add implant system <span>*</span></label>
					<input class="form-control" type="text" name="cs_implant_system" placeholder="Please add implant system" value="<?php if(isset($responce['value']['cs_implant_system'])) { echo @$responce['value']['cs_implant_system']; } ?>">
					<?php if(isset($responce['error']['cs_implant_system'])) { ?> <div class="error"><?php echo @$responce['error']['cs_implant_system']; ?></div> <?php } ?>
				</div>
			</div><!--form_step-->

			<div class="form_step">
				<h4>Material Type <span>*</span></h4>
				<div class="form-group">
					<div class="d-flex">
						<div class="w-50">
							<label class="gender_filed">Crown
								<input class="form-control" type="radio" name="cs_material_type" value="Crown" <?php if(@$responce['value']['cs_material_type'] == 'Crown') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Post and core
								<input class="form-control" type="radio" name="cs_material_type" value="Post and core" <?php if(@$responce['value']['cs_material_type'] == 'Post and core') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Largest
								<input class="form-control" type="radio" name="cs_material_type" value="Largest" <?php if(@$responce['value']['cs_material_type'] == 'Largest') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Bridge
								<input class="form-control" type="radio" name="cs_material_type" value="Bridge" <?php if(@$responce['value']['cs_material_type'] == 'Bridge') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Dignstatic wax up
								<input class="form-control" type="radio" name="cs_material_type" value="Dignstatic wax up" <?php if(@$responce['value']['cs_material_type'] == 'Dignstatic wax up') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Webnar
								<input class="form-control" type="radio" name="cs_material_type" value="Webnar" <?php if(@$responce['value']['cs_material_type'] == 'Webnar') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Way
								<input class="form-control" type="radio" name="cs_material_type" value="Way" <?php if(@$responce['value']['cs_material_type'] == 'Way') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Guide
								<input class="form-control" type="radio" name="cs_material_type" value="Guide" <?php if(@$responce['value']['cs_material_type'] == 'Guide') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>

						<div class="w-50">
							<label class="gender_filed">Other
								<input class="form-control" type="radio" name="cs_material_type" value="Other" <?php if(@$responce['value']['cs_material_type'] == 'Other') { echo'checked'; } ?>>
								<span class="checkmark"></span>
							</label>
						</div>
				</div>
				<?php if(isset($responce['error']['cs_material_type'])) { ?> <div class="error"><?php echo @$responce['error']['cs_material_type']; ?></div> <?php } ?>
			</div>

			<div class="form-group">
				<input class="form-control" type="submit" name="cs_submit" value="Submit">
			</div>

		</form>	

	</div>
</div>