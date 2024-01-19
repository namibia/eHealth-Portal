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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper as Html;
HTML::_('bootstrap.renderModal');

/**
 * Script File of Ehealthportal Component
 */
class Com_EhealthportalInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 */
	public function __construct(ComponentAdapter $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(ComponentAdapter $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 */
	public function uninstall(ComponentAdapter $parent)
	{
		// Get Application object
		$app = Factory::getApplication();

		// Get The Database object
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Payment alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$payment_found = $db->getNumRows();
		// Now check if there were any rows
		if ($payment_found)
		{
			// Since there are load the needed  payment type ids
			$payment_ids = $db->loadColumn();
			// Remove Payment from the content type table
			$payment_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully remove Payment add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.payment) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Payment items from the contentitem tag map table
			$payment_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully remove Payment add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.payment) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Payment items from the ucm content table
			$payment_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully removed Payment add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.payment) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Payment items are cleared from DB
			foreach ($payment_ids as $payment_id)
			{
				// Remove Payment items from the ucm base table
				$payment_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($payment_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment items
				$db->execute();

				// Remove Payment items from the ucm history table
				$payment_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($payment_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where General_medical_check_up alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.general_medical_check_up') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$general_medical_check_up_found = $db->getNumRows();
		// Now check if there were any rows
		if ($general_medical_check_up_found)
		{
			// Since there are load the needed  general_medical_check_up type ids
			$general_medical_check_up_ids = $db->loadColumn();
			// Remove General_medical_check_up from the content type table
			$general_medical_check_up_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.general_medical_check_up') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($general_medical_check_up_condition);
			$db->setQuery($query);
			// Execute the query to remove General_medical_check_up items
			$general_medical_check_up_done = $db->execute();
			if ($general_medical_check_up_done)
			{
				// If successfully remove General_medical_check_up add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.general_medical_check_up) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove General_medical_check_up items from the contentitem tag map table
			$general_medical_check_up_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.general_medical_check_up') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($general_medical_check_up_condition);
			$db->setQuery($query);
			// Execute the query to remove General_medical_check_up items
			$general_medical_check_up_done = $db->execute();
			if ($general_medical_check_up_done)
			{
				// If successfully remove General_medical_check_up add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.general_medical_check_up) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove General_medical_check_up items from the ucm content table
			$general_medical_check_up_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.general_medical_check_up') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($general_medical_check_up_condition);
			$db->setQuery($query);
			// Execute the query to remove General_medical_check_up items
			$general_medical_check_up_done = $db->execute();
			if ($general_medical_check_up_done)
			{
				// If successfully removed General_medical_check_up add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.general_medical_check_up) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the General_medical_check_up items are cleared from DB
			foreach ($general_medical_check_up_ids as $general_medical_check_up_id)
			{
				// Remove General_medical_check_up items from the ucm base table
				$general_medical_check_up_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $general_medical_check_up_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($general_medical_check_up_condition);
				$db->setQuery($query);
				// Execute the query to remove General_medical_check_up items
				$db->execute();

				// Remove General_medical_check_up items from the ucm history table
				$general_medical_check_up_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $general_medical_check_up_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($general_medical_check_up_condition);
				$db->setQuery($query);
				// Execute the query to remove General_medical_check_up items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Antenatal_care alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.antenatal_care') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$antenatal_care_found = $db->getNumRows();
		// Now check if there were any rows
		if ($antenatal_care_found)
		{
			// Since there are load the needed  antenatal_care type ids
			$antenatal_care_ids = $db->loadColumn();
			// Remove Antenatal_care from the content type table
			$antenatal_care_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.antenatal_care') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($antenatal_care_condition);
			$db->setQuery($query);
			// Execute the query to remove Antenatal_care items
			$antenatal_care_done = $db->execute();
			if ($antenatal_care_done)
			{
				// If successfully remove Antenatal_care add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.antenatal_care) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Antenatal_care items from the contentitem tag map table
			$antenatal_care_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.antenatal_care') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($antenatal_care_condition);
			$db->setQuery($query);
			// Execute the query to remove Antenatal_care items
			$antenatal_care_done = $db->execute();
			if ($antenatal_care_done)
			{
				// If successfully remove Antenatal_care add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.antenatal_care) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Antenatal_care items from the ucm content table
			$antenatal_care_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.antenatal_care') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($antenatal_care_condition);
			$db->setQuery($query);
			// Execute the query to remove Antenatal_care items
			$antenatal_care_done = $db->execute();
			if ($antenatal_care_done)
			{
				// If successfully removed Antenatal_care add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.antenatal_care) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Antenatal_care items are cleared from DB
			foreach ($antenatal_care_ids as $antenatal_care_id)
			{
				// Remove Antenatal_care items from the ucm base table
				$antenatal_care_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $antenatal_care_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($antenatal_care_condition);
				$db->setQuery($query);
				// Execute the query to remove Antenatal_care items
				$db->execute();

				// Remove Antenatal_care items from the ucm history table
				$antenatal_care_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $antenatal_care_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($antenatal_care_condition);
				$db->setQuery($query);
				// Execute the query to remove Antenatal_care items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Immunisation alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$immunisation_found = $db->getNumRows();
		// Now check if there were any rows
		if ($immunisation_found)
		{
			// Since there are load the needed  immunisation type ids
			$immunisation_ids = $db->loadColumn();
			// Remove Immunisation from the content type table
			$immunisation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($immunisation_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation items
			$immunisation_done = $db->execute();
			if ($immunisation_done)
			{
				// If successfully remove Immunisation add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Immunisation items from the contentitem tag map table
			$immunisation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($immunisation_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation items
			$immunisation_done = $db->execute();
			if ($immunisation_done)
			{
				// If successfully remove Immunisation add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Immunisation items from the ucm content table
			$immunisation_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.immunisation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($immunisation_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation items
			$immunisation_done = $db->execute();
			if ($immunisation_done)
			{
				// If successfully removed Immunisation add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Immunisation items are cleared from DB
			foreach ($immunisation_ids as $immunisation_id)
			{
				// Remove Immunisation items from the ucm base table
				$immunisation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($immunisation_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation items
				$db->execute();

				// Remove Immunisation items from the ucm history table
				$immunisation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($immunisation_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Vmmc alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.vmmc') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$vmmc_found = $db->getNumRows();
		// Now check if there were any rows
		if ($vmmc_found)
		{
			// Since there are load the needed  vmmc type ids
			$vmmc_ids = $db->loadColumn();
			// Remove Vmmc from the content type table
			$vmmc_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.vmmc') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($vmmc_condition);
			$db->setQuery($query);
			// Execute the query to remove Vmmc items
			$vmmc_done = $db->execute();
			if ($vmmc_done)
			{
				// If successfully remove Vmmc add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.vmmc) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Vmmc items from the contentitem tag map table
			$vmmc_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.vmmc') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($vmmc_condition);
			$db->setQuery($query);
			// Execute the query to remove Vmmc items
			$vmmc_done = $db->execute();
			if ($vmmc_done)
			{
				// If successfully remove Vmmc add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.vmmc) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Vmmc items from the ucm content table
			$vmmc_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.vmmc') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($vmmc_condition);
			$db->setQuery($query);
			// Execute the query to remove Vmmc items
			$vmmc_done = $db->execute();
			if ($vmmc_done)
			{
				// If successfully removed Vmmc add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.vmmc) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Vmmc items are cleared from DB
			foreach ($vmmc_ids as $vmmc_id)
			{
				// Remove Vmmc items from the ucm base table
				$vmmc_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $vmmc_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($vmmc_condition);
				$db->setQuery($query);
				// Execute the query to remove Vmmc items
				$db->execute();

				// Remove Vmmc items from the ucm history table
				$vmmc_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $vmmc_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($vmmc_condition);
				$db->setQuery($query);
				// Execute the query to remove Vmmc items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Prostate_and_testicular_cancer alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.prostate_and_testicular_cancer') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$prostate_and_testicular_cancer_found = $db->getNumRows();
		// Now check if there were any rows
		if ($prostate_and_testicular_cancer_found)
		{
			// Since there are load the needed  prostate_and_testicular_cancer type ids
			$prostate_and_testicular_cancer_ids = $db->loadColumn();
			// Remove Prostate_and_testicular_cancer from the content type table
			$prostate_and_testicular_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.prostate_and_testicular_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($prostate_and_testicular_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Prostate_and_testicular_cancer items
			$prostate_and_testicular_cancer_done = $db->execute();
			if ($prostate_and_testicular_cancer_done)
			{
				// If successfully remove Prostate_and_testicular_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.prostate_and_testicular_cancer) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Prostate_and_testicular_cancer items from the contentitem tag map table
			$prostate_and_testicular_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.prostate_and_testicular_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($prostate_and_testicular_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Prostate_and_testicular_cancer items
			$prostate_and_testicular_cancer_done = $db->execute();
			if ($prostate_and_testicular_cancer_done)
			{
				// If successfully remove Prostate_and_testicular_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.prostate_and_testicular_cancer) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Prostate_and_testicular_cancer items from the ucm content table
			$prostate_and_testicular_cancer_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.prostate_and_testicular_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($prostate_and_testicular_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Prostate_and_testicular_cancer items
			$prostate_and_testicular_cancer_done = $db->execute();
			if ($prostate_and_testicular_cancer_done)
			{
				// If successfully removed Prostate_and_testicular_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.prostate_and_testicular_cancer) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Prostate_and_testicular_cancer items are cleared from DB
			foreach ($prostate_and_testicular_cancer_ids as $prostate_and_testicular_cancer_id)
			{
				// Remove Prostate_and_testicular_cancer items from the ucm base table
				$prostate_and_testicular_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $prostate_and_testicular_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($prostate_and_testicular_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Prostate_and_testicular_cancer items
				$db->execute();

				// Remove Prostate_and_testicular_cancer items from the ucm history table
				$prostate_and_testicular_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $prostate_and_testicular_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($prostate_and_testicular_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Prostate_and_testicular_cancer items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Tuberculosis alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.tuberculosis') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$tuberculosis_found = $db->getNumRows();
		// Now check if there were any rows
		if ($tuberculosis_found)
		{
			// Since there are load the needed  tuberculosis type ids
			$tuberculosis_ids = $db->loadColumn();
			// Remove Tuberculosis from the content type table
			$tuberculosis_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.tuberculosis') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($tuberculosis_condition);
			$db->setQuery($query);
			// Execute the query to remove Tuberculosis items
			$tuberculosis_done = $db->execute();
			if ($tuberculosis_done)
			{
				// If successfully remove Tuberculosis add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.tuberculosis) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Tuberculosis items from the contentitem tag map table
			$tuberculosis_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.tuberculosis') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($tuberculosis_condition);
			$db->setQuery($query);
			// Execute the query to remove Tuberculosis items
			$tuberculosis_done = $db->execute();
			if ($tuberculosis_done)
			{
				// If successfully remove Tuberculosis add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.tuberculosis) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Tuberculosis items from the ucm content table
			$tuberculosis_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.tuberculosis') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($tuberculosis_condition);
			$db->setQuery($query);
			// Execute the query to remove Tuberculosis items
			$tuberculosis_done = $db->execute();
			if ($tuberculosis_done)
			{
				// If successfully removed Tuberculosis add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.tuberculosis) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Tuberculosis items are cleared from DB
			foreach ($tuberculosis_ids as $tuberculosis_id)
			{
				// Remove Tuberculosis items from the ucm base table
				$tuberculosis_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tuberculosis_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($tuberculosis_condition);
				$db->setQuery($query);
				// Execute the query to remove Tuberculosis items
				$db->execute();

				// Remove Tuberculosis items from the ucm history table
				$tuberculosis_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tuberculosis_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($tuberculosis_condition);
				$db->setQuery($query);
				// Execute the query to remove Tuberculosis items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Hiv_counseling_and_testing alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.hiv_counseling_and_testing') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$hiv_counseling_and_testing_found = $db->getNumRows();
		// Now check if there were any rows
		if ($hiv_counseling_and_testing_found)
		{
			// Since there are load the needed  hiv_counseling_and_testing type ids
			$hiv_counseling_and_testing_ids = $db->loadColumn();
			// Remove Hiv_counseling_and_testing from the content type table
			$hiv_counseling_and_testing_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.hiv_counseling_and_testing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($hiv_counseling_and_testing_condition);
			$db->setQuery($query);
			// Execute the query to remove Hiv_counseling_and_testing items
			$hiv_counseling_and_testing_done = $db->execute();
			if ($hiv_counseling_and_testing_done)
			{
				// If successfully remove Hiv_counseling_and_testing add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.hiv_counseling_and_testing) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Hiv_counseling_and_testing items from the contentitem tag map table
			$hiv_counseling_and_testing_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.hiv_counseling_and_testing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($hiv_counseling_and_testing_condition);
			$db->setQuery($query);
			// Execute the query to remove Hiv_counseling_and_testing items
			$hiv_counseling_and_testing_done = $db->execute();
			if ($hiv_counseling_and_testing_done)
			{
				// If successfully remove Hiv_counseling_and_testing add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.hiv_counseling_and_testing) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Hiv_counseling_and_testing items from the ucm content table
			$hiv_counseling_and_testing_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.hiv_counseling_and_testing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($hiv_counseling_and_testing_condition);
			$db->setQuery($query);
			// Execute the query to remove Hiv_counseling_and_testing items
			$hiv_counseling_and_testing_done = $db->execute();
			if ($hiv_counseling_and_testing_done)
			{
				// If successfully removed Hiv_counseling_and_testing add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.hiv_counseling_and_testing) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Hiv_counseling_and_testing items are cleared from DB
			foreach ($hiv_counseling_and_testing_ids as $hiv_counseling_and_testing_id)
			{
				// Remove Hiv_counseling_and_testing items from the ucm base table
				$hiv_counseling_and_testing_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $hiv_counseling_and_testing_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($hiv_counseling_and_testing_condition);
				$db->setQuery($query);
				// Execute the query to remove Hiv_counseling_and_testing items
				$db->execute();

				// Remove Hiv_counseling_and_testing items from the ucm history table
				$hiv_counseling_and_testing_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $hiv_counseling_and_testing_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($hiv_counseling_and_testing_condition);
				$db->setQuery($query);
				// Execute the query to remove Hiv_counseling_and_testing items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Family_planning alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.family_planning') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$family_planning_found = $db->getNumRows();
		// Now check if there were any rows
		if ($family_planning_found)
		{
			// Since there are load the needed  family_planning type ids
			$family_planning_ids = $db->loadColumn();
			// Remove Family_planning from the content type table
			$family_planning_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.family_planning') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($family_planning_condition);
			$db->setQuery($query);
			// Execute the query to remove Family_planning items
			$family_planning_done = $db->execute();
			if ($family_planning_done)
			{
				// If successfully remove Family_planning add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.family_planning) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Family_planning items from the contentitem tag map table
			$family_planning_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.family_planning') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($family_planning_condition);
			$db->setQuery($query);
			// Execute the query to remove Family_planning items
			$family_planning_done = $db->execute();
			if ($family_planning_done)
			{
				// If successfully remove Family_planning add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.family_planning) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Family_planning items from the ucm content table
			$family_planning_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.family_planning') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($family_planning_condition);
			$db->setQuery($query);
			// Execute the query to remove Family_planning items
			$family_planning_done = $db->execute();
			if ($family_planning_done)
			{
				// If successfully removed Family_planning add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.family_planning) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Family_planning items are cleared from DB
			foreach ($family_planning_ids as $family_planning_id)
			{
				// Remove Family_planning items from the ucm base table
				$family_planning_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $family_planning_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($family_planning_condition);
				$db->setQuery($query);
				// Execute the query to remove Family_planning items
				$db->execute();

				// Remove Family_planning items from the ucm history table
				$family_planning_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $family_planning_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($family_planning_condition);
				$db->setQuery($query);
				// Execute the query to remove Family_planning items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Health_education alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$health_education_found = $db->getNumRows();
		// Now check if there were any rows
		if ($health_education_found)
		{
			// Since there are load the needed  health_education type ids
			$health_education_ids = $db->loadColumn();
			// Remove Health_education from the content type table
			$health_education_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($health_education_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education items
			$health_education_done = $db->execute();
			if ($health_education_done)
			{
				// If successfully remove Health_education add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.health_education) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Health_education items from the contentitem tag map table
			$health_education_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($health_education_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education items
			$health_education_done = $db->execute();
			if ($health_education_done)
			{
				// If successfully remove Health_education add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.health_education) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Health_education items from the ucm content table
			$health_education_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.health_education') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($health_education_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education items
			$health_education_done = $db->execute();
			if ($health_education_done)
			{
				// If successfully removed Health_education add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.health_education) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Health_education items are cleared from DB
			foreach ($health_education_ids as $health_education_id)
			{
				// Remove Health_education items from the ucm base table
				$health_education_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($health_education_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education items
				$db->execute();

				// Remove Health_education items from the ucm history table
				$health_education_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($health_education_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Cervical_cancer alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.cervical_cancer') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$cervical_cancer_found = $db->getNumRows();
		// Now check if there were any rows
		if ($cervical_cancer_found)
		{
			// Since there are load the needed  cervical_cancer type ids
			$cervical_cancer_ids = $db->loadColumn();
			// Remove Cervical_cancer from the content type table
			$cervical_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.cervical_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($cervical_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Cervical_cancer items
			$cervical_cancer_done = $db->execute();
			if ($cervical_cancer_done)
			{
				// If successfully remove Cervical_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.cervical_cancer) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Cervical_cancer items from the contentitem tag map table
			$cervical_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.cervical_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($cervical_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Cervical_cancer items
			$cervical_cancer_done = $db->execute();
			if ($cervical_cancer_done)
			{
				// If successfully remove Cervical_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.cervical_cancer) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Cervical_cancer items from the ucm content table
			$cervical_cancer_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.cervical_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($cervical_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Cervical_cancer items
			$cervical_cancer_done = $db->execute();
			if ($cervical_cancer_done)
			{
				// If successfully removed Cervical_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.cervical_cancer) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Cervical_cancer items are cleared from DB
			foreach ($cervical_cancer_ids as $cervical_cancer_id)
			{
				// Remove Cervical_cancer items from the ucm base table
				$cervical_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $cervical_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($cervical_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Cervical_cancer items
				$db->execute();

				// Remove Cervical_cancer items from the ucm history table
				$cervical_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $cervical_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($cervical_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Cervical_cancer items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Breast_cancer alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.breast_cancer') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$breast_cancer_found = $db->getNumRows();
		// Now check if there were any rows
		if ($breast_cancer_found)
		{
			// Since there are load the needed  breast_cancer type ids
			$breast_cancer_ids = $db->loadColumn();
			// Remove Breast_cancer from the content type table
			$breast_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.breast_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($breast_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Breast_cancer items
			$breast_cancer_done = $db->execute();
			if ($breast_cancer_done)
			{
				// If successfully remove Breast_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.breast_cancer) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Breast_cancer items from the contentitem tag map table
			$breast_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.breast_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($breast_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Breast_cancer items
			$breast_cancer_done = $db->execute();
			if ($breast_cancer_done)
			{
				// If successfully remove Breast_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.breast_cancer) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Breast_cancer items from the ucm content table
			$breast_cancer_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.breast_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($breast_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Breast_cancer items
			$breast_cancer_done = $db->execute();
			if ($breast_cancer_done)
			{
				// If successfully removed Breast_cancer add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.breast_cancer) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Breast_cancer items are cleared from DB
			foreach ($breast_cancer_ids as $breast_cancer_id)
			{
				// Remove Breast_cancer items from the ucm base table
				$breast_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $breast_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($breast_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Breast_cancer items
				$db->execute();

				// Remove Breast_cancer items from the ucm history table
				$breast_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $breast_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($breast_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Breast_cancer items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Test alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.test') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$test_found = $db->getNumRows();
		// Now check if there were any rows
		if ($test_found)
		{
			// Since there are load the needed  test type ids
			$test_ids = $db->loadColumn();
			// Remove Test from the content type table
			$test_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.test') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($test_condition);
			$db->setQuery($query);
			// Execute the query to remove Test items
			$test_done = $db->execute();
			if ($test_done)
			{
				// If successfully remove Test add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.test) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Test items from the contentitem tag map table
			$test_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.test') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($test_condition);
			$db->setQuery($query);
			// Execute the query to remove Test items
			$test_done = $db->execute();
			if ($test_done)
			{
				// If successfully remove Test add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.test) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Test items from the ucm content table
			$test_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.test') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($test_condition);
			$db->setQuery($query);
			// Execute the query to remove Test items
			$test_done = $db->execute();
			if ($test_done)
			{
				// If successfully removed Test add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.test) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Test items are cleared from DB
			foreach ($test_ids as $test_id)
			{
				// Remove Test items from the ucm base table
				$test_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $test_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($test_condition);
				$db->setQuery($query);
				// Execute the query to remove Test items
				$db->execute();

				// Remove Test items from the ucm history table
				$test_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $test_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($test_condition);
				$db->setQuery($query);
				// Execute the query to remove Test items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Foetal_lie alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_lie') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$foetal_lie_found = $db->getNumRows();
		// Now check if there were any rows
		if ($foetal_lie_found)
		{
			// Since there are load the needed  foetal_lie type ids
			$foetal_lie_ids = $db->loadColumn();
			// Remove Foetal_lie from the content type table
			$foetal_lie_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_lie') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($foetal_lie_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_lie items
			$foetal_lie_done = $db->execute();
			if ($foetal_lie_done)
			{
				// If successfully remove Foetal_lie add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_lie) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Foetal_lie items from the contentitem tag map table
			$foetal_lie_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_lie') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($foetal_lie_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_lie items
			$foetal_lie_done = $db->execute();
			if ($foetal_lie_done)
			{
				// If successfully remove Foetal_lie add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_lie) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Foetal_lie items from the ucm content table
			$foetal_lie_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.foetal_lie') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($foetal_lie_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_lie items
			$foetal_lie_done = $db->execute();
			if ($foetal_lie_done)
			{
				// If successfully removed Foetal_lie add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_lie) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Foetal_lie items are cleared from DB
			foreach ($foetal_lie_ids as $foetal_lie_id)
			{
				// Remove Foetal_lie items from the ucm base table
				$foetal_lie_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_lie_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($foetal_lie_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_lie items
				$db->execute();

				// Remove Foetal_lie items from the ucm history table
				$foetal_lie_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_lie_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($foetal_lie_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_lie items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Immunisation_vaccine_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_vaccine_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$immunisation_vaccine_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($immunisation_vaccine_type_found)
		{
			// Since there are load the needed  immunisation_vaccine_type type ids
			$immunisation_vaccine_type_ids = $db->loadColumn();
			// Remove Immunisation_vaccine_type from the content type table
			$immunisation_vaccine_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_vaccine_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($immunisation_vaccine_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_vaccine_type items
			$immunisation_vaccine_type_done = $db->execute();
			if ($immunisation_vaccine_type_done)
			{
				// If successfully remove Immunisation_vaccine_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation_vaccine_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Immunisation_vaccine_type items from the contentitem tag map table
			$immunisation_vaccine_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_vaccine_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($immunisation_vaccine_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_vaccine_type items
			$immunisation_vaccine_type_done = $db->execute();
			if ($immunisation_vaccine_type_done)
			{
				// If successfully remove Immunisation_vaccine_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation_vaccine_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Immunisation_vaccine_type items from the ucm content table
			$immunisation_vaccine_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.immunisation_vaccine_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($immunisation_vaccine_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_vaccine_type items
			$immunisation_vaccine_type_done = $db->execute();
			if ($immunisation_vaccine_type_done)
			{
				// If successfully removed Immunisation_vaccine_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation_vaccine_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Immunisation_vaccine_type items are cleared from DB
			foreach ($immunisation_vaccine_type_ids as $immunisation_vaccine_type_id)
			{
				// Remove Immunisation_vaccine_type items from the ucm base table
				$immunisation_vaccine_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_vaccine_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($immunisation_vaccine_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_vaccine_type items
				$db->execute();

				// Remove Immunisation_vaccine_type items from the ucm history table
				$immunisation_vaccine_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_vaccine_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($immunisation_vaccine_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_vaccine_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Foetal_engagement alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_engagement') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$foetal_engagement_found = $db->getNumRows();
		// Now check if there were any rows
		if ($foetal_engagement_found)
		{
			// Since there are load the needed  foetal_engagement type ids
			$foetal_engagement_ids = $db->loadColumn();
			// Remove Foetal_engagement from the content type table
			$foetal_engagement_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_engagement') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($foetal_engagement_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_engagement items
			$foetal_engagement_done = $db->execute();
			if ($foetal_engagement_done)
			{
				// If successfully remove Foetal_engagement add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_engagement) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Foetal_engagement items from the contentitem tag map table
			$foetal_engagement_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_engagement') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($foetal_engagement_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_engagement items
			$foetal_engagement_done = $db->execute();
			if ($foetal_engagement_done)
			{
				// If successfully remove Foetal_engagement add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_engagement) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Foetal_engagement items from the ucm content table
			$foetal_engagement_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.foetal_engagement') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($foetal_engagement_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_engagement items
			$foetal_engagement_done = $db->execute();
			if ($foetal_engagement_done)
			{
				// If successfully removed Foetal_engagement add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_engagement) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Foetal_engagement items are cleared from DB
			foreach ($foetal_engagement_ids as $foetal_engagement_id)
			{
				// Remove Foetal_engagement items from the ucm base table
				$foetal_engagement_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_engagement_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($foetal_engagement_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_engagement items
				$db->execute();

				// Remove Foetal_engagement items from the ucm history table
				$foetal_engagement_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_engagement_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($foetal_engagement_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_engagement items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Foetal_presentation alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_presentation') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$foetal_presentation_found = $db->getNumRows();
		// Now check if there were any rows
		if ($foetal_presentation_found)
		{
			// Since there are load the needed  foetal_presentation type ids
			$foetal_presentation_ids = $db->loadColumn();
			// Remove Foetal_presentation from the content type table
			$foetal_presentation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_presentation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($foetal_presentation_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_presentation items
			$foetal_presentation_done = $db->execute();
			if ($foetal_presentation_done)
			{
				// If successfully remove Foetal_presentation add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_presentation) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Foetal_presentation items from the contentitem tag map table
			$foetal_presentation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_presentation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($foetal_presentation_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_presentation items
			$foetal_presentation_done = $db->execute();
			if ($foetal_presentation_done)
			{
				// If successfully remove Foetal_presentation add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_presentation) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Foetal_presentation items from the ucm content table
			$foetal_presentation_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.foetal_presentation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($foetal_presentation_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_presentation items
			$foetal_presentation_done = $db->execute();
			if ($foetal_presentation_done)
			{
				// If successfully removed Foetal_presentation add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.foetal_presentation) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Foetal_presentation items are cleared from DB
			foreach ($foetal_presentation_ids as $foetal_presentation_id)
			{
				// Remove Foetal_presentation items from the ucm base table
				$foetal_presentation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_presentation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($foetal_presentation_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_presentation items
				$db->execute();

				// Remove Foetal_presentation items from the ucm history table
				$foetal_presentation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_presentation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($foetal_presentation_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_presentation items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Testing_reason alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.testing_reason') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$testing_reason_found = $db->getNumRows();
		// Now check if there were any rows
		if ($testing_reason_found)
		{
			// Since there are load the needed  testing_reason type ids
			$testing_reason_ids = $db->loadColumn();
			// Remove Testing_reason from the content type table
			$testing_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.testing_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($testing_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Testing_reason items
			$testing_reason_done = $db->execute();
			if ($testing_reason_done)
			{
				// If successfully remove Testing_reason add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.testing_reason) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Testing_reason items from the contentitem tag map table
			$testing_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.testing_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($testing_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Testing_reason items
			$testing_reason_done = $db->execute();
			if ($testing_reason_done)
			{
				// If successfully remove Testing_reason add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.testing_reason) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Testing_reason items from the ucm content table
			$testing_reason_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.testing_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($testing_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Testing_reason items
			$testing_reason_done = $db->execute();
			if ($testing_reason_done)
			{
				// If successfully removed Testing_reason add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.testing_reason) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Testing_reason items are cleared from DB
			foreach ($testing_reason_ids as $testing_reason_id)
			{
				// Remove Testing_reason items from the ucm base table
				$testing_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $testing_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($testing_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Testing_reason items
				$db->execute();

				// Remove Testing_reason items from the ucm history table
				$testing_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $testing_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($testing_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Testing_reason items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Counseling_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.counseling_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$counseling_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($counseling_type_found)
		{
			// Since there are load the needed  counseling_type type ids
			$counseling_type_ids = $db->loadColumn();
			// Remove Counseling_type from the content type table
			$counseling_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.counseling_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($counseling_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Counseling_type items
			$counseling_type_done = $db->execute();
			if ($counseling_type_done)
			{
				// If successfully remove Counseling_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.counseling_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Counseling_type items from the contentitem tag map table
			$counseling_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.counseling_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($counseling_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Counseling_type items
			$counseling_type_done = $db->execute();
			if ($counseling_type_done)
			{
				// If successfully remove Counseling_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.counseling_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Counseling_type items from the ucm content table
			$counseling_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.counseling_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($counseling_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Counseling_type items
			$counseling_type_done = $db->execute();
			if ($counseling_type_done)
			{
				// If successfully removed Counseling_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.counseling_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Counseling_type items are cleared from DB
			foreach ($counseling_type_ids as $counseling_type_id)
			{
				// Remove Counseling_type items from the ucm base table
				$counseling_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $counseling_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($counseling_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Counseling_type items
				$db->execute();

				// Remove Counseling_type items from the ucm history table
				$counseling_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $counseling_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($counseling_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Counseling_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Health_education_topic alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education_topic') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$health_education_topic_found = $db->getNumRows();
		// Now check if there were any rows
		if ($health_education_topic_found)
		{
			// Since there are load the needed  health_education_topic type ids
			$health_education_topic_ids = $db->loadColumn();
			// Remove Health_education_topic from the content type table
			$health_education_topic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education_topic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($health_education_topic_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education_topic items
			$health_education_topic_done = $db->execute();
			if ($health_education_topic_done)
			{
				// If successfully remove Health_education_topic add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.health_education_topic) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Health_education_topic items from the contentitem tag map table
			$health_education_topic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education_topic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($health_education_topic_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education_topic items
			$health_education_topic_done = $db->execute();
			if ($health_education_topic_done)
			{
				// If successfully remove Health_education_topic add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.health_education_topic) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Health_education_topic items from the ucm content table
			$health_education_topic_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.health_education_topic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($health_education_topic_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education_topic items
			$health_education_topic_done = $db->execute();
			if ($health_education_topic_done)
			{
				// If successfully removed Health_education_topic add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.health_education_topic) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Health_education_topic items are cleared from DB
			foreach ($health_education_topic_ids as $health_education_topic_id)
			{
				// Remove Health_education_topic items from the ucm base table
				$health_education_topic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_topic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($health_education_topic_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education_topic items
				$db->execute();

				// Remove Health_education_topic items from the ucm history table
				$health_education_topic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_topic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($health_education_topic_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education_topic items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Immunisation_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$immunisation_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($immunisation_type_found)
		{
			// Since there are load the needed  immunisation_type type ids
			$immunisation_type_ids = $db->loadColumn();
			// Remove Immunisation_type from the content type table
			$immunisation_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($immunisation_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_type items
			$immunisation_type_done = $db->execute();
			if ($immunisation_type_done)
			{
				// If successfully remove Immunisation_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Immunisation_type items from the contentitem tag map table
			$immunisation_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($immunisation_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_type items
			$immunisation_type_done = $db->execute();
			if ($immunisation_type_done)
			{
				// If successfully remove Immunisation_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Immunisation_type items from the ucm content table
			$immunisation_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.immunisation_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($immunisation_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_type items
			$immunisation_type_done = $db->execute();
			if ($immunisation_type_done)
			{
				// If successfully removed Immunisation_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.immunisation_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Immunisation_type items are cleared from DB
			foreach ($immunisation_type_ids as $immunisation_type_id)
			{
				// Remove Immunisation_type items from the ucm base table
				$immunisation_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($immunisation_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_type items
				$db->execute();

				// Remove Immunisation_type items from the ucm history table
				$immunisation_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($immunisation_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Strength alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.strength') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$strength_found = $db->getNumRows();
		// Now check if there were any rows
		if ($strength_found)
		{
			// Since there are load the needed  strength type ids
			$strength_ids = $db->loadColumn();
			// Remove Strength from the content type table
			$strength_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.strength') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($strength_condition);
			$db->setQuery($query);
			// Execute the query to remove Strength items
			$strength_done = $db->execute();
			if ($strength_done)
			{
				// If successfully remove Strength add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.strength) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Strength items from the contentitem tag map table
			$strength_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.strength') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($strength_condition);
			$db->setQuery($query);
			// Execute the query to remove Strength items
			$strength_done = $db->execute();
			if ($strength_done)
			{
				// If successfully remove Strength add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.strength) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Strength items from the ucm content table
			$strength_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.strength') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($strength_condition);
			$db->setQuery($query);
			// Execute the query to remove Strength items
			$strength_done = $db->execute();
			if ($strength_done)
			{
				// If successfully removed Strength add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.strength) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Strength items are cleared from DB
			foreach ($strength_ids as $strength_id)
			{
				// Remove Strength items from the ucm base table
				$strength_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $strength_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($strength_condition);
				$db->setQuery($query);
				// Execute the query to remove Strength items
				$db->execute();

				// Remove Strength items from the ucm history table
				$strength_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $strength_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($strength_condition);
				$db->setQuery($query);
				// Execute the query to remove Strength items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Referral alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.referral') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$referral_found = $db->getNumRows();
		// Now check if there were any rows
		if ($referral_found)
		{
			// Since there are load the needed  referral type ids
			$referral_ids = $db->loadColumn();
			// Remove Referral from the content type table
			$referral_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.referral') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($referral_condition);
			$db->setQuery($query);
			// Execute the query to remove Referral items
			$referral_done = $db->execute();
			if ($referral_done)
			{
				// If successfully remove Referral add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.referral) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Referral items from the contentitem tag map table
			$referral_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.referral') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($referral_condition);
			$db->setQuery($query);
			// Execute the query to remove Referral items
			$referral_done = $db->execute();
			if ($referral_done)
			{
				// If successfully remove Referral add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.referral) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Referral items from the ucm content table
			$referral_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.referral') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($referral_condition);
			$db->setQuery($query);
			// Execute the query to remove Referral items
			$referral_done = $db->execute();
			if ($referral_done)
			{
				// If successfully removed Referral add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.referral) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Referral items are cleared from DB
			foreach ($referral_ids as $referral_id)
			{
				// Remove Referral items from the ucm base table
				$referral_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $referral_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($referral_condition);
				$db->setQuery($query);
				// Execute the query to remove Referral items
				$db->execute();

				// Remove Referral items from the ucm history table
				$referral_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $referral_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($referral_condition);
				$db->setQuery($query);
				// Execute the query to remove Referral items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Planning_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.planning_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$planning_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($planning_type_found)
		{
			// Since there are load the needed  planning_type type ids
			$planning_type_ids = $db->loadColumn();
			// Remove Planning_type from the content type table
			$planning_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.planning_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($planning_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Planning_type items
			$planning_type_done = $db->execute();
			if ($planning_type_done)
			{
				// If successfully remove Planning_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.planning_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Planning_type items from the contentitem tag map table
			$planning_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.planning_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($planning_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Planning_type items
			$planning_type_done = $db->execute();
			if ($planning_type_done)
			{
				// If successfully remove Planning_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.planning_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Planning_type items from the ucm content table
			$planning_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.planning_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($planning_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Planning_type items
			$planning_type_done = $db->execute();
			if ($planning_type_done)
			{
				// If successfully removed Planning_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.planning_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Planning_type items are cleared from DB
			foreach ($planning_type_ids as $planning_type_id)
			{
				// Remove Planning_type items from the ucm base table
				$planning_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $planning_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($planning_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Planning_type items
				$db->execute();

				// Remove Planning_type items from the ucm history table
				$planning_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $planning_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($planning_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Planning_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Diagnosis_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.diagnosis_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$diagnosis_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($diagnosis_type_found)
		{
			// Since there are load the needed  diagnosis_type type ids
			$diagnosis_type_ids = $db->loadColumn();
			// Remove Diagnosis_type from the content type table
			$diagnosis_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.diagnosis_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($diagnosis_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Diagnosis_type items
			$diagnosis_type_done = $db->execute();
			if ($diagnosis_type_done)
			{
				// If successfully remove Diagnosis_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.diagnosis_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Diagnosis_type items from the contentitem tag map table
			$diagnosis_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.diagnosis_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($diagnosis_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Diagnosis_type items
			$diagnosis_type_done = $db->execute();
			if ($diagnosis_type_done)
			{
				// If successfully remove Diagnosis_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.diagnosis_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Diagnosis_type items from the ucm content table
			$diagnosis_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.diagnosis_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($diagnosis_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Diagnosis_type items
			$diagnosis_type_done = $db->execute();
			if ($diagnosis_type_done)
			{
				// If successfully removed Diagnosis_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.diagnosis_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Diagnosis_type items are cleared from DB
			foreach ($diagnosis_type_ids as $diagnosis_type_id)
			{
				// Remove Diagnosis_type items from the ucm base table
				$diagnosis_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $diagnosis_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($diagnosis_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Diagnosis_type items
				$db->execute();

				// Remove Diagnosis_type items from the ucm history table
				$diagnosis_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $diagnosis_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($diagnosis_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Diagnosis_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Nonpay_reason alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.nonpay_reason') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$nonpay_reason_found = $db->getNumRows();
		// Now check if there were any rows
		if ($nonpay_reason_found)
		{
			// Since there are load the needed  nonpay_reason type ids
			$nonpay_reason_ids = $db->loadColumn();
			// Remove Nonpay_reason from the content type table
			$nonpay_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.nonpay_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($nonpay_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Nonpay_reason items
			$nonpay_reason_done = $db->execute();
			if ($nonpay_reason_done)
			{
				// If successfully remove Nonpay_reason add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.nonpay_reason) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Nonpay_reason items from the contentitem tag map table
			$nonpay_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.nonpay_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($nonpay_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Nonpay_reason items
			$nonpay_reason_done = $db->execute();
			if ($nonpay_reason_done)
			{
				// If successfully remove Nonpay_reason add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.nonpay_reason) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Nonpay_reason items from the ucm content table
			$nonpay_reason_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.nonpay_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($nonpay_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Nonpay_reason items
			$nonpay_reason_done = $db->execute();
			if ($nonpay_reason_done)
			{
				// If successfully removed Nonpay_reason add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.nonpay_reason) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Nonpay_reason items are cleared from DB
			foreach ($nonpay_reason_ids as $nonpay_reason_id)
			{
				// Remove Nonpay_reason items from the ucm base table
				$nonpay_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $nonpay_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($nonpay_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Nonpay_reason items
				$db->execute();

				// Remove Nonpay_reason items from the ucm history table
				$nonpay_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $nonpay_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($nonpay_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Nonpay_reason items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Medication alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.medication') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$medication_found = $db->getNumRows();
		// Now check if there were any rows
		if ($medication_found)
		{
			// Since there are load the needed  medication type ids
			$medication_ids = $db->loadColumn();
			// Remove Medication from the content type table
			$medication_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.medication') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($medication_condition);
			$db->setQuery($query);
			// Execute the query to remove Medication items
			$medication_done = $db->execute();
			if ($medication_done)
			{
				// If successfully remove Medication add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.medication) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Medication items from the contentitem tag map table
			$medication_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.medication') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($medication_condition);
			$db->setQuery($query);
			// Execute the query to remove Medication items
			$medication_done = $db->execute();
			if ($medication_done)
			{
				// If successfully remove Medication add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.medication) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Medication items from the ucm content table
			$medication_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.medication') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($medication_condition);
			$db->setQuery($query);
			// Execute the query to remove Medication items
			$medication_done = $db->execute();
			if ($medication_done)
			{
				// If successfully removed Medication add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.medication) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Medication items are cleared from DB
			foreach ($medication_ids as $medication_id)
			{
				// Remove Medication items from the ucm base table
				$medication_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $medication_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($medication_condition);
				$db->setQuery($query);
				// Execute the query to remove Medication items
				$db->execute();

				// Remove Medication items from the ucm history table
				$medication_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $medication_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($medication_condition);
				$db->setQuery($query);
				// Execute the query to remove Medication items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Payment_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$payment_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($payment_type_found)
		{
			// Since there are load the needed  payment_type type ids
			$payment_type_ids = $db->loadColumn();
			// Remove Payment_type from the content type table
			$payment_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($payment_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_type items
			$payment_type_done = $db->execute();
			if ($payment_type_done)
			{
				// If successfully remove Payment_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.payment_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Payment_type items from the contentitem tag map table
			$payment_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($payment_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_type items
			$payment_type_done = $db->execute();
			if ($payment_type_done)
			{
				// If successfully remove Payment_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.payment_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Payment_type items from the ucm content table
			$payment_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.payment_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($payment_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_type items
			$payment_type_done = $db->execute();
			if ($payment_type_done)
			{
				// If successfully removed Payment_type add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.payment_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Payment_type items are cleared from DB
			foreach ($payment_type_ids as $payment_type_id)
			{
				// Remove Payment_type items from the ucm base table
				$payment_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($payment_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment_type items
				$db->execute();

				// Remove Payment_type items from the ucm history table
				$payment_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($payment_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Administration_part alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.administration_part') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$administration_part_found = $db->getNumRows();
		// Now check if there were any rows
		if ($administration_part_found)
		{
			// Since there are load the needed  administration_part type ids
			$administration_part_ids = $db->loadColumn();
			// Remove Administration_part from the content type table
			$administration_part_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.administration_part') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($administration_part_condition);
			$db->setQuery($query);
			// Execute the query to remove Administration_part items
			$administration_part_done = $db->execute();
			if ($administration_part_done)
			{
				// If successfully remove Administration_part add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.administration_part) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Administration_part items from the contentitem tag map table
			$administration_part_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.administration_part') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($administration_part_condition);
			$db->setQuery($query);
			// Execute the query to remove Administration_part items
			$administration_part_done = $db->execute();
			if ($administration_part_done)
			{
				// If successfully remove Administration_part add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.administration_part) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Administration_part items from the ucm content table
			$administration_part_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.administration_part') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($administration_part_condition);
			$db->setQuery($query);
			// Execute the query to remove Administration_part items
			$administration_part_done = $db->execute();
			if ($administration_part_done)
			{
				// If successfully removed Administration_part add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.administration_part) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Administration_part items are cleared from DB
			foreach ($administration_part_ids as $administration_part_id)
			{
				// Remove Administration_part items from the ucm base table
				$administration_part_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $administration_part_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($administration_part_condition);
				$db->setQuery($query);
				// Execute the query to remove Administration_part items
				$db->execute();

				// Remove Administration_part items from the ucm history table
				$administration_part_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $administration_part_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($administration_part_condition);
				$db->setQuery($query);
				// Execute the query to remove Administration_part items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Site alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.site') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$site_found = $db->getNumRows();
		// Now check if there were any rows
		if ($site_found)
		{
			// Since there are load the needed  site type ids
			$site_ids = $db->loadColumn();
			// Remove Site from the content type table
			$site_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.site') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($site_condition);
			$db->setQuery($query);
			// Execute the query to remove Site items
			$site_done = $db->execute();
			if ($site_done)
			{
				// If successfully remove Site add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.site) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Site items from the contentitem tag map table
			$site_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.site') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($site_condition);
			$db->setQuery($query);
			// Execute the query to remove Site items
			$site_done = $db->execute();
			if ($site_done)
			{
				// If successfully remove Site add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.site) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Site items from the ucm content table
			$site_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.site') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($site_condition);
			$db->setQuery($query);
			// Execute the query to remove Site items
			$site_done = $db->execute();
			if ($site_done)
			{
				// If successfully removed Site add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.site) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Site items are cleared from DB
			foreach ($site_ids as $site_id)
			{
				// Remove Site items from the ucm base table
				$site_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $site_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($site_condition);
				$db->setQuery($query);
				// Execute the query to remove Site items
				$db->execute();

				// Remove Site items from the ucm history table
				$site_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $site_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($site_condition);
				$db->setQuery($query);
				// Execute the query to remove Site items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Unit alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.unit') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$unit_found = $db->getNumRows();
		// Now check if there were any rows
		if ($unit_found)
		{
			// Since there are load the needed  unit type ids
			$unit_ids = $db->loadColumn();
			// Remove Unit from the content type table
			$unit_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.unit') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($unit_condition);
			$db->setQuery($query);
			// Execute the query to remove Unit items
			$unit_done = $db->execute();
			if ($unit_done)
			{
				// If successfully remove Unit add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.unit) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Unit items from the contentitem tag map table
			$unit_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.unit') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($unit_condition);
			$db->setQuery($query);
			// Execute the query to remove Unit items
			$unit_done = $db->execute();
			if ($unit_done)
			{
				// If successfully remove Unit add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.unit) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Unit items from the ucm content table
			$unit_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.unit') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($unit_condition);
			$db->setQuery($query);
			// Execute the query to remove Unit items
			$unit_done = $db->execute();
			if ($unit_done)
			{
				// If successfully removed Unit add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.unit) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Unit items are cleared from DB
			foreach ($unit_ids as $unit_id)
			{
				// Remove Unit items from the ucm base table
				$unit_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $unit_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($unit_condition);
				$db->setQuery($query);
				// Execute the query to remove Unit items
				$db->execute();

				// Remove Unit items from the ucm history table
				$unit_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $unit_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($unit_condition);
				$db->setQuery($query);
				// Execute the query to remove Unit items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Clinic alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.clinic') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$clinic_found = $db->getNumRows();
		// Now check if there were any rows
		if ($clinic_found)
		{
			// Since there are load the needed  clinic type ids
			$clinic_ids = $db->loadColumn();
			// Remove Clinic from the content type table
			$clinic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done)
			{
				// If successfully remove Clinic add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.clinic) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Clinic items from the contentitem tag map table
			$clinic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done)
			{
				// If successfully remove Clinic add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.clinic) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Clinic items from the ucm content table
			$clinic_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealthportal.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done)
			{
				// If successfully removed Clinic add queued success message.
				$app->enqueueMessage(Text::_('The (com_ehealthportal.clinic) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Clinic items are cleared from DB
			foreach ($clinic_ids as $clinic_id)
			{
				// Remove Clinic items from the ucm base table
				$clinic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $clinic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($clinic_condition);
				$db->setQuery($query);
				// Execute the query to remove Clinic items
				$db->execute();

				// Remove Clinic items from the ucm history table
				$clinic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $clinic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($clinic_condition);
				$db->setQuery($query);
				// Execute the query to remove Clinic items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(Text::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(Text::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove ehealthportal assets from the assets table
		$ehealthportal_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_ehealthportal%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($ehealthportal_condition);
		$db->setQuery($query);
		$clinic_done = $db->execute();
		if ($clinic_done)
		{
			// If successfully removed ehealthportal add queued success message.
			$app->enqueueMessage(Text::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// Get the biggest rule column in the assets table at this point.
		$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$db->setQuery($get_rule_length);
		if ($db->execute())
		{
			$rule_length = $db->loadResult();
			// Check the size of the rules column
			if ($rule_length < 5120)
			{
				// Revert the assets table rules column back to the default
				$revert_rule = "ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';";
				$db->setQuery($revert_rule);
				$db->execute();
				$app->enqueueMessage(Text::_('Reverted the <b>#__assets</b> table rules column back to its default size of varchar(5120)'));
			}
			else
			{

				$app->enqueueMessage(Text::_('Could not revert the <b>#__assets</b> table rules column back to its default size of varchar(5120), since there is still one or more components that still requires the column to be larger.'));
			}
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal from the action_logs_extensions table
		$ehealthportal_action_logs_extensions = array( $db->quoteName('extension') . ' = ' . $db->quote('com_ehealthportal') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_logs_extensions'));
		$query->where($ehealthportal_action_logs_extensions);
		$db->setQuery($query);
		// Execute the query to remove Ehealthportal
		$ehealthportal_removed_done = $db->execute();
		if ($ehealthportal_removed_done)
		{
			// If successfully remove Ehealthportal add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal extension was removed from the <b>#__action_logs_extensions</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Payment from the action_log_config table
		$payment_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($payment_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.payment
		$payment_action_log_config_done = $db->execute();
		if ($payment_action_log_config_done)
		{
			// If successfully removed Ehealthportal Payment add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.payment type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal General_medical_check_up from the action_log_config table
		$general_medical_check_up_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.general_medical_check_up') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($general_medical_check_up_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.general_medical_check_up
		$general_medical_check_up_action_log_config_done = $db->execute();
		if ($general_medical_check_up_action_log_config_done)
		{
			// If successfully removed Ehealthportal General_medical_check_up add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.general_medical_check_up type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Antenatal_care from the action_log_config table
		$antenatal_care_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.antenatal_care') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($antenatal_care_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.antenatal_care
		$antenatal_care_action_log_config_done = $db->execute();
		if ($antenatal_care_action_log_config_done)
		{
			// If successfully removed Ehealthportal Antenatal_care add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.antenatal_care type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Immunisation from the action_log_config table
		$immunisation_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($immunisation_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.immunisation
		$immunisation_action_log_config_done = $db->execute();
		if ($immunisation_action_log_config_done)
		{
			// If successfully removed Ehealthportal Immunisation add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.immunisation type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Vmmc from the action_log_config table
		$vmmc_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.vmmc') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($vmmc_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.vmmc
		$vmmc_action_log_config_done = $db->execute();
		if ($vmmc_action_log_config_done)
		{
			// If successfully removed Ehealthportal Vmmc add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.vmmc type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Prostate_and_testicular_cancer from the action_log_config table
		$prostate_and_testicular_cancer_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.prostate_and_testicular_cancer') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($prostate_and_testicular_cancer_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.prostate_and_testicular_cancer
		$prostate_and_testicular_cancer_action_log_config_done = $db->execute();
		if ($prostate_and_testicular_cancer_action_log_config_done)
		{
			// If successfully removed Ehealthportal Prostate_and_testicular_cancer add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.prostate_and_testicular_cancer type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Tuberculosis from the action_log_config table
		$tuberculosis_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.tuberculosis') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($tuberculosis_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.tuberculosis
		$tuberculosis_action_log_config_done = $db->execute();
		if ($tuberculosis_action_log_config_done)
		{
			// If successfully removed Ehealthportal Tuberculosis add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.tuberculosis type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Hiv_counseling_and_testing from the action_log_config table
		$hiv_counseling_and_testing_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.hiv_counseling_and_testing') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($hiv_counseling_and_testing_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.hiv_counseling_and_testing
		$hiv_counseling_and_testing_action_log_config_done = $db->execute();
		if ($hiv_counseling_and_testing_action_log_config_done)
		{
			// If successfully removed Ehealthportal Hiv_counseling_and_testing add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.hiv_counseling_and_testing type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Family_planning from the action_log_config table
		$family_planning_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.family_planning') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($family_planning_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.family_planning
		$family_planning_action_log_config_done = $db->execute();
		if ($family_planning_action_log_config_done)
		{
			// If successfully removed Ehealthportal Family_planning add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.family_planning type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Health_education from the action_log_config table
		$health_education_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($health_education_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.health_education
		$health_education_action_log_config_done = $db->execute();
		if ($health_education_action_log_config_done)
		{
			// If successfully removed Ehealthportal Health_education add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.health_education type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Cervical_cancer from the action_log_config table
		$cervical_cancer_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.cervical_cancer') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($cervical_cancer_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.cervical_cancer
		$cervical_cancer_action_log_config_done = $db->execute();
		if ($cervical_cancer_action_log_config_done)
		{
			// If successfully removed Ehealthportal Cervical_cancer add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.cervical_cancer type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Breast_cancer from the action_log_config table
		$breast_cancer_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.breast_cancer') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($breast_cancer_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.breast_cancer
		$breast_cancer_action_log_config_done = $db->execute();
		if ($breast_cancer_action_log_config_done)
		{
			// If successfully removed Ehealthportal Breast_cancer add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.breast_cancer type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Test from the action_log_config table
		$test_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.test') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($test_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.test
		$test_action_log_config_done = $db->execute();
		if ($test_action_log_config_done)
		{
			// If successfully removed Ehealthportal Test add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.test type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Foetal_lie from the action_log_config table
		$foetal_lie_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_lie') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($foetal_lie_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.foetal_lie
		$foetal_lie_action_log_config_done = $db->execute();
		if ($foetal_lie_action_log_config_done)
		{
			// If successfully removed Ehealthportal Foetal_lie add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.foetal_lie type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Immunisation_vaccine_type from the action_log_config table
		$immunisation_vaccine_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_vaccine_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($immunisation_vaccine_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.immunisation_vaccine_type
		$immunisation_vaccine_type_action_log_config_done = $db->execute();
		if ($immunisation_vaccine_type_action_log_config_done)
		{
			// If successfully removed Ehealthportal Immunisation_vaccine_type add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.immunisation_vaccine_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Foetal_engagement from the action_log_config table
		$foetal_engagement_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_engagement') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($foetal_engagement_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.foetal_engagement
		$foetal_engagement_action_log_config_done = $db->execute();
		if ($foetal_engagement_action_log_config_done)
		{
			// If successfully removed Ehealthportal Foetal_engagement add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.foetal_engagement type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Foetal_presentation from the action_log_config table
		$foetal_presentation_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.foetal_presentation') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($foetal_presentation_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.foetal_presentation
		$foetal_presentation_action_log_config_done = $db->execute();
		if ($foetal_presentation_action_log_config_done)
		{
			// If successfully removed Ehealthportal Foetal_presentation add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.foetal_presentation type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Testing_reason from the action_log_config table
		$testing_reason_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.testing_reason') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($testing_reason_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.testing_reason
		$testing_reason_action_log_config_done = $db->execute();
		if ($testing_reason_action_log_config_done)
		{
			// If successfully removed Ehealthportal Testing_reason add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.testing_reason type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Counseling_type from the action_log_config table
		$counseling_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.counseling_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($counseling_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.counseling_type
		$counseling_type_action_log_config_done = $db->execute();
		if ($counseling_type_action_log_config_done)
		{
			// If successfully removed Ehealthportal Counseling_type add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.counseling_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Health_education_topic from the action_log_config table
		$health_education_topic_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.health_education_topic') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($health_education_topic_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.health_education_topic
		$health_education_topic_action_log_config_done = $db->execute();
		if ($health_education_topic_action_log_config_done)
		{
			// If successfully removed Ehealthportal Health_education_topic add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.health_education_topic type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Immunisation_type from the action_log_config table
		$immunisation_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.immunisation_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($immunisation_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.immunisation_type
		$immunisation_type_action_log_config_done = $db->execute();
		if ($immunisation_type_action_log_config_done)
		{
			// If successfully removed Ehealthportal Immunisation_type add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.immunisation_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Strength from the action_log_config table
		$strength_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.strength') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($strength_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.strength
		$strength_action_log_config_done = $db->execute();
		if ($strength_action_log_config_done)
		{
			// If successfully removed Ehealthportal Strength add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.strength type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Referral from the action_log_config table
		$referral_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.referral') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($referral_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.referral
		$referral_action_log_config_done = $db->execute();
		if ($referral_action_log_config_done)
		{
			// If successfully removed Ehealthportal Referral add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.referral type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Planning_type from the action_log_config table
		$planning_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.planning_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($planning_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.planning_type
		$planning_type_action_log_config_done = $db->execute();
		if ($planning_type_action_log_config_done)
		{
			// If successfully removed Ehealthportal Planning_type add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.planning_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Diagnosis_type from the action_log_config table
		$diagnosis_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.diagnosis_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($diagnosis_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.diagnosis_type
		$diagnosis_type_action_log_config_done = $db->execute();
		if ($diagnosis_type_action_log_config_done)
		{
			// If successfully removed Ehealthportal Diagnosis_type add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.diagnosis_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Nonpay_reason from the action_log_config table
		$nonpay_reason_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.nonpay_reason') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($nonpay_reason_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.nonpay_reason
		$nonpay_reason_action_log_config_done = $db->execute();
		if ($nonpay_reason_action_log_config_done)
		{
			// If successfully removed Ehealthportal Nonpay_reason add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.nonpay_reason type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Medication from the action_log_config table
		$medication_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.medication') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($medication_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.medication
		$medication_action_log_config_done = $db->execute();
		if ($medication_action_log_config_done)
		{
			// If successfully removed Ehealthportal Medication add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.medication type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Payment_type from the action_log_config table
		$payment_type_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.payment_type') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($payment_type_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.payment_type
		$payment_type_action_log_config_done = $db->execute();
		if ($payment_type_action_log_config_done)
		{
			// If successfully removed Ehealthportal Payment_type add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.payment_type type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Administration_part from the action_log_config table
		$administration_part_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.administration_part') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($administration_part_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.administration_part
		$administration_part_action_log_config_done = $db->execute();
		if ($administration_part_action_log_config_done)
		{
			// If successfully removed Ehealthportal Administration_part add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.administration_part type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Site from the action_log_config table
		$site_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.site') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($site_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.site
		$site_action_log_config_done = $db->execute();
		if ($site_action_log_config_done)
		{
			// If successfully removed Ehealthportal Site add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.site type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Unit from the action_log_config table
		$unit_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.unit') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($unit_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.unit
		$unit_action_log_config_done = $db->execute();
		if ($unit_action_log_config_done)
		{
			// If successfully removed Ehealthportal Unit add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.unit type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = Factory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = Factory::getApplication();
		}
		// Remove Ehealthportal Clinic from the action_log_config table
		$clinic_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealthportal.clinic') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($clinic_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_ehealthportal.clinic
		$clinic_action_log_config_done = $db->execute();
		if ($clinic_action_log_config_done)
		{
			// If successfully removed Ehealthportal Clinic add queued success message.
			$app->enqueueMessage(Text::_('The com_ehealthportal.clinic type alias was removed from the <b>#__action_log_config</b> table'));
		}
		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:joomla@vdm.io">joomla@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://git.vdm.dev/joomla/eHealth-Portal" target="_blank">https://git.vdm.dev/joomla/eHealth-Portal</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(ComponentAdapter $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, ComponentAdapter $parent)
	{
		// get application
		$app = Factory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new Version();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		// check if the PHPExcel stuff is still around
		if (File::exists(JPATH_ADMINISTRATOR . '/components/com_ehealthportal/helpers/PHPExcel.php'))
		{
			// We need to remove this old PHPExcel folder
			$this->removeFolder(JPATH_ADMINISTRATOR . '/components/com_ehealthportal/helpers/PHPExcel');
			// We need to remove this old PHPExcel file
			File::delete(JPATH_ADMINISTRATOR . '/components/com_ehealthportal/helpers/PHPExcel.php');
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, ComponentAdapter $parent)
	{
		// get application
		$app = Factory::getApplication();
		// We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = Factory::getDbo();

			// Create the payment content type object.
			$payment = new stdClass();
			$payment->type_title = 'Ehealthportal Payment';
			$payment->type_alias = 'com_ehealthportal.payment';
			$payment->table = '{"special": {"dbtable": "#__ehealthportal_payment","key": "id","type": "Payment","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","payment_category":"payment_category","payment_type":"payment_type","payment_amount":"payment_amount","nonpay_reason":"nonpay_reason","receipt_no":"receipt_no","guid":"guid"}}';
			$payment->router = 'EhealthportalHelperRoute::getPaymentRoute';
			$payment->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","payment_type","nonpay_reason"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "payment_type","targetTable": "#__ehealthportal_payment_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "nonpay_reason","targetTable": "#__ehealthportal_nonpay_reason","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$payment_Inserted = $db->insertObject('#__content_types', $payment);

			// Create the general_medical_check_up content type object.
			$general_medical_check_up = new stdClass();
			$general_medical_check_up->type_title = 'Ehealthportal General_medical_check_up';
			$general_medical_check_up->type_alias = 'com_ehealthportal.general_medical_check_up';
			$general_medical_check_up->table = '{"special": {"dbtable": "#__ehealthportal_general_medical_check_up","key": "id","type": "General_medical_check_up","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$general_medical_check_up->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bp_diastolic_one":"bp_diastolic_one","bp_systolic_one":"bp_systolic_one","temp_one":"temp_one","weight":"weight","pulse":"pulse","chronic_medication":"chronic_medication","bp_diastolic_two":"bp_diastolic_two","bp_systolic_two":"bp_systolic_two","temp_two":"temp_two","height":"height","bmi":"bmi","complaint":"complaint","investigations":"investigations","notes":"notes","diagnosis":"diagnosis","referral":"referral","reason":"reason","guid":"guid"}}';
			$general_medical_check_up->router = 'EhealthportalHelperRoute::getGeneral_medical_check_upRoute';
			$general_medical_check_up->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/general_medical_check_up.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bp_diastolic_one","bp_systolic_one","pulse","bp_diastolic_two","bp_systolic_two","diagnosis","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_diagnosis_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$general_medical_check_up_Inserted = $db->insertObject('#__content_types', $general_medical_check_up);

			// Create the antenatal_care content type object.
			$antenatal_care = new stdClass();
			$antenatal_care->type_title = 'Ehealthportal Antenatal_care';
			$antenatal_care->type_alias = 'com_ehealthportal.antenatal_care';
			$antenatal_care->table = '{"special": {"dbtable": "#__ehealthportal_antenatal_care","key": "id","type": "Antenatal_care","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$antenatal_care->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","foetal_lie":"foetal_lie","foetal_presentation":"foetal_presentation","foetal_engagement":"foetal_engagement","foetal_heart_rate":"foetal_heart_rate","foetal_movements":"foetal_movements","guid":"guid","caesarean_sections":"caesarean_sections","last_menstrual_period":"last_menstrual_period","normal_births":"normal_births","still_births":"still_births","miscarriages":"miscarriages","live_births":"live_births","pregnancies_excl":"pregnancies_excl"}}';
			$antenatal_care->router = 'EhealthportalHelperRoute::getAntenatal_careRoute';
			$antenatal_care->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/antenatal_care.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","foetal_lie","foetal_presentation","foetal_engagement","foetal_heart_rate","caesarean_sections","normal_births","still_births","miscarriages","live_births","pregnancies_excl"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_lie","targetTable": "#__ehealthportal_foetal_lie","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_presentation","targetTable": "#__ehealthportal_foetal_presentation","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_engagement","targetTable": "#__ehealthportal_foetal_engagement","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$antenatal_care_Inserted = $db->insertObject('#__content_types', $antenatal_care);

			// Create the immunisation content type object.
			$immunisation = new stdClass();
			$immunisation->type_title = 'Ehealthportal Immunisation';
			$immunisation->type_alias = 'com_ehealthportal.immunisation';
			$immunisation->table = '{"special": {"dbtable": "#__ehealthportal_immunisation","key": "id","type": "Immunisation","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","referral":"referral","immunisation_up_to_date":"immunisation_up_to_date","reason":"reason","guid":"guid"}}';
			$immunisation->router = 'EhealthportalHelperRoute::getImmunisationRoute';
			$immunisation->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/immunisation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$immunisation_Inserted = $db->insertObject('#__content_types', $immunisation);

			// Create the vmmc content type object.
			$vmmc = new stdClass();
			$vmmc->type_title = 'Ehealthportal Vmmc';
			$vmmc->type_alias = 'com_ehealthportal.vmmc';
			$vmmc->table = '{"special": {"dbtable": "#__ehealthportal_vmmc","key": "id","type": "Vmmc","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$vmmc->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","are_you_circumcised":"are_you_circumcised","info_ben_vmcc":"info_ben_vmcc","interested_in_vmmc":"interested_in_vmmc","vmmc_gender":"vmmc_gender","reason":"reason","referral":"referral","guid":"guid","partner_circumcised":"partner_circumcised"}}';
			$vmmc->router = 'EhealthportalHelperRoute::getVmmcRoute';
			$vmmc->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/vmmc.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$vmmc_Inserted = $db->insertObject('#__content_types', $vmmc);

			// Create the prostate_and_testicular_cancer content type object.
			$prostate_and_testicular_cancer = new stdClass();
			$prostate_and_testicular_cancer->type_title = 'Ehealthportal Prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->type_alias = 'com_ehealthportal.prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->table = '{"special": {"dbtable": "#__ehealthportal_prostate_and_testicular_cancer","key": "id","type": "Prostate_and_testicular_cancer","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$prostate_and_testicular_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","ptc_age":"ptc_age","ptc_fam_history":"ptc_fam_history","ptc_diet":"ptc_diet","ptc_phy_activity":"ptc_phy_activity","ptc_overweight":"ptc_overweight","ptc_urinate":"ptc_urinate","ptc_urine_freq":"ptc_urine_freq","referral":"referral","reason":"reason","txt_ptc_urine_freq":"txt_ptc_urine_freq","txt_ptc_urinate":"txt_ptc_urinate","txt_ptc_age":"txt_ptc_age","txt_ptc_phy_activity":"txt_ptc_phy_activity","txt_ptc_diet":"txt_ptc_diet","txt_ptc_fam_history":"txt_ptc_fam_history","guid":"guid","txt_ptc_overweight":"txt_ptc_overweight"}}';
			$prostate_and_testicular_cancer->router = 'EhealthportalHelperRoute::getProstate_and_testicular_cancerRoute';
			$prostate_and_testicular_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/prostate_and_testicular_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$prostate_and_testicular_cancer_Inserted = $db->insertObject('#__content_types', $prostate_and_testicular_cancer);

			// Create the tuberculosis content type object.
			$tuberculosis = new stdClass();
			$tuberculosis->type_title = 'Ehealthportal Tuberculosis';
			$tuberculosis->type_alias = 'com_ehealthportal.tuberculosis';
			$tuberculosis->table = '{"special": {"dbtable": "#__ehealthportal_tuberculosis","key": "id","type": "Tuberculosis","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tuberculosis->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","recurring_night_sweats":"recurring_night_sweats","tb_fever":"tb_fever","persistent_cough":"persistent_cough","blood_streaked_sputum":"blood_streaked_sputum","unusual_tiredness":"unusual_tiredness","pain_in_chest":"pain_in_chest","shortness_of_breath":"shortness_of_breath","diagnosed_with_disease":"diagnosed_with_disease","tb_exposed":"tb_exposed","tb_treatment":"tb_treatment","date_of_treatment":"date_of_treatment","treating_dhc":"treating_dhc","sputum_collection_one":"sputum_collection_one","tb_reason_one":"tb_reason_one","sputum_result_one":"sputum_result_one","referred_second_sputum":"referred_second_sputum","tb_reason_two":"tb_reason_two","sputum_result_two":"sputum_result_two","weight_loss_wdieting":"weight_loss_wdieting","reason":"reason","guid":"guid","referral":"referral"}}';
			$tuberculosis->router = 'EhealthportalHelperRoute::getTuberculosisRoute';
			$tuberculosis->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/tuberculosis.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$tuberculosis_Inserted = $db->insertObject('#__content_types', $tuberculosis);

			// Create the hiv_counseling_and_testing content type object.
			$hiv_counseling_and_testing = new stdClass();
			$hiv_counseling_and_testing->type_title = 'Ehealthportal Hiv_counseling_and_testing';
			$hiv_counseling_and_testing->type_alias = 'com_ehealthportal.hiv_counseling_and_testing';
			$hiv_counseling_and_testing->table = '{"special": {"dbtable": "#__ehealthportal_hiv_counseling_and_testing","key": "id","type": "Hiv_counseling_and_testing","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$hiv_counseling_and_testing->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","counseling_type":"counseling_type","testing_reason":"testing_reason","last_test_date":"last_test_date","prev_test_result":"prev_test_result","test_result_one":"test_result_one","test_result_two":"test_result_two","final_test_result":"final_test_result","eqa":"eqa","referral":"referral","reason":"reason","guid":"guid"}}';
			$hiv_counseling_and_testing->router = 'EhealthportalHelperRoute::getHiv_counseling_and_testingRoute';
			$hiv_counseling_and_testing->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/hiv_counseling_and_testing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","testing_reason","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "testing_reason","targetTable": "#__ehealthportal_testing_reason","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$hiv_counseling_and_testing_Inserted = $db->insertObject('#__content_types', $hiv_counseling_and_testing);

			// Create the family_planning content type object.
			$family_planning = new stdClass();
			$family_planning->type_title = 'Ehealthportal Family_planning';
			$family_planning->type_alias = 'com_ehealthportal.family_planning';
			$family_planning->table = '{"special": {"dbtable": "#__ehealthportal_family_planning","key": "id","type": "Family_planning","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$family_planning->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "diagnosis","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","diagnosis":"diagnosis","guid":"guid"}}';
			$family_planning->router = 'EhealthportalHelperRoute::getFamily_planningRoute';
			$family_planning->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/family_planning.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","diagnosis"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_planning_type","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$family_planning_Inserted = $db->insertObject('#__content_types', $family_planning);

			// Create the health_education content type object.
			$health_education = new stdClass();
			$health_education->type_title = 'Ehealthportal Health_education';
			$health_education->type_alias = 'com_ehealthportal.health_education';
			$health_education->table = '{"special": {"dbtable": "#__ehealthportal_health_education","key": "id","type": "Health_education","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"education_type":"education_type","health_education_topic":"health_education_topic","patient":"patient","guid":"guid"}}';
			$health_education->router = 'EhealthportalHelperRoute::getHealth_educationRoute';
			$health_education->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/health_education.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","health_education_topic"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "health_education_topic","targetTable": "#__ehealthportal_health_education_topic","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$health_education_Inserted = $db->insertObject('#__content_types', $health_education);

			// Create the cervical_cancer content type object.
			$cervical_cancer = new stdClass();
			$cervical_cancer->type_title = 'Ehealthportal Cervical_cancer';
			$cervical_cancer->type_alias = 'com_ehealthportal.cervical_cancer';
			$cervical_cancer->table = '{"special": {"dbtable": "#__ehealthportal_cervical_cancer","key": "id","type": "Cervical_cancer","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$cervical_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","cc_viginal_bleeding":"cc_viginal_bleeding","cc_v_discharge":"cc_v_discharge","cc_periods":"cc_periods","cc_smoking":"cc_smoking","cc_sex_actve":"cc_sex_actve","cc_sex_partner":"cc_sex_partner","pap_smear_collection":"pap_smear_collection","cc_result":"cc_result","referral":"referral","reason":"reason","cc_reason":"cc_reason","txt_cc_v_discharge":"txt_cc_v_discharge","txt_cc_sex_partner":"txt_cc_sex_partner","txt_cc_viginal_bleeding":"txt_cc_viginal_bleeding","txt_cc_smoking":"txt_cc_smoking","txt_cc_periods":"txt_cc_periods","guid":"guid","txt_cc_sex_actve":"txt_cc_sex_actve"}}';
			$cervical_cancer->router = 'EhealthportalHelperRoute::getCervical_cancerRoute';
			$cervical_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/cervical_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$cervical_cancer_Inserted = $db->insertObject('#__content_types', $cervical_cancer);

			// Create the breast_cancer content type object.
			$breast_cancer = new stdClass();
			$breast_cancer->type_title = 'Ehealthportal Breast_cancer';
			$breast_cancer->type_alias = 'com_ehealthportal.breast_cancer';
			$breast_cancer->table = '{"special": {"dbtable": "#__ehealthportal_breast_cancer","key": "id","type": "Breast_cancer","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$breast_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bc_age_range":"bc_age_range","bc_family_history":"bc_family_history","bc_race":"bc_race","bc_breastfeeding":"bc_breastfeeding","bc_preg_freq":"bc_preg_freq","bc_preg_age":"bc_preg_age","bc_history_hrt":"bc_history_hrt","bc_reg_exercise":"bc_reg_exercise","bc_overweight":"bc_overweight","bc_lump_near_breast":"bc_lump_near_breast","bc_dimpling":"bc_dimpling","bc_inward_nipple":"bc_inward_nipple","bc_nipple_discharge":"bc_nipple_discharge","bc_abnormal_skin":"bc_abnormal_skin","bc_breast_shape":"bc_breast_shape","referral":"referral","reason":"reason","guid":"guid","txt_bc_breast_shape":"txt_bc_breast_shape","txt_bc_abnormal_skin":"txt_bc_abnormal_skin","txt_bc_nipple_discharge":"txt_bc_nipple_discharge","txt_bc_dimpling":"txt_bc_dimpling","txt_bc_lump_near_breast":"txt_bc_lump_near_breast","txt_bc_inward_nipple":"txt_bc_inward_nipple"}}';
			$breast_cancer->router = 'EhealthportalHelperRoute::getBreast_cancerRoute';
			$breast_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/breast_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bc_preg_freq","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$breast_cancer_Inserted = $db->insertObject('#__content_types', $breast_cancer);

			// Create the test content type object.
			$test = new stdClass();
			$test->type_title = 'Ehealthportal Test';
			$test->type_alias = 'com_ehealthportal.test';
			$test->table = '{"special": {"dbtable": "#__ehealthportal_test","key": "id","type": "Test","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$test->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","urine_test_result":"urine_test_result","glucose_first_reading":"glucose_first_reading","glucose_second_reading":"glucose_second_reading","haemoglobin_reading":"haemoglobin_reading","cholesterol_reading":"cholesterol_reading","syphilis_first_reading":"syphilis_first_reading","syphilis_second_reading":"syphilis_second_reading","hepatitis_first_reading":"hepatitis_first_reading","hepatitis_second_reading":"hepatitis_second_reading","malaria_first_reading":"malaria_first_reading","malaria_second_reading":"malaria_second_reading","pregnancy_first_reading":"pregnancy_first_reading","pregnancy_second_reading":"pregnancy_second_reading","referral":"referral","reason":"reason","guid":"guid"}}';
			$test->router = 'EhealthportalHelperRoute::getTestRoute';
			$test->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/test.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","glucose_first_reading","glucose_second_reading","haemoglobin_reading","cholesterol_reading","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$test_Inserted = $db->insertObject('#__content_types', $test);

			// Create the foetal_lie content type object.
			$foetal_lie = new stdClass();
			$foetal_lie->type_title = 'Ehealthportal Foetal_lie';
			$foetal_lie->type_alias = 'com_ehealthportal.foetal_lie';
			$foetal_lie->table = '{"special": {"dbtable": "#__ehealthportal_foetal_lie","key": "id","type": "Foetal_lie","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_lie->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$foetal_lie->router = 'EhealthportalHelperRoute::getFoetal_lieRoute';
			$foetal_lie->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/foetal_lie.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$foetal_lie_Inserted = $db->insertObject('#__content_types', $foetal_lie);

			// Create the immunisation_vaccine_type content type object.
			$immunisation_vaccine_type = new stdClass();
			$immunisation_vaccine_type->type_title = 'Ehealthportal Immunisation_vaccine_type';
			$immunisation_vaccine_type->type_alias = 'com_ehealthportal.immunisation_vaccine_type';
			$immunisation_vaccine_type->table = '{"special": {"dbtable": "#__ehealthportal_immunisation_vaccine_type","key": "id","type": "Immunisation_vaccine_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_vaccine_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","administration_part":"administration_part","description":"description","guid":"guid","alias":"alias"}}';
			$immunisation_vaccine_type->router = 'EhealthportalHelperRoute::getImmunisation_vaccine_typeRoute';
			$immunisation_vaccine_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/immunisation_vaccine_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealthportal_administration_part","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$immunisation_vaccine_type_Inserted = $db->insertObject('#__content_types', $immunisation_vaccine_type);

			// Create the foetal_engagement content type object.
			$foetal_engagement = new stdClass();
			$foetal_engagement->type_title = 'Ehealthportal Foetal_engagement';
			$foetal_engagement->type_alias = 'com_ehealthportal.foetal_engagement';
			$foetal_engagement->table = '{"special": {"dbtable": "#__ehealthportal_foetal_engagement","key": "id","type": "Foetal_engagement","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_engagement->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$foetal_engagement->router = 'EhealthportalHelperRoute::getFoetal_engagementRoute';
			$foetal_engagement->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/foetal_engagement.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$foetal_engagement_Inserted = $db->insertObject('#__content_types', $foetal_engagement);

			// Create the foetal_presentation content type object.
			$foetal_presentation = new stdClass();
			$foetal_presentation->type_title = 'Ehealthportal Foetal_presentation';
			$foetal_presentation->type_alias = 'com_ehealthportal.foetal_presentation';
			$foetal_presentation->table = '{"special": {"dbtable": "#__ehealthportal_foetal_presentation","key": "id","type": "Foetal_presentation","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_presentation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$foetal_presentation->router = 'EhealthportalHelperRoute::getFoetal_presentationRoute';
			$foetal_presentation->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/foetal_presentation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$foetal_presentation_Inserted = $db->insertObject('#__content_types', $foetal_presentation);

			// Create the testing_reason content type object.
			$testing_reason = new stdClass();
			$testing_reason->type_title = 'Ehealthportal Testing_reason';
			$testing_reason->type_alias = 'com_ehealthportal.testing_reason';
			$testing_reason->table = '{"special": {"dbtable": "#__ehealthportal_testing_reason","key": "id","type": "Testing_reason","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$testing_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$testing_reason->router = 'EhealthportalHelperRoute::getTesting_reasonRoute';
			$testing_reason->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/testing_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$testing_reason_Inserted = $db->insertObject('#__content_types', $testing_reason);

			// Create the counseling_type content type object.
			$counseling_type = new stdClass();
			$counseling_type->type_title = 'Ehealthportal Counseling_type';
			$counseling_type->type_alias = 'com_ehealthportal.counseling_type';
			$counseling_type->table = '{"special": {"dbtable": "#__ehealthportal_counseling_type","key": "id","type": "Counseling_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$counseling_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$counseling_type->router = 'EhealthportalHelperRoute::getCounseling_typeRoute';
			$counseling_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/counseling_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$counseling_type_Inserted = $db->insertObject('#__content_types', $counseling_type);

			// Create the health_education_topic content type object.
			$health_education_topic = new stdClass();
			$health_education_topic->type_title = 'Ehealthportal Health_education_topic';
			$health_education_topic->type_alias = 'com_ehealthportal.health_education_topic';
			$health_education_topic->table = '{"special": {"dbtable": "#__ehealthportal_health_education_topic","key": "id","type": "Health_education_topic","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education_topic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$health_education_topic->router = 'EhealthportalHelperRoute::getHealth_education_topicRoute';
			$health_education_topic->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/health_education_topic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$health_education_topic_Inserted = $db->insertObject('#__content_types', $health_education_topic);

			// Create the immunisation_type content type object.
			$immunisation_type = new stdClass();
			$immunisation_type->type_title = 'Ehealthportal Immunisation_type';
			$immunisation_type->type_alias = 'com_ehealthportal.immunisation_type';
			$immunisation_type->table = '{"special": {"dbtable": "#__ehealthportal_immunisation_type","key": "id","type": "Immunisation_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$immunisation_type->router = 'EhealthportalHelperRoute::getImmunisation_typeRoute';
			$immunisation_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/immunisation_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$immunisation_type_Inserted = $db->insertObject('#__content_types', $immunisation_type);

			// Create the strength content type object.
			$strength = new stdClass();
			$strength->type_title = 'Ehealthportal Strength';
			$strength->type_alias = 'com_ehealthportal.strength';
			$strength->table = '{"special": {"dbtable": "#__ehealthportal_strength","key": "id","type": "Strength","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$strength->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$strength->router = 'EhealthportalHelperRoute::getStrengthRoute';
			$strength->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/strength.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$strength_Inserted = $db->insertObject('#__content_types', $strength);

			// Create the referral content type object.
			$referral = new stdClass();
			$referral->type_title = 'Ehealthportal Referral';
			$referral->type_alias = 'com_ehealthportal.referral';
			$referral->table = '{"special": {"dbtable": "#__ehealthportal_referral","key": "id","type": "Referral","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$referral->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$referral->router = 'EhealthportalHelperRoute::getReferralRoute';
			$referral->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/referral.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$referral_Inserted = $db->insertObject('#__content_types', $referral);

			// Create the planning_type content type object.
			$planning_type = new stdClass();
			$planning_type->type_title = 'Ehealthportal Planning_type';
			$planning_type->type_alias = 'com_ehealthportal.planning_type';
			$planning_type->table = '{"special": {"dbtable": "#__ehealthportal_planning_type","key": "id","type": "Planning_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$planning_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$planning_type->router = 'EhealthportalHelperRoute::getPlanning_typeRoute';
			$planning_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/planning_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$planning_type_Inserted = $db->insertObject('#__content_types', $planning_type);

			// Create the diagnosis_type content type object.
			$diagnosis_type = new stdClass();
			$diagnosis_type->type_title = 'Ehealthportal Diagnosis_type';
			$diagnosis_type->type_alias = 'com_ehealthportal.diagnosis_type';
			$diagnosis_type->table = '{"special": {"dbtable": "#__ehealthportal_diagnosis_type","key": "id","type": "Diagnosis_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$diagnosis_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$diagnosis_type->router = 'EhealthportalHelperRoute::getDiagnosis_typeRoute';
			$diagnosis_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/diagnosis_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$diagnosis_type_Inserted = $db->insertObject('#__content_types', $diagnosis_type);

			// Create the nonpay_reason content type object.
			$nonpay_reason = new stdClass();
			$nonpay_reason->type_title = 'Ehealthportal Nonpay_reason';
			$nonpay_reason->type_alias = 'com_ehealthportal.nonpay_reason';
			$nonpay_reason->table = '{"special": {"dbtable": "#__ehealthportal_nonpay_reason","key": "id","type": "Nonpay_reason","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$nonpay_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$nonpay_reason->router = 'EhealthportalHelperRoute::getNonpay_reasonRoute';
			$nonpay_reason->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/nonpay_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$nonpay_reason_Inserted = $db->insertObject('#__content_types', $nonpay_reason);

			// Create the medication content type object.
			$medication = new stdClass();
			$medication->type_title = 'Ehealthportal Medication';
			$medication->type_alias = 'com_ehealthportal.medication';
			$medication->table = '{"special": {"dbtable": "#__ehealthportal_medication","key": "id","type": "Medication","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$medication->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$medication->router = 'EhealthportalHelperRoute::getMedicationRoute';
			$medication->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/medication.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$medication_Inserted = $db->insertObject('#__content_types', $medication);

			// Create the payment_type content type object.
			$payment_type = new stdClass();
			$payment_type->type_title = 'Ehealthportal Payment_type';
			$payment_type->type_alias = 'com_ehealthportal.payment_type';
			$payment_type->table = '{"special": {"dbtable": "#__ehealthportal_payment_type","key": "id","type": "Payment_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$payment_type->router = 'EhealthportalHelperRoute::getPayment_typeRoute';
			$payment_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/payment_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$payment_type_Inserted = $db->insertObject('#__content_types', $payment_type);

			// Create the administration_part content type object.
			$administration_part = new stdClass();
			$administration_part->type_title = 'Ehealthportal Administration_part';
			$administration_part->type_alias = 'com_ehealthportal.administration_part';
			$administration_part->table = '{"special": {"dbtable": "#__ehealthportal_administration_part","key": "id","type": "Administration_part","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$administration_part->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$administration_part->router = 'EhealthportalHelperRoute::getAdministration_partRoute';
			$administration_part->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/administration_part.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$administration_part_Inserted = $db->insertObject('#__content_types', $administration_part);

			// Create the site content type object.
			$site = new stdClass();
			$site->type_title = 'Ehealthportal Site';
			$site->type_alias = 'com_ehealthportal.site';
			$site->table = '{"special": {"dbtable": "#__ehealthportal_site","key": "id","type": "Site","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$site->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "site_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"site_name":"site_name","description":"description","site_region":"site_region","guid":"guid","alias":"alias"}}';
			$site->router = 'EhealthportalHelperRoute::getSiteRoute';
			$site->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/site.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$site_Inserted = $db->insertObject('#__content_types', $site);

			// Create the unit content type object.
			$unit = new stdClass();
			$unit->type_title = 'Ehealthportal Unit';
			$unit->type_alias = 'com_ehealthportal.unit';
			$unit->table = '{"special": {"dbtable": "#__ehealthportal_unit","key": "id","type": "Unit","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$unit->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$unit->router = 'EhealthportalHelperRoute::getUnitRoute';
			$unit->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/unit.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$unit_Inserted = $db->insertObject('#__content_types', $unit);

			// Create the clinic content type object.
			$clinic = new stdClass();
			$clinic->type_title = 'Ehealthportal Clinic';
			$clinic->type_alias = 'com_ehealthportal.clinic';
			$clinic->table = '{"special": {"dbtable": "#__ehealthportal_clinic","key": "id","type": "Clinic","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$clinic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "clinic_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"clinic_name":"clinic_name","description":"description","guid":"guid","clinic_type":"clinic_type","alias":"alias"}}';
			$clinic->router = 'EhealthportalHelperRoute::getClinicRoute';
			$clinic->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$clinic_Inserted = $db->insertObject('#__content_types', $clinic);


			// Get the biggest rule column in the assets table at this point.
			$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
			$db->setQuery($get_rule_length);
			if ($db->execute())
			{
				$rule_length = $db->loadResult();
				// Check the size of the rules column
				if ($rule_length <= 43040)
				{
					// Fix the assets table rules column size
					$fix_rules_size = "ALTER TABLE `#__assets` CHANGE `rules` `rules` TEXT NOT NULL COMMENT 'JSON encoded access control. Enlarged to TEXT by JCB';";
					$db->setQuery($fix_rules_size);
					$db->execute();
					$app->enqueueMessage(Text::_('The <b>#__assets</b> table rules column was resized to the TEXT datatype for the components possible large permission rules.'));
				}
			}
			// Install the global extension params.
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Llewellyn van der Merwe","autorEmail":"joomla@vdm.io","check_in":"-1 day","save_history":"1","history_limit":"10"}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_ehealthportal')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();


			echo '<div style="background-color: #fff;" class="alert alert-info"><a target="_blank" href="https://git.vdm.dev/joomla/eHealth-Portal" title="eHealth Portal">
				<img src="components/com_ehealthportal/assets/images/vdm-component.jpg"/>
				</a></div>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the ehealthportal action logs extensions object.
			$ehealthportal_action_logs_extensions = new stdClass();
			$ehealthportal_action_logs_extensions->extension = 'com_ehealthportal';

			// Set the object into the action logs extensions table.
			$ehealthportal_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $ehealthportal_action_logs_extensions);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the payment action log config object.
			$payment_action_log_config = new stdClass();
			$payment_action_log_config->type_title = 'PAYMENT';
			$payment_action_log_config->type_alias = 'com_ehealthportal.payment';
			$payment_action_log_config->id_holder = 'id';
			$payment_action_log_config->title_holder = 'patient';
			$payment_action_log_config->table_name = '#__ehealthportal_payment';
			$payment_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$payment_Inserted = $db->insertObject('#__action_log_config', $payment_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the general_medical_check_up action log config object.
			$general_medical_check_up_action_log_config = new stdClass();
			$general_medical_check_up_action_log_config->type_title = 'GENERAL_MEDICAL_CHECK_UP';
			$general_medical_check_up_action_log_config->type_alias = 'com_ehealthportal.general_medical_check_up';
			$general_medical_check_up_action_log_config->id_holder = 'id';
			$general_medical_check_up_action_log_config->title_holder = 'patient';
			$general_medical_check_up_action_log_config->table_name = '#__ehealthportal_general_medical_check_up';
			$general_medical_check_up_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$general_medical_check_up_Inserted = $db->insertObject('#__action_log_config', $general_medical_check_up_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the antenatal_care action log config object.
			$antenatal_care_action_log_config = new stdClass();
			$antenatal_care_action_log_config->type_title = 'ANTENATAL_CARE';
			$antenatal_care_action_log_config->type_alias = 'com_ehealthportal.antenatal_care';
			$antenatal_care_action_log_config->id_holder = 'id';
			$antenatal_care_action_log_config->title_holder = 'patient';
			$antenatal_care_action_log_config->table_name = '#__ehealthportal_antenatal_care';
			$antenatal_care_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$antenatal_care_Inserted = $db->insertObject('#__action_log_config', $antenatal_care_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the immunisation action log config object.
			$immunisation_action_log_config = new stdClass();
			$immunisation_action_log_config->type_title = 'IMMUNISATION';
			$immunisation_action_log_config->type_alias = 'com_ehealthportal.immunisation';
			$immunisation_action_log_config->id_holder = 'id';
			$immunisation_action_log_config->title_holder = 'patient';
			$immunisation_action_log_config->table_name = '#__ehealthportal_immunisation';
			$immunisation_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$immunisation_Inserted = $db->insertObject('#__action_log_config', $immunisation_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the vmmc action log config object.
			$vmmc_action_log_config = new stdClass();
			$vmmc_action_log_config->type_title = 'VMMC';
			$vmmc_action_log_config->type_alias = 'com_ehealthportal.vmmc';
			$vmmc_action_log_config->id_holder = 'id';
			$vmmc_action_log_config->title_holder = 'patient';
			$vmmc_action_log_config->table_name = '#__ehealthportal_vmmc';
			$vmmc_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$vmmc_Inserted = $db->insertObject('#__action_log_config', $vmmc_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the prostate_and_testicular_cancer action log config object.
			$prostate_and_testicular_cancer_action_log_config = new stdClass();
			$prostate_and_testicular_cancer_action_log_config->type_title = 'PROSTATE_AND_TESTICULAR_CANCER';
			$prostate_and_testicular_cancer_action_log_config->type_alias = 'com_ehealthportal.prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer_action_log_config->id_holder = 'id';
			$prostate_and_testicular_cancer_action_log_config->title_holder = 'patient';
			$prostate_and_testicular_cancer_action_log_config->table_name = '#__ehealthportal_prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$prostate_and_testicular_cancer_Inserted = $db->insertObject('#__action_log_config', $prostate_and_testicular_cancer_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the tuberculosis action log config object.
			$tuberculosis_action_log_config = new stdClass();
			$tuberculosis_action_log_config->type_title = 'TUBERCULOSIS';
			$tuberculosis_action_log_config->type_alias = 'com_ehealthportal.tuberculosis';
			$tuberculosis_action_log_config->id_holder = 'id';
			$tuberculosis_action_log_config->title_holder = 'patient';
			$tuberculosis_action_log_config->table_name = '#__ehealthportal_tuberculosis';
			$tuberculosis_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$tuberculosis_Inserted = $db->insertObject('#__action_log_config', $tuberculosis_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the hiv_counseling_and_testing action log config object.
			$hiv_counseling_and_testing_action_log_config = new stdClass();
			$hiv_counseling_and_testing_action_log_config->type_title = 'HIV_COUNSELING_AND_TESTING';
			$hiv_counseling_and_testing_action_log_config->type_alias = 'com_ehealthportal.hiv_counseling_and_testing';
			$hiv_counseling_and_testing_action_log_config->id_holder = 'id';
			$hiv_counseling_and_testing_action_log_config->title_holder = 'patient';
			$hiv_counseling_and_testing_action_log_config->table_name = '#__ehealthportal_hiv_counseling_and_testing';
			$hiv_counseling_and_testing_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$hiv_counseling_and_testing_Inserted = $db->insertObject('#__action_log_config', $hiv_counseling_and_testing_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the family_planning action log config object.
			$family_planning_action_log_config = new stdClass();
			$family_planning_action_log_config->type_title = 'FAMILY_PLANNING';
			$family_planning_action_log_config->type_alias = 'com_ehealthportal.family_planning';
			$family_planning_action_log_config->id_holder = 'id';
			$family_planning_action_log_config->title_holder = 'diagnosis';
			$family_planning_action_log_config->table_name = '#__ehealthportal_family_planning';
			$family_planning_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$family_planning_Inserted = $db->insertObject('#__action_log_config', $family_planning_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the health_education action log config object.
			$health_education_action_log_config = new stdClass();
			$health_education_action_log_config->type_title = 'HEALTH_EDUCATION';
			$health_education_action_log_config->type_alias = 'com_ehealthportal.health_education';
			$health_education_action_log_config->id_holder = 'id';
			$health_education_action_log_config->title_holder = 'patient';
			$health_education_action_log_config->table_name = '#__ehealthportal_health_education';
			$health_education_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$health_education_Inserted = $db->insertObject('#__action_log_config', $health_education_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the cervical_cancer action log config object.
			$cervical_cancer_action_log_config = new stdClass();
			$cervical_cancer_action_log_config->type_title = 'CERVICAL_CANCER';
			$cervical_cancer_action_log_config->type_alias = 'com_ehealthportal.cervical_cancer';
			$cervical_cancer_action_log_config->id_holder = 'id';
			$cervical_cancer_action_log_config->title_holder = 'patient';
			$cervical_cancer_action_log_config->table_name = '#__ehealthportal_cervical_cancer';
			$cervical_cancer_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$cervical_cancer_Inserted = $db->insertObject('#__action_log_config', $cervical_cancer_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the breast_cancer action log config object.
			$breast_cancer_action_log_config = new stdClass();
			$breast_cancer_action_log_config->type_title = 'BREAST_CANCER';
			$breast_cancer_action_log_config->type_alias = 'com_ehealthportal.breast_cancer';
			$breast_cancer_action_log_config->id_holder = 'id';
			$breast_cancer_action_log_config->title_holder = 'patient';
			$breast_cancer_action_log_config->table_name = '#__ehealthportal_breast_cancer';
			$breast_cancer_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$breast_cancer_Inserted = $db->insertObject('#__action_log_config', $breast_cancer_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the test action log config object.
			$test_action_log_config = new stdClass();
			$test_action_log_config->type_title = 'TEST';
			$test_action_log_config->type_alias = 'com_ehealthportal.test';
			$test_action_log_config->id_holder = 'id';
			$test_action_log_config->title_holder = 'patient';
			$test_action_log_config->table_name = '#__ehealthportal_test';
			$test_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$test_Inserted = $db->insertObject('#__action_log_config', $test_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the foetal_lie action log config object.
			$foetal_lie_action_log_config = new stdClass();
			$foetal_lie_action_log_config->type_title = 'FOETAL_LIE';
			$foetal_lie_action_log_config->type_alias = 'com_ehealthportal.foetal_lie';
			$foetal_lie_action_log_config->id_holder = 'id';
			$foetal_lie_action_log_config->title_holder = 'name';
			$foetal_lie_action_log_config->table_name = '#__ehealthportal_foetal_lie';
			$foetal_lie_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$foetal_lie_Inserted = $db->insertObject('#__action_log_config', $foetal_lie_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the immunisation_vaccine_type action log config object.
			$immunisation_vaccine_type_action_log_config = new stdClass();
			$immunisation_vaccine_type_action_log_config->type_title = 'IMMUNISATION_VACCINE_TYPE';
			$immunisation_vaccine_type_action_log_config->type_alias = 'com_ehealthportal.immunisation_vaccine_type';
			$immunisation_vaccine_type_action_log_config->id_holder = 'id';
			$immunisation_vaccine_type_action_log_config->title_holder = 'name';
			$immunisation_vaccine_type_action_log_config->table_name = '#__ehealthportal_immunisation_vaccine_type';
			$immunisation_vaccine_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$immunisation_vaccine_type_Inserted = $db->insertObject('#__action_log_config', $immunisation_vaccine_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the foetal_engagement action log config object.
			$foetal_engagement_action_log_config = new stdClass();
			$foetal_engagement_action_log_config->type_title = 'FOETAL_ENGAGEMENT';
			$foetal_engagement_action_log_config->type_alias = 'com_ehealthportal.foetal_engagement';
			$foetal_engagement_action_log_config->id_holder = 'id';
			$foetal_engagement_action_log_config->title_holder = 'name';
			$foetal_engagement_action_log_config->table_name = '#__ehealthportal_foetal_engagement';
			$foetal_engagement_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$foetal_engagement_Inserted = $db->insertObject('#__action_log_config', $foetal_engagement_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the foetal_presentation action log config object.
			$foetal_presentation_action_log_config = new stdClass();
			$foetal_presentation_action_log_config->type_title = 'FOETAL_PRESENTATION';
			$foetal_presentation_action_log_config->type_alias = 'com_ehealthportal.foetal_presentation';
			$foetal_presentation_action_log_config->id_holder = 'id';
			$foetal_presentation_action_log_config->title_holder = 'name';
			$foetal_presentation_action_log_config->table_name = '#__ehealthportal_foetal_presentation';
			$foetal_presentation_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$foetal_presentation_Inserted = $db->insertObject('#__action_log_config', $foetal_presentation_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the testing_reason action log config object.
			$testing_reason_action_log_config = new stdClass();
			$testing_reason_action_log_config->type_title = 'TESTING_REASON';
			$testing_reason_action_log_config->type_alias = 'com_ehealthportal.testing_reason';
			$testing_reason_action_log_config->id_holder = 'id';
			$testing_reason_action_log_config->title_holder = 'name';
			$testing_reason_action_log_config->table_name = '#__ehealthportal_testing_reason';
			$testing_reason_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$testing_reason_Inserted = $db->insertObject('#__action_log_config', $testing_reason_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the counseling_type action log config object.
			$counseling_type_action_log_config = new stdClass();
			$counseling_type_action_log_config->type_title = 'COUNSELING_TYPE';
			$counseling_type_action_log_config->type_alias = 'com_ehealthportal.counseling_type';
			$counseling_type_action_log_config->id_holder = 'id';
			$counseling_type_action_log_config->title_holder = 'name';
			$counseling_type_action_log_config->table_name = '#__ehealthportal_counseling_type';
			$counseling_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$counseling_type_Inserted = $db->insertObject('#__action_log_config', $counseling_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the health_education_topic action log config object.
			$health_education_topic_action_log_config = new stdClass();
			$health_education_topic_action_log_config->type_title = 'HEALTH_EDUCATION_TOPIC';
			$health_education_topic_action_log_config->type_alias = 'com_ehealthportal.health_education_topic';
			$health_education_topic_action_log_config->id_holder = 'id';
			$health_education_topic_action_log_config->title_holder = 'name';
			$health_education_topic_action_log_config->table_name = '#__ehealthportal_health_education_topic';
			$health_education_topic_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$health_education_topic_Inserted = $db->insertObject('#__action_log_config', $health_education_topic_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the immunisation_type action log config object.
			$immunisation_type_action_log_config = new stdClass();
			$immunisation_type_action_log_config->type_title = 'IMMUNISATION_TYPE';
			$immunisation_type_action_log_config->type_alias = 'com_ehealthportal.immunisation_type';
			$immunisation_type_action_log_config->id_holder = 'id';
			$immunisation_type_action_log_config->title_holder = 'name';
			$immunisation_type_action_log_config->table_name = '#__ehealthportal_immunisation_type';
			$immunisation_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$immunisation_type_Inserted = $db->insertObject('#__action_log_config', $immunisation_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the strength action log config object.
			$strength_action_log_config = new stdClass();
			$strength_action_log_config->type_title = 'STRENGTH';
			$strength_action_log_config->type_alias = 'com_ehealthportal.strength';
			$strength_action_log_config->id_holder = 'id';
			$strength_action_log_config->title_holder = 'name';
			$strength_action_log_config->table_name = '#__ehealthportal_strength';
			$strength_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$strength_Inserted = $db->insertObject('#__action_log_config', $strength_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the referral action log config object.
			$referral_action_log_config = new stdClass();
			$referral_action_log_config->type_title = 'REFERRAL';
			$referral_action_log_config->type_alias = 'com_ehealthportal.referral';
			$referral_action_log_config->id_holder = 'id';
			$referral_action_log_config->title_holder = 'name';
			$referral_action_log_config->table_name = '#__ehealthportal_referral';
			$referral_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$referral_Inserted = $db->insertObject('#__action_log_config', $referral_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the planning_type action log config object.
			$planning_type_action_log_config = new stdClass();
			$planning_type_action_log_config->type_title = 'PLANNING_TYPE';
			$planning_type_action_log_config->type_alias = 'com_ehealthportal.planning_type';
			$planning_type_action_log_config->id_holder = 'id';
			$planning_type_action_log_config->title_holder = 'name';
			$planning_type_action_log_config->table_name = '#__ehealthportal_planning_type';
			$planning_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$planning_type_Inserted = $db->insertObject('#__action_log_config', $planning_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the diagnosis_type action log config object.
			$diagnosis_type_action_log_config = new stdClass();
			$diagnosis_type_action_log_config->type_title = 'DIAGNOSIS_TYPE';
			$diagnosis_type_action_log_config->type_alias = 'com_ehealthportal.diagnosis_type';
			$diagnosis_type_action_log_config->id_holder = 'id';
			$diagnosis_type_action_log_config->title_holder = 'name';
			$diagnosis_type_action_log_config->table_name = '#__ehealthportal_diagnosis_type';
			$diagnosis_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$diagnosis_type_Inserted = $db->insertObject('#__action_log_config', $diagnosis_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the nonpay_reason action log config object.
			$nonpay_reason_action_log_config = new stdClass();
			$nonpay_reason_action_log_config->type_title = 'NONPAY_REASON';
			$nonpay_reason_action_log_config->type_alias = 'com_ehealthportal.nonpay_reason';
			$nonpay_reason_action_log_config->id_holder = 'id';
			$nonpay_reason_action_log_config->title_holder = 'name';
			$nonpay_reason_action_log_config->table_name = '#__ehealthportal_nonpay_reason';
			$nonpay_reason_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$nonpay_reason_Inserted = $db->insertObject('#__action_log_config', $nonpay_reason_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the medication action log config object.
			$medication_action_log_config = new stdClass();
			$medication_action_log_config->type_title = 'MEDICATION';
			$medication_action_log_config->type_alias = 'com_ehealthportal.medication';
			$medication_action_log_config->id_holder = 'id';
			$medication_action_log_config->title_holder = 'name';
			$medication_action_log_config->table_name = '#__ehealthportal_medication';
			$medication_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$medication_Inserted = $db->insertObject('#__action_log_config', $medication_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the payment_type action log config object.
			$payment_type_action_log_config = new stdClass();
			$payment_type_action_log_config->type_title = 'PAYMENT_TYPE';
			$payment_type_action_log_config->type_alias = 'com_ehealthportal.payment_type';
			$payment_type_action_log_config->id_holder = 'id';
			$payment_type_action_log_config->title_holder = 'name';
			$payment_type_action_log_config->table_name = '#__ehealthportal_payment_type';
			$payment_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$payment_type_Inserted = $db->insertObject('#__action_log_config', $payment_type_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the administration_part action log config object.
			$administration_part_action_log_config = new stdClass();
			$administration_part_action_log_config->type_title = 'ADMINISTRATION_PART';
			$administration_part_action_log_config->type_alias = 'com_ehealthportal.administration_part';
			$administration_part_action_log_config->id_holder = 'id';
			$administration_part_action_log_config->title_holder = 'name';
			$administration_part_action_log_config->table_name = '#__ehealthportal_administration_part';
			$administration_part_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$administration_part_Inserted = $db->insertObject('#__action_log_config', $administration_part_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the site action log config object.
			$site_action_log_config = new stdClass();
			$site_action_log_config->type_title = 'SITE';
			$site_action_log_config->type_alias = 'com_ehealthportal.site';
			$site_action_log_config->id_holder = 'id';
			$site_action_log_config->title_holder = 'site_name';
			$site_action_log_config->table_name = '#__ehealthportal_site';
			$site_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$site_Inserted = $db->insertObject('#__action_log_config', $site_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the unit action log config object.
			$unit_action_log_config = new stdClass();
			$unit_action_log_config->type_title = 'UNIT';
			$unit_action_log_config->type_alias = 'com_ehealthportal.unit';
			$unit_action_log_config->id_holder = 'id';
			$unit_action_log_config->title_holder = 'name';
			$unit_action_log_config->table_name = '#__ehealthportal_unit';
			$unit_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$unit_Inserted = $db->insertObject('#__action_log_config', $unit_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the clinic action log config object.
			$clinic_action_log_config = new stdClass();
			$clinic_action_log_config->type_title = 'CLINIC';
			$clinic_action_log_config->type_alias = 'com_ehealthportal.clinic';
			$clinic_action_log_config->id_holder = 'id';
			$clinic_action_log_config->title_holder = 'clinic_name';
			$clinic_action_log_config->table_name = '#__ehealthportal_clinic';
			$clinic_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Set the object into the action log config table.
			$clinic_Inserted = $db->insertObject('#__action_log_config', $clinic_action_log_config);
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = Factory::getDbo();

			// Create the payment content type object.
			$payment = new stdClass();
			$payment->type_title = 'Ehealthportal Payment';
			$payment->type_alias = 'com_ehealthportal.payment';
			$payment->table = '{"special": {"dbtable": "#__ehealthportal_payment","key": "id","type": "Payment","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","payment_category":"payment_category","payment_type":"payment_type","payment_amount":"payment_amount","nonpay_reason":"nonpay_reason","receipt_no":"receipt_no","guid":"guid"}}';
			$payment->router = 'EhealthportalHelperRoute::getPaymentRoute';
			$payment->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","payment_type","nonpay_reason"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "payment_type","targetTable": "#__ehealthportal_payment_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "nonpay_reason","targetTable": "#__ehealthportal_nonpay_reason","targetColumn": "id","displayColumn": "name"}]}';

			// Check if payment type is already in content_type DB.
			$payment_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment->type_id = $db->loadResult();
				$payment_Updated = $db->updateObject('#__content_types', $payment, 'type_id');
			}
			else
			{
				$payment_Inserted = $db->insertObject('#__content_types', $payment);
			}

			// Create the general_medical_check_up content type object.
			$general_medical_check_up = new stdClass();
			$general_medical_check_up->type_title = 'Ehealthportal General_medical_check_up';
			$general_medical_check_up->type_alias = 'com_ehealthportal.general_medical_check_up';
			$general_medical_check_up->table = '{"special": {"dbtable": "#__ehealthportal_general_medical_check_up","key": "id","type": "General_medical_check_up","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$general_medical_check_up->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bp_diastolic_one":"bp_diastolic_one","bp_systolic_one":"bp_systolic_one","temp_one":"temp_one","weight":"weight","pulse":"pulse","chronic_medication":"chronic_medication","bp_diastolic_two":"bp_diastolic_two","bp_systolic_two":"bp_systolic_two","temp_two":"temp_two","height":"height","bmi":"bmi","complaint":"complaint","investigations":"investigations","notes":"notes","diagnosis":"diagnosis","referral":"referral","reason":"reason","guid":"guid"}}';
			$general_medical_check_up->router = 'EhealthportalHelperRoute::getGeneral_medical_check_upRoute';
			$general_medical_check_up->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/general_medical_check_up.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bp_diastolic_one","bp_systolic_one","pulse","bp_diastolic_two","bp_systolic_two","diagnosis","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_diagnosis_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if general_medical_check_up type is already in content_type DB.
			$general_medical_check_up_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($general_medical_check_up->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$general_medical_check_up->type_id = $db->loadResult();
				$general_medical_check_up_Updated = $db->updateObject('#__content_types', $general_medical_check_up, 'type_id');
			}
			else
			{
				$general_medical_check_up_Inserted = $db->insertObject('#__content_types', $general_medical_check_up);
			}

			// Create the antenatal_care content type object.
			$antenatal_care = new stdClass();
			$antenatal_care->type_title = 'Ehealthportal Antenatal_care';
			$antenatal_care->type_alias = 'com_ehealthportal.antenatal_care';
			$antenatal_care->table = '{"special": {"dbtable": "#__ehealthportal_antenatal_care","key": "id","type": "Antenatal_care","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$antenatal_care->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","foetal_lie":"foetal_lie","foetal_presentation":"foetal_presentation","foetal_engagement":"foetal_engagement","foetal_heart_rate":"foetal_heart_rate","foetal_movements":"foetal_movements","guid":"guid","caesarean_sections":"caesarean_sections","last_menstrual_period":"last_menstrual_period","normal_births":"normal_births","still_births":"still_births","miscarriages":"miscarriages","live_births":"live_births","pregnancies_excl":"pregnancies_excl"}}';
			$antenatal_care->router = 'EhealthportalHelperRoute::getAntenatal_careRoute';
			$antenatal_care->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/antenatal_care.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","foetal_lie","foetal_presentation","foetal_engagement","foetal_heart_rate","caesarean_sections","normal_births","still_births","miscarriages","live_births","pregnancies_excl"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_lie","targetTable": "#__ehealthportal_foetal_lie","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_presentation","targetTable": "#__ehealthportal_foetal_presentation","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_engagement","targetTable": "#__ehealthportal_foetal_engagement","targetColumn": "id","displayColumn": "name"}]}';

			// Check if antenatal_care type is already in content_type DB.
			$antenatal_care_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($antenatal_care->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$antenatal_care->type_id = $db->loadResult();
				$antenatal_care_Updated = $db->updateObject('#__content_types', $antenatal_care, 'type_id');
			}
			else
			{
				$antenatal_care_Inserted = $db->insertObject('#__content_types', $antenatal_care);
			}

			// Create the immunisation content type object.
			$immunisation = new stdClass();
			$immunisation->type_title = 'Ehealthportal Immunisation';
			$immunisation->type_alias = 'com_ehealthportal.immunisation';
			$immunisation->table = '{"special": {"dbtable": "#__ehealthportal_immunisation","key": "id","type": "Immunisation","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","referral":"referral","immunisation_up_to_date":"immunisation_up_to_date","reason":"reason","guid":"guid"}}';
			$immunisation->router = 'EhealthportalHelperRoute::getImmunisationRoute';
			$immunisation->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/immunisation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if immunisation type is already in content_type DB.
			$immunisation_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation->type_id = $db->loadResult();
				$immunisation_Updated = $db->updateObject('#__content_types', $immunisation, 'type_id');
			}
			else
			{
				$immunisation_Inserted = $db->insertObject('#__content_types', $immunisation);
			}

			// Create the vmmc content type object.
			$vmmc = new stdClass();
			$vmmc->type_title = 'Ehealthportal Vmmc';
			$vmmc->type_alias = 'com_ehealthportal.vmmc';
			$vmmc->table = '{"special": {"dbtable": "#__ehealthportal_vmmc","key": "id","type": "Vmmc","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$vmmc->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","are_you_circumcised":"are_you_circumcised","info_ben_vmcc":"info_ben_vmcc","interested_in_vmmc":"interested_in_vmmc","vmmc_gender":"vmmc_gender","reason":"reason","referral":"referral","guid":"guid","partner_circumcised":"partner_circumcised"}}';
			$vmmc->router = 'EhealthportalHelperRoute::getVmmcRoute';
			$vmmc->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/vmmc.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if vmmc type is already in content_type DB.
			$vmmc_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($vmmc->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$vmmc->type_id = $db->loadResult();
				$vmmc_Updated = $db->updateObject('#__content_types', $vmmc, 'type_id');
			}
			else
			{
				$vmmc_Inserted = $db->insertObject('#__content_types', $vmmc);
			}

			// Create the prostate_and_testicular_cancer content type object.
			$prostate_and_testicular_cancer = new stdClass();
			$prostate_and_testicular_cancer->type_title = 'Ehealthportal Prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->type_alias = 'com_ehealthportal.prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->table = '{"special": {"dbtable": "#__ehealthportal_prostate_and_testicular_cancer","key": "id","type": "Prostate_and_testicular_cancer","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$prostate_and_testicular_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","ptc_age":"ptc_age","ptc_fam_history":"ptc_fam_history","ptc_diet":"ptc_diet","ptc_phy_activity":"ptc_phy_activity","ptc_overweight":"ptc_overweight","ptc_urinate":"ptc_urinate","ptc_urine_freq":"ptc_urine_freq","referral":"referral","reason":"reason","txt_ptc_urine_freq":"txt_ptc_urine_freq","txt_ptc_urinate":"txt_ptc_urinate","txt_ptc_age":"txt_ptc_age","txt_ptc_phy_activity":"txt_ptc_phy_activity","txt_ptc_diet":"txt_ptc_diet","txt_ptc_fam_history":"txt_ptc_fam_history","guid":"guid","txt_ptc_overweight":"txt_ptc_overweight"}}';
			$prostate_and_testicular_cancer->router = 'EhealthportalHelperRoute::getProstate_and_testicular_cancerRoute';
			$prostate_and_testicular_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/prostate_and_testicular_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if prostate_and_testicular_cancer type is already in content_type DB.
			$prostate_and_testicular_cancer_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($prostate_and_testicular_cancer->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$prostate_and_testicular_cancer->type_id = $db->loadResult();
				$prostate_and_testicular_cancer_Updated = $db->updateObject('#__content_types', $prostate_and_testicular_cancer, 'type_id');
			}
			else
			{
				$prostate_and_testicular_cancer_Inserted = $db->insertObject('#__content_types', $prostate_and_testicular_cancer);
			}

			// Create the tuberculosis content type object.
			$tuberculosis = new stdClass();
			$tuberculosis->type_title = 'Ehealthportal Tuberculosis';
			$tuberculosis->type_alias = 'com_ehealthportal.tuberculosis';
			$tuberculosis->table = '{"special": {"dbtable": "#__ehealthportal_tuberculosis","key": "id","type": "Tuberculosis","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tuberculosis->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","recurring_night_sweats":"recurring_night_sweats","tb_fever":"tb_fever","persistent_cough":"persistent_cough","blood_streaked_sputum":"blood_streaked_sputum","unusual_tiredness":"unusual_tiredness","pain_in_chest":"pain_in_chest","shortness_of_breath":"shortness_of_breath","diagnosed_with_disease":"diagnosed_with_disease","tb_exposed":"tb_exposed","tb_treatment":"tb_treatment","date_of_treatment":"date_of_treatment","treating_dhc":"treating_dhc","sputum_collection_one":"sputum_collection_one","tb_reason_one":"tb_reason_one","sputum_result_one":"sputum_result_one","referred_second_sputum":"referred_second_sputum","tb_reason_two":"tb_reason_two","sputum_result_two":"sputum_result_two","weight_loss_wdieting":"weight_loss_wdieting","reason":"reason","guid":"guid","referral":"referral"}}';
			$tuberculosis->router = 'EhealthportalHelperRoute::getTuberculosisRoute';
			$tuberculosis->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/tuberculosis.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if tuberculosis type is already in content_type DB.
			$tuberculosis_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($tuberculosis->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$tuberculosis->type_id = $db->loadResult();
				$tuberculosis_Updated = $db->updateObject('#__content_types', $tuberculosis, 'type_id');
			}
			else
			{
				$tuberculosis_Inserted = $db->insertObject('#__content_types', $tuberculosis);
			}

			// Create the hiv_counseling_and_testing content type object.
			$hiv_counseling_and_testing = new stdClass();
			$hiv_counseling_and_testing->type_title = 'Ehealthportal Hiv_counseling_and_testing';
			$hiv_counseling_and_testing->type_alias = 'com_ehealthportal.hiv_counseling_and_testing';
			$hiv_counseling_and_testing->table = '{"special": {"dbtable": "#__ehealthportal_hiv_counseling_and_testing","key": "id","type": "Hiv_counseling_and_testing","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$hiv_counseling_and_testing->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","counseling_type":"counseling_type","testing_reason":"testing_reason","last_test_date":"last_test_date","prev_test_result":"prev_test_result","test_result_one":"test_result_one","test_result_two":"test_result_two","final_test_result":"final_test_result","eqa":"eqa","referral":"referral","reason":"reason","guid":"guid"}}';
			$hiv_counseling_and_testing->router = 'EhealthportalHelperRoute::getHiv_counseling_and_testingRoute';
			$hiv_counseling_and_testing->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/hiv_counseling_and_testing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","testing_reason","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "testing_reason","targetTable": "#__ehealthportal_testing_reason","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if hiv_counseling_and_testing type is already in content_type DB.
			$hiv_counseling_and_testing_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($hiv_counseling_and_testing->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$hiv_counseling_and_testing->type_id = $db->loadResult();
				$hiv_counseling_and_testing_Updated = $db->updateObject('#__content_types', $hiv_counseling_and_testing, 'type_id');
			}
			else
			{
				$hiv_counseling_and_testing_Inserted = $db->insertObject('#__content_types', $hiv_counseling_and_testing);
			}

			// Create the family_planning content type object.
			$family_planning = new stdClass();
			$family_planning->type_title = 'Ehealthportal Family_planning';
			$family_planning->type_alias = 'com_ehealthportal.family_planning';
			$family_planning->table = '{"special": {"dbtable": "#__ehealthportal_family_planning","key": "id","type": "Family_planning","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$family_planning->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "diagnosis","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","diagnosis":"diagnosis","guid":"guid"}}';
			$family_planning->router = 'EhealthportalHelperRoute::getFamily_planningRoute';
			$family_planning->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/family_planning.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","diagnosis"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_planning_type","targetColumn": "id","displayColumn": "name"}]}';

			// Check if family_planning type is already in content_type DB.
			$family_planning_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($family_planning->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$family_planning->type_id = $db->loadResult();
				$family_planning_Updated = $db->updateObject('#__content_types', $family_planning, 'type_id');
			}
			else
			{
				$family_planning_Inserted = $db->insertObject('#__content_types', $family_planning);
			}

			// Create the health_education content type object.
			$health_education = new stdClass();
			$health_education->type_title = 'Ehealthportal Health_education';
			$health_education->type_alias = 'com_ehealthportal.health_education';
			$health_education->table = '{"special": {"dbtable": "#__ehealthportal_health_education","key": "id","type": "Health_education","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"education_type":"education_type","health_education_topic":"health_education_topic","patient":"patient","guid":"guid"}}';
			$health_education->router = 'EhealthportalHelperRoute::getHealth_educationRoute';
			$health_education->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/health_education.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","health_education_topic"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "health_education_topic","targetTable": "#__ehealthportal_health_education_topic","targetColumn": "id","displayColumn": "name"}]}';

			// Check if health_education type is already in content_type DB.
			$health_education_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($health_education->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$health_education->type_id = $db->loadResult();
				$health_education_Updated = $db->updateObject('#__content_types', $health_education, 'type_id');
			}
			else
			{
				$health_education_Inserted = $db->insertObject('#__content_types', $health_education);
			}

			// Create the cervical_cancer content type object.
			$cervical_cancer = new stdClass();
			$cervical_cancer->type_title = 'Ehealthportal Cervical_cancer';
			$cervical_cancer->type_alias = 'com_ehealthportal.cervical_cancer';
			$cervical_cancer->table = '{"special": {"dbtable": "#__ehealthportal_cervical_cancer","key": "id","type": "Cervical_cancer","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$cervical_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","cc_viginal_bleeding":"cc_viginal_bleeding","cc_v_discharge":"cc_v_discharge","cc_periods":"cc_periods","cc_smoking":"cc_smoking","cc_sex_actve":"cc_sex_actve","cc_sex_partner":"cc_sex_partner","pap_smear_collection":"pap_smear_collection","cc_result":"cc_result","referral":"referral","reason":"reason","cc_reason":"cc_reason","txt_cc_v_discharge":"txt_cc_v_discharge","txt_cc_sex_partner":"txt_cc_sex_partner","txt_cc_viginal_bleeding":"txt_cc_viginal_bleeding","txt_cc_smoking":"txt_cc_smoking","txt_cc_periods":"txt_cc_periods","guid":"guid","txt_cc_sex_actve":"txt_cc_sex_actve"}}';
			$cervical_cancer->router = 'EhealthportalHelperRoute::getCervical_cancerRoute';
			$cervical_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/cervical_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if cervical_cancer type is already in content_type DB.
			$cervical_cancer_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($cervical_cancer->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$cervical_cancer->type_id = $db->loadResult();
				$cervical_cancer_Updated = $db->updateObject('#__content_types', $cervical_cancer, 'type_id');
			}
			else
			{
				$cervical_cancer_Inserted = $db->insertObject('#__content_types', $cervical_cancer);
			}

			// Create the breast_cancer content type object.
			$breast_cancer = new stdClass();
			$breast_cancer->type_title = 'Ehealthportal Breast_cancer';
			$breast_cancer->type_alias = 'com_ehealthportal.breast_cancer';
			$breast_cancer->table = '{"special": {"dbtable": "#__ehealthportal_breast_cancer","key": "id","type": "Breast_cancer","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$breast_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bc_age_range":"bc_age_range","bc_family_history":"bc_family_history","bc_race":"bc_race","bc_breastfeeding":"bc_breastfeeding","bc_preg_freq":"bc_preg_freq","bc_preg_age":"bc_preg_age","bc_history_hrt":"bc_history_hrt","bc_reg_exercise":"bc_reg_exercise","bc_overweight":"bc_overweight","bc_lump_near_breast":"bc_lump_near_breast","bc_dimpling":"bc_dimpling","bc_inward_nipple":"bc_inward_nipple","bc_nipple_discharge":"bc_nipple_discharge","bc_abnormal_skin":"bc_abnormal_skin","bc_breast_shape":"bc_breast_shape","referral":"referral","reason":"reason","guid":"guid","txt_bc_breast_shape":"txt_bc_breast_shape","txt_bc_abnormal_skin":"txt_bc_abnormal_skin","txt_bc_nipple_discharge":"txt_bc_nipple_discharge","txt_bc_dimpling":"txt_bc_dimpling","txt_bc_lump_near_breast":"txt_bc_lump_near_breast","txt_bc_inward_nipple":"txt_bc_inward_nipple"}}';
			$breast_cancer->router = 'EhealthportalHelperRoute::getBreast_cancerRoute';
			$breast_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/breast_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bc_preg_freq","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if breast_cancer type is already in content_type DB.
			$breast_cancer_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($breast_cancer->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$breast_cancer->type_id = $db->loadResult();
				$breast_cancer_Updated = $db->updateObject('#__content_types', $breast_cancer, 'type_id');
			}
			else
			{
				$breast_cancer_Inserted = $db->insertObject('#__content_types', $breast_cancer);
			}

			// Create the test content type object.
			$test = new stdClass();
			$test->type_title = 'Ehealthportal Test';
			$test->type_alias = 'com_ehealthportal.test';
			$test->table = '{"special": {"dbtable": "#__ehealthportal_test","key": "id","type": "Test","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$test->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","urine_test_result":"urine_test_result","glucose_first_reading":"glucose_first_reading","glucose_second_reading":"glucose_second_reading","haemoglobin_reading":"haemoglobin_reading","cholesterol_reading":"cholesterol_reading","syphilis_first_reading":"syphilis_first_reading","syphilis_second_reading":"syphilis_second_reading","hepatitis_first_reading":"hepatitis_first_reading","hepatitis_second_reading":"hepatitis_second_reading","malaria_first_reading":"malaria_first_reading","malaria_second_reading":"malaria_second_reading","pregnancy_first_reading":"pregnancy_first_reading","pregnancy_second_reading":"pregnancy_second_reading","referral":"referral","reason":"reason","guid":"guid"}}';
			$test->router = 'EhealthportalHelperRoute::getTestRoute';
			$test->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/test.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","glucose_first_reading","glucose_second_reading","haemoglobin_reading","cholesterol_reading","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if test type is already in content_type DB.
			$test_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($test->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$test->type_id = $db->loadResult();
				$test_Updated = $db->updateObject('#__content_types', $test, 'type_id');
			}
			else
			{
				$test_Inserted = $db->insertObject('#__content_types', $test);
			}

			// Create the foetal_lie content type object.
			$foetal_lie = new stdClass();
			$foetal_lie->type_title = 'Ehealthportal Foetal_lie';
			$foetal_lie->type_alias = 'com_ehealthportal.foetal_lie';
			$foetal_lie->table = '{"special": {"dbtable": "#__ehealthportal_foetal_lie","key": "id","type": "Foetal_lie","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_lie->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$foetal_lie->router = 'EhealthportalHelperRoute::getFoetal_lieRoute';
			$foetal_lie->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/foetal_lie.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if foetal_lie type is already in content_type DB.
			$foetal_lie_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_lie->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_lie->type_id = $db->loadResult();
				$foetal_lie_Updated = $db->updateObject('#__content_types', $foetal_lie, 'type_id');
			}
			else
			{
				$foetal_lie_Inserted = $db->insertObject('#__content_types', $foetal_lie);
			}

			// Create the immunisation_vaccine_type content type object.
			$immunisation_vaccine_type = new stdClass();
			$immunisation_vaccine_type->type_title = 'Ehealthportal Immunisation_vaccine_type';
			$immunisation_vaccine_type->type_alias = 'com_ehealthportal.immunisation_vaccine_type';
			$immunisation_vaccine_type->table = '{"special": {"dbtable": "#__ehealthportal_immunisation_vaccine_type","key": "id","type": "Immunisation_vaccine_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_vaccine_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","administration_part":"administration_part","description":"description","guid":"guid","alias":"alias"}}';
			$immunisation_vaccine_type->router = 'EhealthportalHelperRoute::getImmunisation_vaccine_typeRoute';
			$immunisation_vaccine_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/immunisation_vaccine_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealthportal_administration_part","targetColumn": "id","displayColumn": "name"}]}';

			// Check if immunisation_vaccine_type type is already in content_type DB.
			$immunisation_vaccine_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation_vaccine_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation_vaccine_type->type_id = $db->loadResult();
				$immunisation_vaccine_type_Updated = $db->updateObject('#__content_types', $immunisation_vaccine_type, 'type_id');
			}
			else
			{
				$immunisation_vaccine_type_Inserted = $db->insertObject('#__content_types', $immunisation_vaccine_type);
			}

			// Create the foetal_engagement content type object.
			$foetal_engagement = new stdClass();
			$foetal_engagement->type_title = 'Ehealthportal Foetal_engagement';
			$foetal_engagement->type_alias = 'com_ehealthportal.foetal_engagement';
			$foetal_engagement->table = '{"special": {"dbtable": "#__ehealthportal_foetal_engagement","key": "id","type": "Foetal_engagement","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_engagement->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$foetal_engagement->router = 'EhealthportalHelperRoute::getFoetal_engagementRoute';
			$foetal_engagement->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/foetal_engagement.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if foetal_engagement type is already in content_type DB.
			$foetal_engagement_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_engagement->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_engagement->type_id = $db->loadResult();
				$foetal_engagement_Updated = $db->updateObject('#__content_types', $foetal_engagement, 'type_id');
			}
			else
			{
				$foetal_engagement_Inserted = $db->insertObject('#__content_types', $foetal_engagement);
			}

			// Create the foetal_presentation content type object.
			$foetal_presentation = new stdClass();
			$foetal_presentation->type_title = 'Ehealthportal Foetal_presentation';
			$foetal_presentation->type_alias = 'com_ehealthportal.foetal_presentation';
			$foetal_presentation->table = '{"special": {"dbtable": "#__ehealthportal_foetal_presentation","key": "id","type": "Foetal_presentation","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_presentation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$foetal_presentation->router = 'EhealthportalHelperRoute::getFoetal_presentationRoute';
			$foetal_presentation->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/foetal_presentation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if foetal_presentation type is already in content_type DB.
			$foetal_presentation_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_presentation->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_presentation->type_id = $db->loadResult();
				$foetal_presentation_Updated = $db->updateObject('#__content_types', $foetal_presentation, 'type_id');
			}
			else
			{
				$foetal_presentation_Inserted = $db->insertObject('#__content_types', $foetal_presentation);
			}

			// Create the testing_reason content type object.
			$testing_reason = new stdClass();
			$testing_reason->type_title = 'Ehealthportal Testing_reason';
			$testing_reason->type_alias = 'com_ehealthportal.testing_reason';
			$testing_reason->table = '{"special": {"dbtable": "#__ehealthportal_testing_reason","key": "id","type": "Testing_reason","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$testing_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$testing_reason->router = 'EhealthportalHelperRoute::getTesting_reasonRoute';
			$testing_reason->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/testing_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if testing_reason type is already in content_type DB.
			$testing_reason_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($testing_reason->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$testing_reason->type_id = $db->loadResult();
				$testing_reason_Updated = $db->updateObject('#__content_types', $testing_reason, 'type_id');
			}
			else
			{
				$testing_reason_Inserted = $db->insertObject('#__content_types', $testing_reason);
			}

			// Create the counseling_type content type object.
			$counseling_type = new stdClass();
			$counseling_type->type_title = 'Ehealthportal Counseling_type';
			$counseling_type->type_alias = 'com_ehealthportal.counseling_type';
			$counseling_type->table = '{"special": {"dbtable": "#__ehealthportal_counseling_type","key": "id","type": "Counseling_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$counseling_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$counseling_type->router = 'EhealthportalHelperRoute::getCounseling_typeRoute';
			$counseling_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/counseling_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if counseling_type type is already in content_type DB.
			$counseling_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($counseling_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$counseling_type->type_id = $db->loadResult();
				$counseling_type_Updated = $db->updateObject('#__content_types', $counseling_type, 'type_id');
			}
			else
			{
				$counseling_type_Inserted = $db->insertObject('#__content_types', $counseling_type);
			}

			// Create the health_education_topic content type object.
			$health_education_topic = new stdClass();
			$health_education_topic->type_title = 'Ehealthportal Health_education_topic';
			$health_education_topic->type_alias = 'com_ehealthportal.health_education_topic';
			$health_education_topic->table = '{"special": {"dbtable": "#__ehealthportal_health_education_topic","key": "id","type": "Health_education_topic","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education_topic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$health_education_topic->router = 'EhealthportalHelperRoute::getHealth_education_topicRoute';
			$health_education_topic->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/health_education_topic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if health_education_topic type is already in content_type DB.
			$health_education_topic_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($health_education_topic->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$health_education_topic->type_id = $db->loadResult();
				$health_education_topic_Updated = $db->updateObject('#__content_types', $health_education_topic, 'type_id');
			}
			else
			{
				$health_education_topic_Inserted = $db->insertObject('#__content_types', $health_education_topic);
			}

			// Create the immunisation_type content type object.
			$immunisation_type = new stdClass();
			$immunisation_type->type_title = 'Ehealthportal Immunisation_type';
			$immunisation_type->type_alias = 'com_ehealthportal.immunisation_type';
			$immunisation_type->table = '{"special": {"dbtable": "#__ehealthportal_immunisation_type","key": "id","type": "Immunisation_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$immunisation_type->router = 'EhealthportalHelperRoute::getImmunisation_typeRoute';
			$immunisation_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/immunisation_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if immunisation_type type is already in content_type DB.
			$immunisation_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation_type->type_id = $db->loadResult();
				$immunisation_type_Updated = $db->updateObject('#__content_types', $immunisation_type, 'type_id');
			}
			else
			{
				$immunisation_type_Inserted = $db->insertObject('#__content_types', $immunisation_type);
			}

			// Create the strength content type object.
			$strength = new stdClass();
			$strength->type_title = 'Ehealthportal Strength';
			$strength->type_alias = 'com_ehealthportal.strength';
			$strength->table = '{"special": {"dbtable": "#__ehealthportal_strength","key": "id","type": "Strength","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$strength->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$strength->router = 'EhealthportalHelperRoute::getStrengthRoute';
			$strength->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/strength.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if strength type is already in content_type DB.
			$strength_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($strength->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$strength->type_id = $db->loadResult();
				$strength_Updated = $db->updateObject('#__content_types', $strength, 'type_id');
			}
			else
			{
				$strength_Inserted = $db->insertObject('#__content_types', $strength);
			}

			// Create the referral content type object.
			$referral = new stdClass();
			$referral->type_title = 'Ehealthportal Referral';
			$referral->type_alias = 'com_ehealthportal.referral';
			$referral->table = '{"special": {"dbtable": "#__ehealthportal_referral","key": "id","type": "Referral","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$referral->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$referral->router = 'EhealthportalHelperRoute::getReferralRoute';
			$referral->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/referral.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if referral type is already in content_type DB.
			$referral_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($referral->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$referral->type_id = $db->loadResult();
				$referral_Updated = $db->updateObject('#__content_types', $referral, 'type_id');
			}
			else
			{
				$referral_Inserted = $db->insertObject('#__content_types', $referral);
			}

			// Create the planning_type content type object.
			$planning_type = new stdClass();
			$planning_type->type_title = 'Ehealthportal Planning_type';
			$planning_type->type_alias = 'com_ehealthportal.planning_type';
			$planning_type->table = '{"special": {"dbtable": "#__ehealthportal_planning_type","key": "id","type": "Planning_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$planning_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$planning_type->router = 'EhealthportalHelperRoute::getPlanning_typeRoute';
			$planning_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/planning_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if planning_type type is already in content_type DB.
			$planning_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($planning_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$planning_type->type_id = $db->loadResult();
				$planning_type_Updated = $db->updateObject('#__content_types', $planning_type, 'type_id');
			}
			else
			{
				$planning_type_Inserted = $db->insertObject('#__content_types', $planning_type);
			}

			// Create the diagnosis_type content type object.
			$diagnosis_type = new stdClass();
			$diagnosis_type->type_title = 'Ehealthportal Diagnosis_type';
			$diagnosis_type->type_alias = 'com_ehealthportal.diagnosis_type';
			$diagnosis_type->table = '{"special": {"dbtable": "#__ehealthportal_diagnosis_type","key": "id","type": "Diagnosis_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$diagnosis_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$diagnosis_type->router = 'EhealthportalHelperRoute::getDiagnosis_typeRoute';
			$diagnosis_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/diagnosis_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if diagnosis_type type is already in content_type DB.
			$diagnosis_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($diagnosis_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$diagnosis_type->type_id = $db->loadResult();
				$diagnosis_type_Updated = $db->updateObject('#__content_types', $diagnosis_type, 'type_id');
			}
			else
			{
				$diagnosis_type_Inserted = $db->insertObject('#__content_types', $diagnosis_type);
			}

			// Create the nonpay_reason content type object.
			$nonpay_reason = new stdClass();
			$nonpay_reason->type_title = 'Ehealthportal Nonpay_reason';
			$nonpay_reason->type_alias = 'com_ehealthportal.nonpay_reason';
			$nonpay_reason->table = '{"special": {"dbtable": "#__ehealthportal_nonpay_reason","key": "id","type": "Nonpay_reason","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$nonpay_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$nonpay_reason->router = 'EhealthportalHelperRoute::getNonpay_reasonRoute';
			$nonpay_reason->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/nonpay_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if nonpay_reason type is already in content_type DB.
			$nonpay_reason_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($nonpay_reason->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$nonpay_reason->type_id = $db->loadResult();
				$nonpay_reason_Updated = $db->updateObject('#__content_types', $nonpay_reason, 'type_id');
			}
			else
			{
				$nonpay_reason_Inserted = $db->insertObject('#__content_types', $nonpay_reason);
			}

			// Create the medication content type object.
			$medication = new stdClass();
			$medication->type_title = 'Ehealthportal Medication';
			$medication->type_alias = 'com_ehealthportal.medication';
			$medication->table = '{"special": {"dbtable": "#__ehealthportal_medication","key": "id","type": "Medication","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$medication->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$medication->router = 'EhealthportalHelperRoute::getMedicationRoute';
			$medication->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/medication.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if medication type is already in content_type DB.
			$medication_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($medication->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$medication->type_id = $db->loadResult();
				$medication_Updated = $db->updateObject('#__content_types', $medication, 'type_id');
			}
			else
			{
				$medication_Inserted = $db->insertObject('#__content_types', $medication);
			}

			// Create the payment_type content type object.
			$payment_type = new stdClass();
			$payment_type->type_title = 'Ehealthportal Payment_type';
			$payment_type->type_alias = 'com_ehealthportal.payment_type';
			$payment_type->table = '{"special": {"dbtable": "#__ehealthportal_payment_type","key": "id","type": "Payment_type","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$payment_type->router = 'EhealthportalHelperRoute::getPayment_typeRoute';
			$payment_type->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/payment_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if payment_type type is already in content_type DB.
			$payment_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment_type->type_id = $db->loadResult();
				$payment_type_Updated = $db->updateObject('#__content_types', $payment_type, 'type_id');
			}
			else
			{
				$payment_type_Inserted = $db->insertObject('#__content_types', $payment_type);
			}

			// Create the administration_part content type object.
			$administration_part = new stdClass();
			$administration_part->type_title = 'Ehealthportal Administration_part';
			$administration_part->type_alias = 'com_ehealthportal.administration_part';
			$administration_part->table = '{"special": {"dbtable": "#__ehealthportal_administration_part","key": "id","type": "Administration_part","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$administration_part->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$administration_part->router = 'EhealthportalHelperRoute::getAdministration_partRoute';
			$administration_part->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/administration_part.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if administration_part type is already in content_type DB.
			$administration_part_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($administration_part->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$administration_part->type_id = $db->loadResult();
				$administration_part_Updated = $db->updateObject('#__content_types', $administration_part, 'type_id');
			}
			else
			{
				$administration_part_Inserted = $db->insertObject('#__content_types', $administration_part);
			}

			// Create the site content type object.
			$site = new stdClass();
			$site->type_title = 'Ehealthportal Site';
			$site->type_alias = 'com_ehealthportal.site';
			$site->table = '{"special": {"dbtable": "#__ehealthportal_site","key": "id","type": "Site","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$site->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "site_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"site_name":"site_name","description":"description","site_region":"site_region","guid":"guid","alias":"alias"}}';
			$site->router = 'EhealthportalHelperRoute::getSiteRoute';
			$site->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/site.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if site type is already in content_type DB.
			$site_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($site->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$site->type_id = $db->loadResult();
				$site_Updated = $db->updateObject('#__content_types', $site, 'type_id');
			}
			else
			{
				$site_Inserted = $db->insertObject('#__content_types', $site);
			}

			// Create the unit content type object.
			$unit = new stdClass();
			$unit->type_title = 'Ehealthportal Unit';
			$unit->type_alias = 'com_ehealthportal.unit';
			$unit->table = '{"special": {"dbtable": "#__ehealthportal_unit","key": "id","type": "Unit","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$unit->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}';
			$unit->router = 'EhealthportalHelperRoute::getUnitRoute';
			$unit->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/unit.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if unit type is already in content_type DB.
			$unit_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($unit->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$unit->type_id = $db->loadResult();
				$unit_Updated = $db->updateObject('#__content_types', $unit, 'type_id');
			}
			else
			{
				$unit_Inserted = $db->insertObject('#__content_types', $unit);
			}

			// Create the clinic content type object.
			$clinic = new stdClass();
			$clinic->type_title = 'Ehealthportal Clinic';
			$clinic->type_alias = 'com_ehealthportal.clinic';
			$clinic->table = '{"special": {"dbtable": "#__ehealthportal_clinic","key": "id","type": "Clinic","prefix": "ehealthportalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$clinic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "clinic_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"clinic_name":"clinic_name","description":"description","guid":"guid","clinic_type":"clinic_type","alias":"alias"}}';
			$clinic->router = 'EhealthportalHelperRoute::getClinicRoute';
			$clinic->content_history_options = '{"formFile": "administrator/components/com_ehealthportal/models/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if clinic type is already in content_type DB.
			$clinic_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($clinic->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$clinic->type_id = $db->loadResult();
				$clinic_Updated = $db->updateObject('#__content_types', $clinic, 'type_id');
			}
			else
			{
				$clinic_Inserted = $db->insertObject('#__content_types', $clinic);
			}



			echo '<div style="background-color: #fff;" class="alert alert-info"><a target="_blank" href="https://git.vdm.dev/joomla/eHealth-Portal" title="eHealth Portal">
				<img src="components/com_ehealthportal/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 3.0.0 Was Successful! Let us know if anything is not working as expected.</h3></div>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the ehealthportal action logs extensions object.
			$ehealthportal_action_logs_extensions = new stdClass();
			$ehealthportal_action_logs_extensions->extension = 'com_ehealthportal';

			// Check if ehealthportal action log extension is already in action logs extensions DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_logs_extensions'));
			$query->where($db->quoteName('extension') . ' LIKE '. $db->quote($ehealthportal_action_logs_extensions->extension));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the action logs extensions table if not found.
			if (!$db->getNumRows())
			{
				$ehealthportal_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $ehealthportal_action_logs_extensions);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the payment action log config object.
			$payment_action_log_config = new stdClass();
			$payment_action_log_config->id = null;
			$payment_action_log_config->type_title = 'PAYMENT';
			$payment_action_log_config->type_alias = 'com_ehealthportal.payment';
			$payment_action_log_config->id_holder = 'id';
			$payment_action_log_config->title_holder = 'patient';
			$payment_action_log_config->table_name = '#__ehealthportal_payment';
			$payment_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if payment action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment_action_log_config->id = $db->loadResult();
				$payment_action_log_config_Updated = $db->updateObject('#__action_log_config', $payment_action_log_config, 'id');
			}
			else
			{
				$payment_action_log_config_Inserted = $db->insertObject('#__action_log_config', $payment_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the general_medical_check_up action log config object.
			$general_medical_check_up_action_log_config = new stdClass();
			$general_medical_check_up_action_log_config->id = null;
			$general_medical_check_up_action_log_config->type_title = 'GENERAL_MEDICAL_CHECK_UP';
			$general_medical_check_up_action_log_config->type_alias = 'com_ehealthportal.general_medical_check_up';
			$general_medical_check_up_action_log_config->id_holder = 'id';
			$general_medical_check_up_action_log_config->title_holder = 'patient';
			$general_medical_check_up_action_log_config->table_name = '#__ehealthportal_general_medical_check_up';
			$general_medical_check_up_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if general_medical_check_up action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($general_medical_check_up_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$general_medical_check_up_action_log_config->id = $db->loadResult();
				$general_medical_check_up_action_log_config_Updated = $db->updateObject('#__action_log_config', $general_medical_check_up_action_log_config, 'id');
			}
			else
			{
				$general_medical_check_up_action_log_config_Inserted = $db->insertObject('#__action_log_config', $general_medical_check_up_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the antenatal_care action log config object.
			$antenatal_care_action_log_config = new stdClass();
			$antenatal_care_action_log_config->id = null;
			$antenatal_care_action_log_config->type_title = 'ANTENATAL_CARE';
			$antenatal_care_action_log_config->type_alias = 'com_ehealthportal.antenatal_care';
			$antenatal_care_action_log_config->id_holder = 'id';
			$antenatal_care_action_log_config->title_holder = 'patient';
			$antenatal_care_action_log_config->table_name = '#__ehealthportal_antenatal_care';
			$antenatal_care_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if antenatal_care action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($antenatal_care_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$antenatal_care_action_log_config->id = $db->loadResult();
				$antenatal_care_action_log_config_Updated = $db->updateObject('#__action_log_config', $antenatal_care_action_log_config, 'id');
			}
			else
			{
				$antenatal_care_action_log_config_Inserted = $db->insertObject('#__action_log_config', $antenatal_care_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the immunisation action log config object.
			$immunisation_action_log_config = new stdClass();
			$immunisation_action_log_config->id = null;
			$immunisation_action_log_config->type_title = 'IMMUNISATION';
			$immunisation_action_log_config->type_alias = 'com_ehealthportal.immunisation';
			$immunisation_action_log_config->id_holder = 'id';
			$immunisation_action_log_config->title_holder = 'patient';
			$immunisation_action_log_config->table_name = '#__ehealthportal_immunisation';
			$immunisation_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if immunisation action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation_action_log_config->id = $db->loadResult();
				$immunisation_action_log_config_Updated = $db->updateObject('#__action_log_config', $immunisation_action_log_config, 'id');
			}
			else
			{
				$immunisation_action_log_config_Inserted = $db->insertObject('#__action_log_config', $immunisation_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the vmmc action log config object.
			$vmmc_action_log_config = new stdClass();
			$vmmc_action_log_config->id = null;
			$vmmc_action_log_config->type_title = 'VMMC';
			$vmmc_action_log_config->type_alias = 'com_ehealthportal.vmmc';
			$vmmc_action_log_config->id_holder = 'id';
			$vmmc_action_log_config->title_holder = 'patient';
			$vmmc_action_log_config->table_name = '#__ehealthportal_vmmc';
			$vmmc_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if vmmc action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($vmmc_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$vmmc_action_log_config->id = $db->loadResult();
				$vmmc_action_log_config_Updated = $db->updateObject('#__action_log_config', $vmmc_action_log_config, 'id');
			}
			else
			{
				$vmmc_action_log_config_Inserted = $db->insertObject('#__action_log_config', $vmmc_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the prostate_and_testicular_cancer action log config object.
			$prostate_and_testicular_cancer_action_log_config = new stdClass();
			$prostate_and_testicular_cancer_action_log_config->id = null;
			$prostate_and_testicular_cancer_action_log_config->type_title = 'PROSTATE_AND_TESTICULAR_CANCER';
			$prostate_and_testicular_cancer_action_log_config->type_alias = 'com_ehealthportal.prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer_action_log_config->id_holder = 'id';
			$prostate_and_testicular_cancer_action_log_config->title_holder = 'patient';
			$prostate_and_testicular_cancer_action_log_config->table_name = '#__ehealthportal_prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if prostate_and_testicular_cancer action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($prostate_and_testicular_cancer_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$prostate_and_testicular_cancer_action_log_config->id = $db->loadResult();
				$prostate_and_testicular_cancer_action_log_config_Updated = $db->updateObject('#__action_log_config', $prostate_and_testicular_cancer_action_log_config, 'id');
			}
			else
			{
				$prostate_and_testicular_cancer_action_log_config_Inserted = $db->insertObject('#__action_log_config', $prostate_and_testicular_cancer_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the tuberculosis action log config object.
			$tuberculosis_action_log_config = new stdClass();
			$tuberculosis_action_log_config->id = null;
			$tuberculosis_action_log_config->type_title = 'TUBERCULOSIS';
			$tuberculosis_action_log_config->type_alias = 'com_ehealthportal.tuberculosis';
			$tuberculosis_action_log_config->id_holder = 'id';
			$tuberculosis_action_log_config->title_holder = 'patient';
			$tuberculosis_action_log_config->table_name = '#__ehealthportal_tuberculosis';
			$tuberculosis_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if tuberculosis action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($tuberculosis_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$tuberculosis_action_log_config->id = $db->loadResult();
				$tuberculosis_action_log_config_Updated = $db->updateObject('#__action_log_config', $tuberculosis_action_log_config, 'id');
			}
			else
			{
				$tuberculosis_action_log_config_Inserted = $db->insertObject('#__action_log_config', $tuberculosis_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the hiv_counseling_and_testing action log config object.
			$hiv_counseling_and_testing_action_log_config = new stdClass();
			$hiv_counseling_and_testing_action_log_config->id = null;
			$hiv_counseling_and_testing_action_log_config->type_title = 'HIV_COUNSELING_AND_TESTING';
			$hiv_counseling_and_testing_action_log_config->type_alias = 'com_ehealthportal.hiv_counseling_and_testing';
			$hiv_counseling_and_testing_action_log_config->id_holder = 'id';
			$hiv_counseling_and_testing_action_log_config->title_holder = 'patient';
			$hiv_counseling_and_testing_action_log_config->table_name = '#__ehealthportal_hiv_counseling_and_testing';
			$hiv_counseling_and_testing_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if hiv_counseling_and_testing action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($hiv_counseling_and_testing_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$hiv_counseling_and_testing_action_log_config->id = $db->loadResult();
				$hiv_counseling_and_testing_action_log_config_Updated = $db->updateObject('#__action_log_config', $hiv_counseling_and_testing_action_log_config, 'id');
			}
			else
			{
				$hiv_counseling_and_testing_action_log_config_Inserted = $db->insertObject('#__action_log_config', $hiv_counseling_and_testing_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the family_planning action log config object.
			$family_planning_action_log_config = new stdClass();
			$family_planning_action_log_config->id = null;
			$family_planning_action_log_config->type_title = 'FAMILY_PLANNING';
			$family_planning_action_log_config->type_alias = 'com_ehealthportal.family_planning';
			$family_planning_action_log_config->id_holder = 'id';
			$family_planning_action_log_config->title_holder = 'diagnosis';
			$family_planning_action_log_config->table_name = '#__ehealthportal_family_planning';
			$family_planning_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if family_planning action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($family_planning_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$family_planning_action_log_config->id = $db->loadResult();
				$family_planning_action_log_config_Updated = $db->updateObject('#__action_log_config', $family_planning_action_log_config, 'id');
			}
			else
			{
				$family_planning_action_log_config_Inserted = $db->insertObject('#__action_log_config', $family_planning_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the health_education action log config object.
			$health_education_action_log_config = new stdClass();
			$health_education_action_log_config->id = null;
			$health_education_action_log_config->type_title = 'HEALTH_EDUCATION';
			$health_education_action_log_config->type_alias = 'com_ehealthportal.health_education';
			$health_education_action_log_config->id_holder = 'id';
			$health_education_action_log_config->title_holder = 'patient';
			$health_education_action_log_config->table_name = '#__ehealthportal_health_education';
			$health_education_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if health_education action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($health_education_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$health_education_action_log_config->id = $db->loadResult();
				$health_education_action_log_config_Updated = $db->updateObject('#__action_log_config', $health_education_action_log_config, 'id');
			}
			else
			{
				$health_education_action_log_config_Inserted = $db->insertObject('#__action_log_config', $health_education_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the cervical_cancer action log config object.
			$cervical_cancer_action_log_config = new stdClass();
			$cervical_cancer_action_log_config->id = null;
			$cervical_cancer_action_log_config->type_title = 'CERVICAL_CANCER';
			$cervical_cancer_action_log_config->type_alias = 'com_ehealthportal.cervical_cancer';
			$cervical_cancer_action_log_config->id_holder = 'id';
			$cervical_cancer_action_log_config->title_holder = 'patient';
			$cervical_cancer_action_log_config->table_name = '#__ehealthportal_cervical_cancer';
			$cervical_cancer_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if cervical_cancer action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($cervical_cancer_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$cervical_cancer_action_log_config->id = $db->loadResult();
				$cervical_cancer_action_log_config_Updated = $db->updateObject('#__action_log_config', $cervical_cancer_action_log_config, 'id');
			}
			else
			{
				$cervical_cancer_action_log_config_Inserted = $db->insertObject('#__action_log_config', $cervical_cancer_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the breast_cancer action log config object.
			$breast_cancer_action_log_config = new stdClass();
			$breast_cancer_action_log_config->id = null;
			$breast_cancer_action_log_config->type_title = 'BREAST_CANCER';
			$breast_cancer_action_log_config->type_alias = 'com_ehealthportal.breast_cancer';
			$breast_cancer_action_log_config->id_holder = 'id';
			$breast_cancer_action_log_config->title_holder = 'patient';
			$breast_cancer_action_log_config->table_name = '#__ehealthportal_breast_cancer';
			$breast_cancer_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if breast_cancer action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($breast_cancer_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$breast_cancer_action_log_config->id = $db->loadResult();
				$breast_cancer_action_log_config_Updated = $db->updateObject('#__action_log_config', $breast_cancer_action_log_config, 'id');
			}
			else
			{
				$breast_cancer_action_log_config_Inserted = $db->insertObject('#__action_log_config', $breast_cancer_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the test action log config object.
			$test_action_log_config = new stdClass();
			$test_action_log_config->id = null;
			$test_action_log_config->type_title = 'TEST';
			$test_action_log_config->type_alias = 'com_ehealthportal.test';
			$test_action_log_config->id_holder = 'id';
			$test_action_log_config->title_holder = 'patient';
			$test_action_log_config->table_name = '#__ehealthportal_test';
			$test_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if test action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($test_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$test_action_log_config->id = $db->loadResult();
				$test_action_log_config_Updated = $db->updateObject('#__action_log_config', $test_action_log_config, 'id');
			}
			else
			{
				$test_action_log_config_Inserted = $db->insertObject('#__action_log_config', $test_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the foetal_lie action log config object.
			$foetal_lie_action_log_config = new stdClass();
			$foetal_lie_action_log_config->id = null;
			$foetal_lie_action_log_config->type_title = 'FOETAL_LIE';
			$foetal_lie_action_log_config->type_alias = 'com_ehealthportal.foetal_lie';
			$foetal_lie_action_log_config->id_holder = 'id';
			$foetal_lie_action_log_config->title_holder = 'name';
			$foetal_lie_action_log_config->table_name = '#__ehealthportal_foetal_lie';
			$foetal_lie_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if foetal_lie action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_lie_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_lie_action_log_config->id = $db->loadResult();
				$foetal_lie_action_log_config_Updated = $db->updateObject('#__action_log_config', $foetal_lie_action_log_config, 'id');
			}
			else
			{
				$foetal_lie_action_log_config_Inserted = $db->insertObject('#__action_log_config', $foetal_lie_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the immunisation_vaccine_type action log config object.
			$immunisation_vaccine_type_action_log_config = new stdClass();
			$immunisation_vaccine_type_action_log_config->id = null;
			$immunisation_vaccine_type_action_log_config->type_title = 'IMMUNISATION_VACCINE_TYPE';
			$immunisation_vaccine_type_action_log_config->type_alias = 'com_ehealthportal.immunisation_vaccine_type';
			$immunisation_vaccine_type_action_log_config->id_holder = 'id';
			$immunisation_vaccine_type_action_log_config->title_holder = 'name';
			$immunisation_vaccine_type_action_log_config->table_name = '#__ehealthportal_immunisation_vaccine_type';
			$immunisation_vaccine_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if immunisation_vaccine_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation_vaccine_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation_vaccine_type_action_log_config->id = $db->loadResult();
				$immunisation_vaccine_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $immunisation_vaccine_type_action_log_config, 'id');
			}
			else
			{
				$immunisation_vaccine_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $immunisation_vaccine_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the foetal_engagement action log config object.
			$foetal_engagement_action_log_config = new stdClass();
			$foetal_engagement_action_log_config->id = null;
			$foetal_engagement_action_log_config->type_title = 'FOETAL_ENGAGEMENT';
			$foetal_engagement_action_log_config->type_alias = 'com_ehealthportal.foetal_engagement';
			$foetal_engagement_action_log_config->id_holder = 'id';
			$foetal_engagement_action_log_config->title_holder = 'name';
			$foetal_engagement_action_log_config->table_name = '#__ehealthportal_foetal_engagement';
			$foetal_engagement_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if foetal_engagement action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_engagement_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_engagement_action_log_config->id = $db->loadResult();
				$foetal_engagement_action_log_config_Updated = $db->updateObject('#__action_log_config', $foetal_engagement_action_log_config, 'id');
			}
			else
			{
				$foetal_engagement_action_log_config_Inserted = $db->insertObject('#__action_log_config', $foetal_engagement_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the foetal_presentation action log config object.
			$foetal_presentation_action_log_config = new stdClass();
			$foetal_presentation_action_log_config->id = null;
			$foetal_presentation_action_log_config->type_title = 'FOETAL_PRESENTATION';
			$foetal_presentation_action_log_config->type_alias = 'com_ehealthportal.foetal_presentation';
			$foetal_presentation_action_log_config->id_holder = 'id';
			$foetal_presentation_action_log_config->title_holder = 'name';
			$foetal_presentation_action_log_config->table_name = '#__ehealthportal_foetal_presentation';
			$foetal_presentation_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if foetal_presentation action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_presentation_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_presentation_action_log_config->id = $db->loadResult();
				$foetal_presentation_action_log_config_Updated = $db->updateObject('#__action_log_config', $foetal_presentation_action_log_config, 'id');
			}
			else
			{
				$foetal_presentation_action_log_config_Inserted = $db->insertObject('#__action_log_config', $foetal_presentation_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the testing_reason action log config object.
			$testing_reason_action_log_config = new stdClass();
			$testing_reason_action_log_config->id = null;
			$testing_reason_action_log_config->type_title = 'TESTING_REASON';
			$testing_reason_action_log_config->type_alias = 'com_ehealthportal.testing_reason';
			$testing_reason_action_log_config->id_holder = 'id';
			$testing_reason_action_log_config->title_holder = 'name';
			$testing_reason_action_log_config->table_name = '#__ehealthportal_testing_reason';
			$testing_reason_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if testing_reason action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($testing_reason_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$testing_reason_action_log_config->id = $db->loadResult();
				$testing_reason_action_log_config_Updated = $db->updateObject('#__action_log_config', $testing_reason_action_log_config, 'id');
			}
			else
			{
				$testing_reason_action_log_config_Inserted = $db->insertObject('#__action_log_config', $testing_reason_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the counseling_type action log config object.
			$counseling_type_action_log_config = new stdClass();
			$counseling_type_action_log_config->id = null;
			$counseling_type_action_log_config->type_title = 'COUNSELING_TYPE';
			$counseling_type_action_log_config->type_alias = 'com_ehealthportal.counseling_type';
			$counseling_type_action_log_config->id_holder = 'id';
			$counseling_type_action_log_config->title_holder = 'name';
			$counseling_type_action_log_config->table_name = '#__ehealthportal_counseling_type';
			$counseling_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if counseling_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($counseling_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$counseling_type_action_log_config->id = $db->loadResult();
				$counseling_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $counseling_type_action_log_config, 'id');
			}
			else
			{
				$counseling_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $counseling_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the health_education_topic action log config object.
			$health_education_topic_action_log_config = new stdClass();
			$health_education_topic_action_log_config->id = null;
			$health_education_topic_action_log_config->type_title = 'HEALTH_EDUCATION_TOPIC';
			$health_education_topic_action_log_config->type_alias = 'com_ehealthportal.health_education_topic';
			$health_education_topic_action_log_config->id_holder = 'id';
			$health_education_topic_action_log_config->title_holder = 'name';
			$health_education_topic_action_log_config->table_name = '#__ehealthportal_health_education_topic';
			$health_education_topic_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if health_education_topic action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($health_education_topic_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$health_education_topic_action_log_config->id = $db->loadResult();
				$health_education_topic_action_log_config_Updated = $db->updateObject('#__action_log_config', $health_education_topic_action_log_config, 'id');
			}
			else
			{
				$health_education_topic_action_log_config_Inserted = $db->insertObject('#__action_log_config', $health_education_topic_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the immunisation_type action log config object.
			$immunisation_type_action_log_config = new stdClass();
			$immunisation_type_action_log_config->id = null;
			$immunisation_type_action_log_config->type_title = 'IMMUNISATION_TYPE';
			$immunisation_type_action_log_config->type_alias = 'com_ehealthportal.immunisation_type';
			$immunisation_type_action_log_config->id_holder = 'id';
			$immunisation_type_action_log_config->title_holder = 'name';
			$immunisation_type_action_log_config->table_name = '#__ehealthportal_immunisation_type';
			$immunisation_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if immunisation_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation_type_action_log_config->id = $db->loadResult();
				$immunisation_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $immunisation_type_action_log_config, 'id');
			}
			else
			{
				$immunisation_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $immunisation_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the strength action log config object.
			$strength_action_log_config = new stdClass();
			$strength_action_log_config->id = null;
			$strength_action_log_config->type_title = 'STRENGTH';
			$strength_action_log_config->type_alias = 'com_ehealthportal.strength';
			$strength_action_log_config->id_holder = 'id';
			$strength_action_log_config->title_holder = 'name';
			$strength_action_log_config->table_name = '#__ehealthportal_strength';
			$strength_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if strength action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($strength_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$strength_action_log_config->id = $db->loadResult();
				$strength_action_log_config_Updated = $db->updateObject('#__action_log_config', $strength_action_log_config, 'id');
			}
			else
			{
				$strength_action_log_config_Inserted = $db->insertObject('#__action_log_config', $strength_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the referral action log config object.
			$referral_action_log_config = new stdClass();
			$referral_action_log_config->id = null;
			$referral_action_log_config->type_title = 'REFERRAL';
			$referral_action_log_config->type_alias = 'com_ehealthportal.referral';
			$referral_action_log_config->id_holder = 'id';
			$referral_action_log_config->title_holder = 'name';
			$referral_action_log_config->table_name = '#__ehealthportal_referral';
			$referral_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if referral action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($referral_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$referral_action_log_config->id = $db->loadResult();
				$referral_action_log_config_Updated = $db->updateObject('#__action_log_config', $referral_action_log_config, 'id');
			}
			else
			{
				$referral_action_log_config_Inserted = $db->insertObject('#__action_log_config', $referral_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the planning_type action log config object.
			$planning_type_action_log_config = new stdClass();
			$planning_type_action_log_config->id = null;
			$planning_type_action_log_config->type_title = 'PLANNING_TYPE';
			$planning_type_action_log_config->type_alias = 'com_ehealthportal.planning_type';
			$planning_type_action_log_config->id_holder = 'id';
			$planning_type_action_log_config->title_holder = 'name';
			$planning_type_action_log_config->table_name = '#__ehealthportal_planning_type';
			$planning_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if planning_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($planning_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$planning_type_action_log_config->id = $db->loadResult();
				$planning_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $planning_type_action_log_config, 'id');
			}
			else
			{
				$planning_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $planning_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the diagnosis_type action log config object.
			$diagnosis_type_action_log_config = new stdClass();
			$diagnosis_type_action_log_config->id = null;
			$diagnosis_type_action_log_config->type_title = 'DIAGNOSIS_TYPE';
			$diagnosis_type_action_log_config->type_alias = 'com_ehealthportal.diagnosis_type';
			$diagnosis_type_action_log_config->id_holder = 'id';
			$diagnosis_type_action_log_config->title_holder = 'name';
			$diagnosis_type_action_log_config->table_name = '#__ehealthportal_diagnosis_type';
			$diagnosis_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if diagnosis_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($diagnosis_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$diagnosis_type_action_log_config->id = $db->loadResult();
				$diagnosis_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $diagnosis_type_action_log_config, 'id');
			}
			else
			{
				$diagnosis_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $diagnosis_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the nonpay_reason action log config object.
			$nonpay_reason_action_log_config = new stdClass();
			$nonpay_reason_action_log_config->id = null;
			$nonpay_reason_action_log_config->type_title = 'NONPAY_REASON';
			$nonpay_reason_action_log_config->type_alias = 'com_ehealthportal.nonpay_reason';
			$nonpay_reason_action_log_config->id_holder = 'id';
			$nonpay_reason_action_log_config->title_holder = 'name';
			$nonpay_reason_action_log_config->table_name = '#__ehealthportal_nonpay_reason';
			$nonpay_reason_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if nonpay_reason action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($nonpay_reason_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$nonpay_reason_action_log_config->id = $db->loadResult();
				$nonpay_reason_action_log_config_Updated = $db->updateObject('#__action_log_config', $nonpay_reason_action_log_config, 'id');
			}
			else
			{
				$nonpay_reason_action_log_config_Inserted = $db->insertObject('#__action_log_config', $nonpay_reason_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the medication action log config object.
			$medication_action_log_config = new stdClass();
			$medication_action_log_config->id = null;
			$medication_action_log_config->type_title = 'MEDICATION';
			$medication_action_log_config->type_alias = 'com_ehealthportal.medication';
			$medication_action_log_config->id_holder = 'id';
			$medication_action_log_config->title_holder = 'name';
			$medication_action_log_config->table_name = '#__ehealthportal_medication';
			$medication_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if medication action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($medication_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$medication_action_log_config->id = $db->loadResult();
				$medication_action_log_config_Updated = $db->updateObject('#__action_log_config', $medication_action_log_config, 'id');
			}
			else
			{
				$medication_action_log_config_Inserted = $db->insertObject('#__action_log_config', $medication_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the payment_type action log config object.
			$payment_type_action_log_config = new stdClass();
			$payment_type_action_log_config->id = null;
			$payment_type_action_log_config->type_title = 'PAYMENT_TYPE';
			$payment_type_action_log_config->type_alias = 'com_ehealthportal.payment_type';
			$payment_type_action_log_config->id_holder = 'id';
			$payment_type_action_log_config->title_holder = 'name';
			$payment_type_action_log_config->table_name = '#__ehealthportal_payment_type';
			$payment_type_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if payment_type action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment_type_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment_type_action_log_config->id = $db->loadResult();
				$payment_type_action_log_config_Updated = $db->updateObject('#__action_log_config', $payment_type_action_log_config, 'id');
			}
			else
			{
				$payment_type_action_log_config_Inserted = $db->insertObject('#__action_log_config', $payment_type_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the administration_part action log config object.
			$administration_part_action_log_config = new stdClass();
			$administration_part_action_log_config->id = null;
			$administration_part_action_log_config->type_title = 'ADMINISTRATION_PART';
			$administration_part_action_log_config->type_alias = 'com_ehealthportal.administration_part';
			$administration_part_action_log_config->id_holder = 'id';
			$administration_part_action_log_config->title_holder = 'name';
			$administration_part_action_log_config->table_name = '#__ehealthportal_administration_part';
			$administration_part_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if administration_part action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($administration_part_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$administration_part_action_log_config->id = $db->loadResult();
				$administration_part_action_log_config_Updated = $db->updateObject('#__action_log_config', $administration_part_action_log_config, 'id');
			}
			else
			{
				$administration_part_action_log_config_Inserted = $db->insertObject('#__action_log_config', $administration_part_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the site action log config object.
			$site_action_log_config = new stdClass();
			$site_action_log_config->id = null;
			$site_action_log_config->type_title = 'SITE';
			$site_action_log_config->type_alias = 'com_ehealthportal.site';
			$site_action_log_config->id_holder = 'id';
			$site_action_log_config->title_holder = 'site_name';
			$site_action_log_config->table_name = '#__ehealthportal_site';
			$site_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if site action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($site_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$site_action_log_config->id = $db->loadResult();
				$site_action_log_config_Updated = $db->updateObject('#__action_log_config', $site_action_log_config, 'id');
			}
			else
			{
				$site_action_log_config_Inserted = $db->insertObject('#__action_log_config', $site_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the unit action log config object.
			$unit_action_log_config = new stdClass();
			$unit_action_log_config->id = null;
			$unit_action_log_config->type_title = 'UNIT';
			$unit_action_log_config->type_alias = 'com_ehealthportal.unit';
			$unit_action_log_config->id_holder = 'id';
			$unit_action_log_config->title_holder = 'name';
			$unit_action_log_config->table_name = '#__ehealthportal_unit';
			$unit_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if unit action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($unit_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$unit_action_log_config->id = $db->loadResult();
				$unit_action_log_config_Updated = $db->updateObject('#__action_log_config', $unit_action_log_config, 'id');
			}
			else
			{
				$unit_action_log_config_Inserted = $db->insertObject('#__action_log_config', $unit_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = Factory::getDbo();
			}
			// Create the clinic action log config object.
			$clinic_action_log_config = new stdClass();
			$clinic_action_log_config->id = null;
			$clinic_action_log_config->type_title = 'CLINIC';
			$clinic_action_log_config->type_alias = 'com_ehealthportal.clinic';
			$clinic_action_log_config->id_holder = 'id';
			$clinic_action_log_config->title_holder = 'clinic_name';
			$clinic_action_log_config->table_name = '#__ehealthportal_clinic';
			$clinic_action_log_config->text_prefix = 'COM_EHEALTHPORTAL';

			// Check if clinic action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($clinic_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$clinic_action_log_config->id = $db->loadResult();
				$clinic_action_log_config_Updated = $db->updateObject('#__action_log_config', $clinic_action_log_config, 'id');
			}
			else
			{
				$clinic_action_log_config_Inserted = $db->insertObject('#__action_log_config', $clinic_action_log_config);
			}
		}
		return true;
	}

	/**
	 * Remove folders with files
	 *
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 *
	 */
	protected function removeFolder($dir, $ignore = false)
	{
		if (Folder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					Folder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					File::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!$this->checkArray($ignore))
			{
				return Folder::delete($dir);
			}
			return true;
		}
		return false;
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input    array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 */
	protected function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return $this->checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// get the installation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = Folder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = ['media','admin','site']; // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!Folder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
