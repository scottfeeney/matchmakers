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