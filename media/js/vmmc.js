/**
 * @package    eHealth Portal
 *
 * @created    13th August, 2020
 * @author     Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
 * @copyright  Copyright (C) 2020 Vast Development Method. All rights reserved.
 * @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Portal for mobile health clinics
 */

// Some Global Values
jform_vvvvvvxvvv_required = false;
jform_vvvvvvyvvw_required = false;
jform_vvvvvvyvvx_required = false;

// Initial Script
document.addEventListener('DOMContentLoaded', function()
{
	var vmmc_gender_vvvvvvx = jQuery("#jform_vmmc_gender").val();
	vvvvvvx(vmmc_gender_vvvvvvx);

	var vmmc_gender_vvvvvvy = jQuery("#jform_vmmc_gender").val();
	vvvvvvy(vmmc_gender_vvvvvvy);
});

// the vvvvvvx function
function vvvvvvx(vmmc_gender_vvvvvvx)
{
	if (isSet(vmmc_gender_vvvvvvx) && vmmc_gender_vvvvvvx.constructor !== Array)
	{
		var temp_vvvvvvx = vmmc_gender_vvvvvvx;
		var vmmc_gender_vvvvvvx = [];
		vmmc_gender_vvvvvvx.push(temp_vvvvvvx);
	}
	else if (!isSet(vmmc_gender_vvvvvvx))
	{
		var vmmc_gender_vvvvvvx = [];
	}
	var vmmc_gender = vmmc_gender_vvvvvvx.some(vmmc_gender_vvvvvvx_SomeFunc);


	// set this function logic
	if (vmmc_gender)
	{
		jQuery('#jform_partner_circumcised').closest('.control-group').show();
		// add required attribute to partner_circumcised field
		if (jform_vvvvvvxvvv_required)
		{
			updateFieldRequired('partner_circumcised',0);
			jQuery('#jform_partner_circumcised').prop('required','required');
			jQuery('#jform_partner_circumcised').attr('aria-required',true);
			jQuery('#jform_partner_circumcised').addClass('required');
			jform_vvvvvvxvvv_required = false;
		}
	}
	else
	{
		jQuery('#jform_partner_circumcised').closest('.control-group').hide();
		// remove required attribute from partner_circumcised field
		if (!jform_vvvvvvxvvv_required)
		{
			updateFieldRequired('partner_circumcised',1);
			jQuery('#jform_partner_circumcised').removeAttr('required');
			jQuery('#jform_partner_circumcised').removeAttr('aria-required');
			jQuery('#jform_partner_circumcised').removeClass('required');
			jform_vvvvvvxvvv_required = true;
		}
	}
}

// the vvvvvvx Some function
function vmmc_gender_vvvvvvx_SomeFunc(vmmc_gender_vvvvvvx)
{
	// set the function logic
	if (vmmc_gender_vvvvvvx == 1)
	{
		return true;
	}
	return false;
}

// the vvvvvvy function
function vvvvvvy(vmmc_gender_vvvvvvy)
{
	if (isSet(vmmc_gender_vvvvvvy) && vmmc_gender_vvvvvvy.constructor !== Array)
	{
		var temp_vvvvvvy = vmmc_gender_vvvvvvy;
		var vmmc_gender_vvvvvvy = [];
		vmmc_gender_vvvvvvy.push(temp_vvvvvvy);
	}
	else if (!isSet(vmmc_gender_vvvvvvy))
	{
		var vmmc_gender_vvvvvvy = [];
	}
	var vmmc_gender = vmmc_gender_vvvvvvy.some(vmmc_gender_vvvvvvy_SomeFunc);


	// set this function logic
	if (vmmc_gender)
	{
		jQuery('#jform_are_you_circumcised').closest('.control-group').show();
		// add required attribute to are_you_circumcised field
		if (jform_vvvvvvyvvw_required)
		{
			updateFieldRequired('are_you_circumcised',0);
			jQuery('#jform_are_you_circumcised').prop('required','required');
			jQuery('#jform_are_you_circumcised').attr('aria-required',true);
			jQuery('#jform_are_you_circumcised').addClass('required');
			jform_vvvvvvyvvw_required = false;
		}
		jQuery('#jform_interested_in_vmmc').closest('.control-group').show();
		// add required attribute to interested_in_vmmc field
		if (jform_vvvvvvyvvx_required)
		{
			updateFieldRequired('interested_in_vmmc',0);
			jQuery('#jform_interested_in_vmmc').prop('required','required');
			jQuery('#jform_interested_in_vmmc').attr('aria-required',true);
			jQuery('#jform_interested_in_vmmc').addClass('required');
			jform_vvvvvvyvvx_required = false;
		}
	}
	else
	{
		jQuery('#jform_are_you_circumcised').closest('.control-group').hide();
		// remove required attribute from are_you_circumcised field
		if (!jform_vvvvvvyvvw_required)
		{
			updateFieldRequired('are_you_circumcised',1);
			jQuery('#jform_are_you_circumcised').removeAttr('required');
			jQuery('#jform_are_you_circumcised').removeAttr('aria-required');
			jQuery('#jform_are_you_circumcised').removeClass('required');
			jform_vvvvvvyvvw_required = true;
		}
		jQuery('#jform_interested_in_vmmc').closest('.control-group').hide();
		// remove required attribute from interested_in_vmmc field
		if (!jform_vvvvvvyvvx_required)
		{
			updateFieldRequired('interested_in_vmmc',1);
			jQuery('#jform_interested_in_vmmc').removeAttr('required');
			jQuery('#jform_interested_in_vmmc').removeAttr('aria-required');
			jQuery('#jform_interested_in_vmmc').removeClass('required');
			jform_vvvvvvyvvx_required = true;
		}
	}
}

// the vvvvvvy Some function
function vmmc_gender_vvvvvvy_SomeFunc(vmmc_gender_vvvvvvy)
{
	// set the function logic
	if (vmmc_gender_vvvvvvy == 0)
	{
		return true;
	}
	return false;
}

// update fields required
function updateFieldRequired(name, status) {
	// check if not_required exist
	if (document.getElementById('jform_not_required')) {
		var not_required = jQuery('#jform_not_required').val().split(",");

		if(status == 1)
		{
			not_required.push(name);
		}
		else
		{
			not_required = removeFieldFromNotRequired(not_required, name);
		}

		jQuery('#jform_not_required').val(fixNotRequiredArray(not_required).toString());
	}
}

// remove field from not_required
function removeFieldFromNotRequired(array, what) {
	return array.filter(function(element){
		return element !== what;
	});
}

// fix not required array
function fixNotRequiredArray(array) {
	var seen = {};
	return removeEmptyFromNotRequiredArray(array).filter(function(item) {
		return seen.hasOwnProperty(item) ? false : (seen[item] = true);
	});
}

// remove empty from not_required array
function removeEmptyFromNotRequiredArray(array) {
	return array.filter(function (el) {
		// remove ( 一_一) as well - lol
		return (el.length > 0 && '一_一' !== el);
	});
}

// the isSet function
function isSet(val)
{
	if ((val != undefined) && (val != null) && 0 !== val.length){
		return true;
	}
	return false;
}
