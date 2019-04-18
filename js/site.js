$(document).ready(function () {
	
	resizePage();
	
	$( window ).resize(function() {
		resizePage();
	});
	

	// bootstrap validation
	var forms = document.getElementsByClassName('needs-validation');

	var validation = Array.prototype.filter.call(forms, function(form) {
		
		form.addEventListener('submit', function(event) {
	  
			$(form).find('input:text').each(function(){
				$(this).val($.trim($(this).val()));
			});
	  
			if (form.checkValidity() === false) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		}, false);
	});
	
	$('.jobseeker-currently-studying-field').change(function() {
		if ($('.jobseeker-currently-studying-field:checked').val() == 'YES') {
			$('.job-seeker-current-study-level-group').show();
			//$('.job-seeker-current-study-level-group select').prop('required',true);
		}
		else {
			$('.job-seeker-current-study-level-group').hide();
			//$('.job-seeker-current-study-level-group select').prop('required',false);
		}
	});

});	

function resizePage() {
	
	if ($(".home-page .jumbotron").length > 0) {
		$(".home-page .jumbotron").css({'height' : $(".main-container").height() + 'px'});
	}
}





/* skill manage */

$(document).ready(function () {
	
	if ($(".skill-manage").length > 0) {
		
		
		$('#SkillCategoryId').on('change', function() {
			if ($(this).val() == '') {
				$(".main-form").hide();
				$(".skill-add-button").hide();
			}
			else {
				$(".main-form").show();
				$(".skill-add-button").show();
				getSkillsByCategory($(this).val());
			}
		});
		
		$('.save-skill').click(function(){
			
			$('.manage-skill-spinner').show();
			
			var data = {}
			data['Mode'] = "save";
			data['SkillId'] = $('#hidSkillId').val();
			data['SkillCategoryId'] = $('#SkillCategoryId').val();
			data['SkillName'] = $("#SkillName").val();


			//console.log(data);
			
			$.ajax({
				type: 'POST',
				url: '/api/skills_manage.php',
				data: JSON.stringify(data),
				contentType: 'application/json; charset=utf-8',
				dataType: 'json',
				success: function (response) {
					if (response.hasError == false) {
						$('#skillManageModel').modal('hide');
						getSkillsByCategory($('#SkillCategoryId').val());
					}
					else {
						$('.skill-manage-add-error').html(response.errorMessage).show();
					}
					$('.manage-skill-spinner').hide();
				},
				error: function (xhr, status, error) {
					$('.manage-skill-spinner').hide();
					console.log(error);
				}
			});
			
		});
		
		
		$('.delete-skill').click(function(){
			
			$('.delete-skill-spinner').show();
			
			var data = {}
			data['Mode'] = "delete";
			data['SkillId'] = $('#hidSkillId').val();

			
			$.ajax({
				type: 'POST',
				url: '/api/skills_manage.php',
				data: JSON.stringify(data),
				contentType: 'application/json; charset=utf-8',
				dataType: 'json',
				success: function (response) {
					if (response.hasError == false) {
						$('#skillDeleteModel').modal('hide');
						getSkillsByCategory($('#SkillCategoryId').val());
					}
					else {
						$('.skill-delete-error').html(response.errorMessage).show();
					}
					$('.delete-skill-spinner').hide();
				},
				error: function (xhr, status, error) {
					$('.delete-skill-spinner').hide();
					console.log(error);
				}
			});
			
		});
		
		$("#skillManageModel").on('show.bs.modal', function () {
			$('.skill-manage-add-error').hide();
			$('.manage-skill-spinner').hide();
			$("#SkillName").val($('#hidSkillName').val());
		});
	  
		$(".skill-manage-button").click(function(){
			skillManage($(this).attr('data-id'), $(this).attr('data-name'))
		});		
		
		
		$("#skillDeleteModel").on('show.bs.modal', function () {
			$('.skill-delete-error').hide();
			$('.delete-skill-spinner').hide();
			$(".skill-delete-message").html("Are you sure you want to delete the skill '" + $('#hidSkillName').val() + "'?");
		});
				
	}
	
	
});	

function skillManage(skillId, skillName) {
	$('#hidSkillId').val(skillId);
	$('#hidSkillName').val(skillName);
	//console.log($('#hidSkillId').val(), $('#hidSkillName').val());
}

function skillDelete(skillId, skillName) {
	$('#hidSkillId').val(skillId);
	$('#hidSkillName').val(skillName);
}

function getSkillsByCategory($skillCategoryId) {
	
	$('.skill-manage-skills-list').hide();
	$('.skill-manage-skills-loading').show();
	
	var data = {}
	data['Mode'] = "list";
	data['SkillCategoryId'] = $skillCategoryId;


	//console.log(data);
	
	$.ajax({
		type: 'POST',
		url: '/api/skills_manage.php',
		data: JSON.stringify(data),
		contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		success: function (response) {
			var h = '';
			$.each(response, function(i, skill) {
				h += '<div class="card"><div class="card-body"><div class="row">';
				h += '<div class="col-10">' + skill.skillName + '</div>';
				h += '<div class="col-2 skill-manage-edit-delete">';
				h += '<div class="skill-manage-edit" data-toggle="modal" data-target="#skillManageModel" onclick="skillManage(\'' + skill.skillId + '\',\'' + skill.skillName + '\');"><i class="far fa-edit"></i></div>';
				h += '<div class="skill-manage-delete" data-toggle="modal" data-target="#skillDeleteModel" onclick="skillDelete(\'' + skill.skillId + '\',\'' + skill.skillName + '\');"><i class="far fa-times-circle"></i></div>';
				h += '</div>';
				h += '</div></div></div>';
			});
			
			$('.skill-manage-skills-list').html(h).show();
			$('.skill-manage-skills-loading').hide();
		},
		error: function (xhr, status, error) {
			$('.skill-manage-skills-loading').hide();
			console.log(error);
		}
	});
	
}


//----skills control----


$(document).ready(function () {
	if ($(".skills-control").length > 0) {
		
		skillsControlUpdate();
		
		$('.skills-control-skills-category').on('change', function() {
			skillsControlLoadCategory($(this).val());
		});
		
	}
});


function skillsControlSelectSkill(item) {
	var selectedItemId = $(item).attr('data-id');
	selected = skillsControlGetSelectedSkills();
	selected.push(selectedItemId);
	$('#SkillsControlSelectedSkills').val(selected.join(","));
	skillsControlUpdate();
    setTimeout("$('.dropdown').find('[data-toggle=dropdown]').dropdown('toggle');", 1);
}

function skillsControlRemoveSkill(item) {
	var selected = removeFromArrayByValue(skillsControlGetSelectedSkills(), $(item).attr('data-id'));
	$('#SkillsControlSelectedSkills').val(selected.join(","));
	skillsControlUpdate();
}

function skillsControlGetSelectedSkills() {
	
	if ($('#SkillsControlSelectedSkills').val() == '') {
		return Array();
	}
	else {
		return $('#SkillsControlSelectedSkills').val().split(',');
	}
}



function skillsControlUpdate() {
	
	if (typeof skills != "undefined") {
	
		var selectedSkills = "," + $('#SkillsControlSelectedSkills').val() + ",";
		var dropdownHtml = '';
		var badgeHtml = '';
		var selectedCount = 0;
		var dropdownMessage = '';

		for (var i = 0; i < skills.length; i++){
			
			if (selectedSkills.indexOf("," + skills[i].skillId + ",") == -1) {
				dropdownHtml += '<a class="dropdown-item" href="#" data-id="' + skills[i].skillId + '" onclick="skillsControlSelectSkill(this);">' + skills[i].skillName + '</a>';
			}
			else {
				selectedCount++;
				badgeHtml += '<span class="badge badge-pill badge-success">' + skills[i].skillName + '&nbsp;&nbsp;<span class="skill-remove" data-id="' + skills[i].skillId + '" onclick="skillsControlRemoveSkill(this);"><i class="fas fa-times"></i></span></span>';
			}
			
		}
		
		$(".skills-control-dropdown-menu").html(dropdownHtml);
		$(".skills-control-selected-skills").html(badgeHtml);
		
		if (selectedCount == 1) {
			dropdownMessage = '1 skill selected';
		}
		else {
			dropdownMessage = selectedCount + ' skills selected';
		}
		
		$(".skills-control-dropdown-button").text(dropdownMessage);
	
	}
}


function skillsControlLoadCategory(id) {
	
	$('.skills-control').hide();
	$('.skills-control-spinner').show();
	
	var data = {}
	data['Mode'] = "control";
	data['SkillCategoryId'] = id;


	//console.log(data);
	
	$.ajax({
		type: 'POST',
		url: '/api/skills_control.php',
		data: JSON.stringify(data),
		contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		success: function (response) {
			$('.skills-control').html(response).show();
			$('.skills-control-spinner').hide();
			skillsControlUpdate();
		},
		error: function (xhr, status, error) {
			$('.skills-control-spinner').hide();
			console.log(error);
		}
	});
	
	
}



function removeFromArrayByValue(array, value) {
	var out = Array();
	for (var i = 0; i < array.length; i++){
		if (array[i] != value) {
			out.push(array[i]);
		}
	}
	return out;
}