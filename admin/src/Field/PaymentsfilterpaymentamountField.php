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
namespace VDM\Component\Ehealthportal\Administrator\Field;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Component\ComponentHelper;
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;

/**
 * Paymentsfilterpaymentamount Form Field class for the Ehealthportal component
 */
class PaymentsfilterpaymentamountField extends ListField
{
	/**
	 * The paymentsfilterpaymentamount field type.
	 *
	 * @var        string
	 */
	public $type = 'Paymentsfilterpaymentamount';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array    An array of Html options.
	 * @since   1.6
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('payment_amount'));
		$query->from($db->quoteName('#__ehealthportal_payment'));
		$query->order($db->quoteName('payment_amount') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = [];

		if ($_results)
		{
			// get paymentsmodel
			$_model = EhealthportalHelper::getModel('payments');
			$_results = array_unique($_results);
			foreach ($_results as $payment_amount)
			{
				// Translate the payment_amount selection
				$_text = $_model->selectionTranslation($payment_amount,'payment_amount');
				// Now add the payment_amount and its text to the options array
				$_filter[] = Html::_('select.option', $payment_amount, Text::_($_text));
			}
		}
		return $_filter;
	}
}
