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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;

/**
 * Immunisation_vaccine_types Admin Controller
 */
class EhealthportalControllerImmunisation_vaccine_types extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_EHEALTHPORTAL_IMMUNISATION_VACCINE_TYPES';

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
	public function getModel($name = 'Immunisation_vaccine_type', $prefix = 'EhealthportalModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = Factory::getUser();
		if ($user->authorise('immunisation_vaccine_type.export', 'com_ehealthportal') && $user->authorise('core.export', 'com_ehealthportal'))
		{
			// Get the input
			$input = Factory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Immunisation_vaccine_types');
			// get the data to export
			$data = $model->getExportData($pks);
			if (UtilitiesArrayHelper::check($data))
			{
				// now set the data to the spreadsheet
				$date = Factory::getDate();
				EhealthportalHelper::xls($data,'Immunisation_vaccine_types_'.$date->format('jS_F_Y'),'Immunisation vaccine types exported ('.$date->format('jS F, Y').')','immunisation vaccine types');
			}
		}
		// Redirect to the list screen with error.
		$message = Text::_('COM_EHEALTHPORTAL_EXPORT_FAILED');
		$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view=immunisation_vaccine_types', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = Factory::getUser();
		if ($user->authorise('immunisation_vaccine_type.import', 'com_ehealthportal') && $user->authorise('core.import', 'com_ehealthportal'))
		{
			// Get the import model
			$model = $this->getModel('Immunisation_vaccine_types');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (ObjectHelper::check($headers))
			{
				// Load headers to session.
				$session = Factory::getSession();
				$headers = json_encode($headers);
				$session->set('immunisation_vaccine_type_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'immunisation_vaccine_types');
				$session->set('dataType_VDM_IMPORTINTO', 'immunisation_vaccine_type');
				// Redirect to import view.
				$message = Text::_('COM_EHEALTHPORTAL_IMPORT_SELECT_FILE_FOR_IMMUNISATION_VACCINE_TYPES');
				$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = Text::_('COM_EHEALTHPORTAL_IMPORT_FAILED');
		$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view=immunisation_vaccine_types', false), $message, 'error');
		return;
	}
}