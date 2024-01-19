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

	@version		3.0.0
	@build			19th January, 2024
	@created		19th January, 2024
	@package		eHealth Portal
	@subpackage		controller.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * General Controller of Ehealthportal component
 */
class EhealthportalController extends BaseController
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   3.0
	 */
	public function __construct($config = [])
	{
		// set the default view
		$config['default_view'] = 'ehealthportal';

		parent::__construct($config);
	}

	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		// set default view if not set
		$view      = $this->input->getCmd('view', 'ehealthportal');
		$data      = $this->getViewRelation($view);
		$layout    = $this->input->get('layout', null, 'WORD');
		$id        = $this->input->getInt('id');

		// Check for edit form.
		if(UtilitiesArrayHelper::check($data))
		{
			if ($data['edit'] && $layout == 'edit' && !$this->checkEditId('com_ehealthportal.edit.'.$data['view'], $id))
			{
				// Somehow the person just went to the form - we don't allow that.
				$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
				$this->setMessage($this->getError(), 'error');
				// check if item was opend from other then its own list view
				$ref     = $this->input->getCmd('ref', 0);
				$refid   = $this->input->getInt('refid', 0);
				// set redirect
				if ($refid > 0 && StringHelper::check($ref))
				{
					// redirect to item of ref
					$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view='.(string)$ref.'&layout=edit&id='.(int)$refid, false));
				}
				elseif (StringHelper::check($ref))
				{

					// redirect to ref
					$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view='.(string)$ref, false));
				}
				else
				{
					// normal redirect back to the list view
					$this->setRedirect(Route::_('index.php?option=com_ehealthportal&view='.$data['views'], false));
				}

				return false;
			}
		}

		return parent::display($cachable, $urlparams);
	}

	protected function getViewRelation($view)
	{
		// check the we have a value
		if (StringHelper::check($view))
		{
			// the view relationships
			$views = array(
				'payment' => 'payments',
				'general_medical_check_up' => 'general_medical_check_ups',
				'antenatal_care' => 'antenatal_cares',
				'immunisation' => 'immunisations',
				'vmmc' => 'vmmcs',
				'prostate_and_testicular_cancer' => 'prostate_and_testicular_cancers',
				'tuberculosis' => 'tuberculoses',
				'hiv_counseling_and_testing' => 'hiv_counselings_and_testings',
				'family_planning' => 'family_plannings',
				'health_education' => 'health_educations',
				'cervical_cancer' => 'cervical_cancers',
				'breast_cancer' => 'breast_cancers',
				'test' => 'tests',
				'foetal_lie' => 'foetal_lies',
				'immunisation_vaccine_type' => 'immunisation_vaccine_types',
				'foetal_engagement' => 'foetal_engagements',
				'foetal_presentation' => 'foetal_presentations',
				'testing_reason' => 'testing_reasons',
				'counseling_type' => 'counseling_types',
				'health_education_topic' => 'health_education_topics',
				'immunisation_type' => 'immunisation_types',
				'strength' => 'strengths',
				'referral' => 'referrals',
				'planning_type' => 'planning_types',
				'diagnosis_type' => 'diagnosis_types',
				'nonpay_reason' => 'nonpay_reasons',
				'medication' => 'medications',
				'payment_type' => 'payment_types',
				'administration_part' => 'administration_parts',
				'site' => 'sites',
				'unit' => 'units',
				'clinic' => 'clinics'
					);
			// check if this is a list view
			if (in_array($view, $views))
			{
				// this is a list view
				return array('edit' => false, 'view' => array_search($view,$views), 'views' => $view);
			}
			// check if it is an edit view
			elseif (array_key_exists($view, $views))
			{
				// this is a edit view
				return array('edit' => true, 'view' => $view, 'views' => $views[$view]);
			}
		}
		return false;
	}
}
