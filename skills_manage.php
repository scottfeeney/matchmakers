<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/adminstaff.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/adminstaff.php';
		require_once './classes/skillcategory.php';
	}
	
	
	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 3) {
		// not staff, send them back to home
        header("Location: home.php");
		die();				
	}
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
	
	//skill categories
	
	$skillCategories = \Classes\SkillCategory::GetSkillCategories();
	
?>	

	<section class="manage-skills">
	
		<h2>Manage Skills</h2>
		
		<div id="manageSkillSelector" class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-10">
						<label for="Location">Category:</label>
						<select name="SkillCategoryId" id="SkillCategoryId" class="form-control skill-manage">
							<option value=""></option>
							<?php foreach ($skillCategories as $skillCategory) { ?>
								<option value="<?php echo $skillCategory->skillCategoryId; ?>"><?php echo $skillCategory->skillCategoryName; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-2 skill-add-button">
						<button type="button" class="btn btn-primary skill-manage-button" data-toggle="modal" data-id="0" data-name="" data-target="#skillManageModel">
							Add Skill
						</button>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="main-form">
			
			<div class="skill-manage-skills-list"></div>
			<div class="skill-manage-skills-loading">
				<i class="fas fa-spinner fa-spin"></i>
			</div>

			<input type="hidden" id="hidSkillId" value="" />
			<input type="hidden" id="hidSkillName" value="" />
			
		</div>


	</section>
		
		
		
	<div class="modal fade" id="skillManageModel">
		<div class="modal-dialog">
			<div class="modal-content">
      
				<div class="modal-header">
					<h4 class="modal-title"><span class="manage-skill-spinner"><i class="fas fa-spinner fa-spin"></i></span> Manage Skill</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="skill-manage-add-error"></div>
					<div class="form-group">
						<label for="Address1">Skill Name:</label>
						<input type="text" class="form-control" name="SkillName" id="SkillName" maxlength="50" value="">
					</div>
						
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary save-skill">Save</button>
				</div>
        
			</div>
		</div>
	</div>		
	
	<div class="modal fade" id="skillDeleteModel">
		<div class="modal-dialog">
			<div class="modal-content">
      
				<div class="modal-header">
					<h4 class="modal-title"><span class="delete-skill-spinner"><i class="fas fa-spinner fa-spin"></i></span> Delete Skill</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="skill-delete-error"></div>
					<div class="skill-delete-message"></div>
						
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-primary delete-skill">Yes</button>
				</div>
        
			</div>
		</div>
	</div>	
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
