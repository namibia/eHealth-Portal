/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |
                                                        |_|
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		4.0.x
	@build			19th January, 2024
	@created		13th August, 2020
	@package		eHealth Portal
	@subpackage		immunisation.js
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/




// set immunisation vaccine types that are on the page
var select_an_immunisation_vaccine_type = 'Select...';
var create_an_immunisation_vaccine_type = 'Create...';
immunisation_vaccine_types = {};
var immunisation_vaccine_type = 0;
jQuery(document).ready(function($)
{
	// when it changes
	$('#adminForm').on('change', '#jform_administration_part',function (e) {
		e.preventDefault();
		getImmunisationVaccineType();
	});
	// set buckets
	jQuery("#jform_immunisation_vaccine_type option").each(function()
	{
		var key =  jQuery(this).val();
		var text =  jQuery(this).text();
		immunisation_vaccine_types[key] = text;
	});
	immunisation_vaccine_type = jQuery('#jform_immunisation_vaccine_type').val();
	// run now
	getImmunisationVaccineType();
});

function getImmunisationVaccineTypeServer(administration_part){
	var getUrl = JRouter("index.php?option=com_ehealthportal&task=ajax.getImmunisationVaccineType&raw=true&format=json");
	if(token.length > 0 && administration_part > 0){
		var request = 'token='+token+'&administration_part='+administration_part;
	}
	return jQuery.ajax({
		type: 'GET',
		url: getUrl,
		dataType: 'json',
		data: request,
		jsonp: false
	});
}
function getImmunisationVaccineType(){
	jQuery("#loading").show();
	// clear the selection
	jQuery('#jform_immunisation_vaccine_type').find('option').remove().end();
	jQuery('#jform_immunisation_vaccine_type').trigger('liszt:updated');
	// get administration part value if set
	var administration_part = jQuery('#jform_administration_part').val();
	getImmunisationVaccineTypeServer(administration_part).done(function(result) {
		setImmunisationVaccineType(result);
		jQuery("#loading").hide();
		if (typeof ImmunisationVaccineTypeButton !== 'undefined') {
			// ensure button is correct
			var immunisation_vaccine_type = jQuery('#jform_immunisation_vaccine_type').val();
			ImmunisationVaccineTypeButton(immunisation_vaccine_type);
		}
	});
}
function setImmunisationVaccineType(array){
	if (array) {
		jQuery('#jform_immunisation_vaccine_type').append('<option value="">'+select_an_immunisation_vaccine_type+'</option>');
		jQuery.each( array, function( i, id ) {
			if (id in immunisation_vaccine_types) {
				jQuery('#jform_immunisation_vaccine_type').append('<option value="'+id+'">'+immunisation_vaccine_types[id]+'</option>');
			}
			if (id == immunisation_vaccine_type) {
				jQuery('#jform_immunisation_vaccine_type').val(id);
			}
		});
	} else {
		jQuery('#jform_immunisation_vaccine_type').append('<option value="">'+create_an_immunisation_vaccine_type+'</option>');
	}
	jQuery('#jform_immunisation_vaccine_type').trigger('liszt:updated');
}
