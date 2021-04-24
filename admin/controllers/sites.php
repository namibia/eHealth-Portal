<?php
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

	@version		1.0.5
	@build			24th April, 2021
	@created		13th August, 2020
	@package		eHealth Portal
	@subpackage		sites.php
	@author			Oh Martin <https://github.com/namibia/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * Sites Controller
 */
class Ehealth_portalControllerSites extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_EHEALTH_PORTAL_SITES';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Site', $prefix = 'Ehealth_portalModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('site.export', 'com_ehealth_portal') && $user->authorise('core.export', 'com_ehealth_portal'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Sites');
			// get the data to export
			$data = $model->getExportData($pks);
			if (Ehealth_portalHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				Ehealth_portalHelper::xls($data,'Sites_'.$date->format('jS_F_Y'),'Sites exported ('.$date->format('jS F, Y').')','sites');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_EHEALTH_PORTAL_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_ehealth_portal&view=sites', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('site.import', 'com_ehealth_portal') && $user->authorise('core.import', 'com_ehealth_portal'))
		{
			// Get the import model
			$model = $this->getModel('Sites');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (Ehealth_portalHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('site_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'sites');
				$session->set('dataType_VDM_IMPORTINTO', 'site');
				// Redirect to import view.
				$message = JText::_('COM_EHEALTH_PORTAL_IMPORT_SELECT_FILE_FOR_SITES');
				$this->setRedirect(JRoute::_('index.php?option=com_ehealth_portal&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_EHEALTH_PORTAL_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_ehealth_portal&view=sites', false), $message, 'error');
		return;
	}
}
