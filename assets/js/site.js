$(document).ready(function(){
	
	$('#nimihaku').click(function() {
		$.getJSON("http://tpudas.users.cs.helsinki.fi/reseptit/nimet", function(data) {
			$('#hakusana').autocomplete({
				source: data
			});
		});
	});
	
	$('#ainehaku').click(function() {
		$.getJSON("http://tpudas.users.cs.helsinki.fi/reseptit/aineet", function(data) {
			$('#hakusana').autocomplete({
				source: data
			});
		});
	});
  
	$('#tagihaku').click(function() {
		$.getJSON("http://tpudas.users.cs.helsinki.fi/reseptit/tagit", function(data) {
			$('#hakusana').autocomplete({
				source: data
			});
		});
	});
  
	// Suoritetaan nimihakukentan klikkaus, jos ollaan hakusivulla.
	if($('#nimihaku').length) {
		$('#nimihaku').click();
	}

	$('#addTagField').click(function() {
		$('#tagFields').append('<br><i>Tag</i><input type="text" class="form-control" name="tagit[]">');
	});

	$('#addAineField').click(function() {
		$('#aineFields').append('<br><i>Aine</i><input type="text" class="form-control" name="aineet[]"><i>M&auml;&auml;r&auml;</i><input type="text" class="form-control" name="maarat[]">');
	});

	// Poistamisen varmistaminen
	$('form.destroy-form').on('submit', function(submit){
		var confirm_message = $(this).attr('data-confirm');
		if(!confirm(confirm_message)){
			submit.preventDefault();
		}
	});
	
});
