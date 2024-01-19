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

// Initial Script
document.addEventListener('DOMContentLoaded', function()
{
	var payment_category_vvvvvvv = jQuery("#jform_payment_category").val();
	vvvvvvv(payment_category_vvvvvvv);

	var payment_amount_vvvvvvw = jQuery("#jform_payment_amount").val();
	vvvvvvw(payment_amount_vvvvvvw);
});

// the vvvvvvv function
function vvvvvvv(payment_category_vvvvvvv)
{
	if (isSet(payment_category_vvvvvvv) && payment_category_vvvvvvv.constructor !== Array)
	{
		var temp_vvvvvvv = payment_category_vvvvvvv;
		var payment_category_vvvvvvv = [];
		payment_category_vvvvvvv.push(temp_vvvvvvv);
	}
	else if (!isSet(payment_category_vvvvvvv))
	{
		var payment_category_vvvvvvv = [];
	}
	var payment_category = payment_category_vvvvvvv.some(payment_category_vvvvvvv_SomeFunc);


	// set this function logic
	if (payment_category)
	{
		jQuery('#jform_nonpay_reason').closest('.control-group').show();
	}
	else
	{
		jQuery('#jform_nonpay_reason').closest('.control-group').hide();
	}
}

// the vvvvvvv Some function
function payment_category_vvvvvvv_SomeFunc(payment_category_vvvvvvv)
{
	// set the function logic
	if (payment_category_vvvvvvv == 1)
	{
		return true;
	}
	return false;
}

// the vvvvvvw function
function vvvvvvw(payment_amount_vvvvvvw)
{
	if (isSet(payment_amount_vvvvvvw) && payment_amount_vvvvvvw.constructor !== Array)
	{
		var temp_vvvvvvw = payment_amount_vvvvvvw;
		var payment_amount_vvvvvvw = [];
		payment_amount_vvvvvvw.push(temp_vvvvvvw);
	}
	else if (!isSet(payment_amount_vvvvvvw))
	{
		var payment_amount_vvvvvvw = [];
	}
	var payment_amount = payment_amount_vvvvvvw.some(payment_amount_vvvvvvw_SomeFunc);


	// set this function logic
	if (payment_amount)
	{
		jQuery('#jform_nonpay_reason').closest('.control-group').show();
	}
	else
	{
		jQuery('#jform_nonpay_reason').closest('.control-group').hide();
	}
}

// the vvvvvvw Some function
function payment_amount_vvvvvvw_SomeFunc(payment_amount_vvvvvvw)
{
	// set the function logic
	if (payment_amount_vvvvvvw == 1)
	{
		return true;
	}
	return false;
}

// the isSet function
function isSet(val)
{
	if ((val != undefined) && (val != null) && 0 !== val.length){
		return true;
	}
	return false;
}
