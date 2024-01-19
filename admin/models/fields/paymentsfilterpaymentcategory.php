<?php
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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Paymentsfilterpaymentcategory Form Field class for the Ehealthportal component
 */
class JFormFieldPaymentsfilterpaymentcategory extends JFormFieldList
{
	/**
	 * The paymentsfilterpaymentcategory field type.
	 *
	 * @var        string
	 */
	public $type = 'paymentsfilterpaymentcategory';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return    array    An array of Html options.
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('payment_category'));
		$query->from($db->quoteName('#__ehealthportal_payment'));
		$query->order($db->quoteName('payment_category') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = [];
		$_filter[] = Html::_('select.option', '', '- ' . Text::_('COM_EHEALTHPORTAL_FILTER_SELECT_PAYMENT_CATEGORY') . ' -');

		if ($_results)
		{
			// get paymentsmodel
			$_model = EhealthportalHelper::getModel('payments');
			$_results = array_unique($_results);
			foreach ($_results as $payment_category)
			{
				// Translate the payment_category selection
				$_text = $_model->selectionTranslation($payment_category,'payment_category');
				// Now add the payment_category and its text to the options array
				$_filter[] = Html::_('select.option', $payment_category, Text::_($_text));
			}
		}
		return $_filter;
	}
}
