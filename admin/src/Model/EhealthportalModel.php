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
namespace VDM\Component\Ehealthportal\Administrator\Model;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Ehealthportal List Model
 */
class EhealthportalModel extends ListModel
{
	public function getIcons()
	{
		// load user for access menus
		$user = Factory::getApplication()->getIdentity();
		// reset icon array
		$icons  = [];
		// view groups array
		$viewGroups = array(
			'main' => array('png.payment.add', 'png.payments', 'png.general_medical_check_up.add', 'png.general_medical_check_ups', 'png.antenatal_care.add', 'png.antenatal_cares', 'png.immunisation.add', 'png.immunisations', 'png.vmmc.add', 'png.vmmcs', 'png.prostate_and_testicular_cancer.add', 'png.prostate_and_testicular_cancers', 'png.tuberculosis.add', 'png.tuberculoses', 'png.hiv_counseling_and_testing.add', 'png.hiv_counselings_and_testings', 'png.family_planning.add', 'png.family_plannings', 'png.health_education.add', 'png.health_educations', 'png.cervical_cancer.add', 'png.cervical_cancers', 'png.breast_cancer.add', 'png.breast_cancers', 'png.test.add', 'png.tests', 'png.patient_queue')
		);
		// view access array
		$viewAccess = [
			'patient_queue.access' => 'patient_queue.access',
			'patient_queue.submenu' => 'patient_queue.submenu',
			'patient_queue.dashboard_list' => 'patient_queue.dashboard_list',
			'payments.access' => 'payment.access',
			'payment.access' => 'payment.access',
			'payments.submenu' => 'payment.submenu',
			'payments.dashboard_list' => 'payment.dashboard_list',
			'payment.dashboard_add' => 'payment.dashboard_add',
			'general_medical_check_ups.access' => 'general_medical_check_up.access',
			'general_medical_check_up.access' => 'general_medical_check_up.access',
			'general_medical_check_ups.submenu' => 'general_medical_check_up.submenu',
			'general_medical_check_ups.dashboard_list' => 'general_medical_check_up.dashboard_list',
			'general_medical_check_up.dashboard_add' => 'general_medical_check_up.dashboard_add',
			'antenatal_cares.access' => 'antenatal_care.access',
			'antenatal_care.access' => 'antenatal_care.access',
			'antenatal_cares.submenu' => 'antenatal_care.submenu',
			'antenatal_cares.dashboard_list' => 'antenatal_care.dashboard_list',
			'antenatal_care.dashboard_add' => 'antenatal_care.dashboard_add',
			'immunisations.access' => 'immunisation.access',
			'immunisation.access' => 'immunisation.access',
			'immunisations.submenu' => 'immunisation.submenu',
			'immunisations.dashboard_list' => 'immunisation.dashboard_list',
			'immunisation.dashboard_add' => 'immunisation.dashboard_add',
			'vmmcs.access' => 'vmmc.access',
			'vmmc.access' => 'vmmc.access',
			'vmmcs.submenu' => 'vmmc.submenu',
			'vmmcs.dashboard_list' => 'vmmc.dashboard_list',
			'vmmc.dashboard_add' => 'vmmc.dashboard_add',
			'prostate_and_testicular_cancers.access' => 'prostate_and_testicular_cancer.access',
			'prostate_and_testicular_cancer.access' => 'prostate_and_testicular_cancer.access',
			'prostate_and_testicular_cancers.submenu' => 'prostate_and_testicular_cancer.submenu',
			'prostate_and_testicular_cancers.dashboard_list' => 'prostate_and_testicular_cancer.dashboard_list',
			'prostate_and_testicular_cancer.dashboard_add' => 'prostate_and_testicular_cancer.dashboard_add',
			'tuberculoses.access' => 'tuberculosis.access',
			'tuberculosis.access' => 'tuberculosis.access',
			'tuberculoses.submenu' => 'tuberculosis.submenu',
			'tuberculoses.dashboard_list' => 'tuberculosis.dashboard_list',
			'tuberculosis.dashboard_add' => 'tuberculosis.dashboard_add',
			'hiv_counselings_and_testings.access' => 'hiv_counseling_and_testing.access',
			'hiv_counseling_and_testing.access' => 'hiv_counseling_and_testing.access',
			'hiv_counselings_and_testings.submenu' => 'hiv_counseling_and_testing.submenu',
			'hiv_counselings_and_testings.dashboard_list' => 'hiv_counseling_and_testing.dashboard_list',
			'hiv_counseling_and_testing.dashboard_add' => 'hiv_counseling_and_testing.dashboard_add',
			'family_plannings.access' => 'family_planning.access',
			'family_planning.access' => 'family_planning.access',
			'family_plannings.submenu' => 'family_planning.submenu',
			'family_plannings.dashboard_list' => 'family_planning.dashboard_list',
			'family_planning.dashboard_add' => 'family_planning.dashboard_add',
			'health_educations.access' => 'health_education.access',
			'health_education.access' => 'health_education.access',
			'health_educations.submenu' => 'health_education.submenu',
			'health_educations.dashboard_list' => 'health_education.dashboard_list',
			'health_education.dashboard_add' => 'health_education.dashboard_add',
			'cervical_cancers.access' => 'cervical_cancer.access',
			'cervical_cancer.access' => 'cervical_cancer.access',
			'cervical_cancers.submenu' => 'cervical_cancer.submenu',
			'cervical_cancers.dashboard_list' => 'cervical_cancer.dashboard_list',
			'cervical_cancer.dashboard_add' => 'cervical_cancer.dashboard_add',
			'breast_cancers.access' => 'breast_cancer.access',
			'breast_cancer.access' => 'breast_cancer.access',
			'breast_cancers.submenu' => 'breast_cancer.submenu',
			'breast_cancers.dashboard_list' => 'breast_cancer.dashboard_list',
			'breast_cancer.dashboard_add' => 'breast_cancer.dashboard_add',
			'tests.access' => 'test.access',
			'test.access' => 'test.access',
			'tests.submenu' => 'test.submenu',
			'tests.dashboard_list' => 'test.dashboard_list',
			'test.dashboard_add' => 'test.dashboard_add',
			'foetal_lies.access' => 'foetal_lie.access',
			'foetal_lie.access' => 'foetal_lie.access',
			'immunisation_vaccine_types.access' => 'immunisation_vaccine_type.access',
			'immunisation_vaccine_type.access' => 'immunisation_vaccine_type.access',
			'immunisation_vaccine_types.submenu' => 'immunisation_vaccine_type.submenu',
			'foetal_engagements.access' => 'foetal_engagement.access',
			'foetal_engagement.access' => 'foetal_engagement.access',
			'foetal_presentations.access' => 'foetal_presentation.access',
			'foetal_presentation.access' => 'foetal_presentation.access',
			'testing_reasons.access' => 'testing_reason.access',
			'testing_reason.access' => 'testing_reason.access',
			'counseling_types.access' => 'counseling_type.access',
			'counseling_type.access' => 'counseling_type.access',
			'health_education_topics.access' => 'health_education_topic.access',
			'health_education_topic.access' => 'health_education_topic.access',
			'immunisation_types.access' => 'immunisation_type.access',
			'immunisation_type.access' => 'immunisation_type.access',
			'immunisation_types.submenu' => 'immunisation_type.submenu',
			'strengths.access' => 'strength.access',
			'strength.access' => 'strength.access',
			'strengths.submenu' => 'strength.submenu',
			'referrals.access' => 'referral.access',
			'referral.access' => 'referral.access',
			'referrals.submenu' => 'referral.submenu',
			'planning_types.access' => 'planning_type.access',
			'planning_type.access' => 'planning_type.access',
			'diagnosis_types.access' => 'diagnosis_type.access',
			'diagnosis_type.access' => 'diagnosis_type.access',
			'nonpay_reasons.access' => 'nonpay_reason.access',
			'nonpay_reason.access' => 'nonpay_reason.access',
			'medications.access' => 'medication.access',
			'medication.access' => 'medication.access',
			'medications.submenu' => 'medication.submenu',
			'payment_types.access' => 'payment_type.access',
			'payment_type.access' => 'payment_type.access',
			'administration_parts.access' => 'administration_part.access',
			'administration_part.access' => 'administration_part.access',
			'administration_parts.submenu' => 'administration_part.submenu',
			'sites.access' => 'site.access',
			'site.access' => 'site.access',
			'units.access' => 'unit.access',
			'unit.access' => 'unit.access',
			'units.submenu' => 'unit.submenu',
			'clinics.access' => 'clinic.access',
			'clinic.access' => 'clinic.access',
		];
		// loop over the $views
		foreach($viewGroups as $group => $views)
		{
			$i = 0;
			if (UtilitiesArrayHelper::check($views))
			{
				foreach($views as $view)
				{
					$add = false;
					// external views (links)
					if (strpos($view,'||') !== false)
					{
						$dwd = explode('||', $view);
						if (count($dwd) == 3)
						{
							list($type, $name, $url) = $dwd;
							$viewName = $name;
							$alt      = $name;
							$url      = $url;
							$image    = $name . '.' . $type;
							$name     = 'COM_EHEALTHPORTAL_DASHBOARD_' . StringHelper::safe($name,'U');
						}
					}
					// internal views
					elseif (strpos($view,'.') !== false)
					{
						$dwd = explode('.', $view);
						if (count($dwd) == 3)
						{
							list($type, $name, $action) = $dwd;
						}
						elseif (count($dwd) == 2)
						{
							list($type, $name) = $dwd;
							$action = false;
						}
						if ($action)
						{
							$viewName = $name;
							switch($action)
							{
								case 'add':
									$url   = 'index.php?option=com_ehealthportal&view=' . $name . '&layout=edit';
									$image = $name . '_' . $action.  '.' . $type;
									$alt   = $name . '&nbsp;' . $action;
									$name  = 'COM_EHEALTHPORTAL_DASHBOARD_'.StringHelper::safe($name,'U').'_ADD';
									$add   = true;
								break;
								default:
									// check for new convention (more stable)
									if (strpos($action, '_qpo0O0oqp_') !== false)
									{
										list($action, $extension) = (array) explode('_qpo0O0oqp_', $action);
										$extension = str_replace('_po0O0oq_', '.', $extension);
									}
									else
									{
										$extension = 'com_ehealthportal.' . $name;
									}
									$url   = 'index.php?option=com_categories&view=categories&extension=' . $extension;
									$image = $name . '_' . $action . '.' . $type;
									$alt   = $viewName . '&nbsp;' . $action;
									$name  = 'COM_EHEALTHPORTAL_DASHBOARD_' . StringHelper::safe($name,'U') . '_' . StringHelper::safe($action,'U');
								break;
							}
						}
						else
						{
							$viewName = $name;
							$alt      = $name;
							$url      = 'index.php?option=com_ehealthportal&view=' . $name;
							$image    = $name . '.' . $type;
							$name     = 'COM_EHEALTHPORTAL_DASHBOARD_' . StringHelper::safe($name,'U');
							$hover    = false;
						}
					}
					else
					{
						$viewName = $view;
						$alt      = $view;
						$url      = 'index.php?option=com_ehealthportal&view=' . $view;
						$image    = $view . '.png';
						$name     = ucwords($view).'<br /><br />';
						$hover    = false;
					}
					// first make sure the view access is set
					if (UtilitiesArrayHelper::check($viewAccess))
					{
						// setup some defaults
						$dashboard_add = false;
						$dashboard_list = false;
						$accessTo = '';
						$accessAdd = '';
						// access checking start
						$accessCreate = (isset($viewAccess[$viewName.'.create'])) ? StringHelper::check($viewAccess[$viewName.'.create']):false;
						$accessAccess = (isset($viewAccess[$viewName.'.access'])) ? StringHelper::check($viewAccess[$viewName.'.access']):false;
						// set main controllers
						$accessDashboard_add = (isset($viewAccess[$viewName.'.dashboard_add'])) ? StringHelper::check($viewAccess[$viewName.'.dashboard_add']):false;
						$accessDashboard_list = (isset($viewAccess[$viewName.'.dashboard_list'])) ? StringHelper::check($viewAccess[$viewName.'.dashboard_list']):false;
						// check for adding access
						if ($add && $accessCreate)
						{
							$accessAdd = $viewAccess[$viewName.'.create'];
						}
						elseif ($add)
						{
							$accessAdd = 'core.create';
						}
						// check if access to view is set
						if ($accessAccess)
						{
							$accessTo = $viewAccess[$viewName.'.access'];
						}
						// set main access controllers
						if ($accessDashboard_add)
						{
							$dashboard_add    = $user->authorise($viewAccess[$viewName.'.dashboard_add'], 'com_ehealthportal');
						}
						if ($accessDashboard_list)
						{
							$dashboard_list = $user->authorise($viewAccess[$viewName.'.dashboard_list'], 'com_ehealthportal');
						}
						if (StringHelper::check($accessAdd) && StringHelper::check($accessTo))
						{
							// check access
							if($user->authorise($accessAdd, 'com_ehealthportal') && $user->authorise($accessTo, 'com_ehealthportal') && $dashboard_add)
							{
								$icons[$group][$i]        = new \StdClass;
								$icons[$group][$i]->url   = $url;
								$icons[$group][$i]->name  = $name;
								$icons[$group][$i]->image = $image;
								$icons[$group][$i]->alt   = $alt;
							}
						}
						elseif (StringHelper::check($accessTo))
						{
							// check access
							if($user->authorise($accessTo, 'com_ehealthportal') && $dashboard_list)
							{
								$icons[$group][$i]        = new \StdClass;
								$icons[$group][$i]->url   = $url;
								$icons[$group][$i]->name  = $name;
								$icons[$group][$i]->image = $image;
								$icons[$group][$i]->alt   = $alt;
							}
						}
						elseif (StringHelper::check($accessAdd))
						{
							// check access
							if($user->authorise($accessAdd, 'com_ehealthportal') && $dashboard_add)
							{
								$icons[$group][$i]        = new \StdClass;
								$icons[$group][$i]->url   = $url;
								$icons[$group][$i]->name  = $name;
								$icons[$group][$i]->image = $image;
								$icons[$group][$i]->alt   = $alt;
							}
						}
						else
						{
							$icons[$group][$i]        = new \StdClass;
							$icons[$group][$i]->url   = $url;
							$icons[$group][$i]->name  = $name;
							$icons[$group][$i]->image = $image;
							$icons[$group][$i]->alt   = $alt;
						}
					}
					else
					{
						$icons[$group][$i]        = new \StdClass;
						$icons[$group][$i]->url   = $url;
						$icons[$group][$i]->name  = $name;
						$icons[$group][$i]->image = $image;
						$icons[$group][$i]->alt   = $alt;
					}
					$i++;
				}
			}
			else
			{
					$icons[$group][$i] = false;
			}
		}
		return $icons;
	}
}
