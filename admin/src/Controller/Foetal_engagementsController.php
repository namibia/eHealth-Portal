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
namespace VDM\Component\Ehealthportal\Administrator\Controller;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;

/**
 * Foetal_engagements Admin Controller
 */
class Foetal_engagementsController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_EHEALTHPORTAL_FOETAL_ENGAGEMENTS';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Foetal_engagement', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = Factory::getApplication()->getIdentity();
		if ($user->authorise('foetal_engagement.export', 'com_ehealthportal') && $user->authorise('core.export', 'com_ehealthportal'))
		{
			// Get the input
			$input = Factory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Foetal_engagements');
			// get the data to export
			$data = $model->getExportData($pks);
			if (UtilitiesArrayHelper::check($data))
			{
				// now set the data to the spreadsheet
				$date = Factory::getDate();
				EhealthportalHelper::xls($data,'Foetal_engagements_'.$date->format('jS_F_Y'),'Foetal engagements exported ('.$date->format('jS F, Y').')','foetal engagements');
			}
		}
		// Redirect to the list screen with error.
		$message = Text::_('COM_EHEALTHPORTAL_EXPORT_FAILED');
		$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view=foetal_engagements', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = Factory::getApplication()->getIdentity();
		if ($user->authorise('foetal_engagement.import', 'com_ehealthportal') && $user->authorise('core.import', 'com_ehealthportal'))
		{
			// Get the import model
			$model = $this->getModel('Foetal_engagements');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (ObjectHelper::check($headers))
			{
				// Load headers to session.
				$session = Factory::getSession();
				$headers = json_encode($headers);
				$session->set('foetal_engagement_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'foetal_engagements');
				$session->set('dataType_VDM_IMPORTINTO', 'foetal_engagement');
				// Redirect to import view.
				$message = Text::_('COM_EHEALTHPORTAL_IMPORT_SELECT_FILE_FOR_FOETAL_ENGAGEMENTS');
				$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = Text::_('COM_EHEALTHPORTAL_IMPORT_FAILED');
		$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view=foetal_engagements', false), $message, 'error');
		return;
	}
}