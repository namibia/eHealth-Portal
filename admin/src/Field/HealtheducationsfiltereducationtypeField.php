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

	@version		4.0.0
	@build			19th January, 2024
	@created		13th August, 2020
	@package		eHealth Portal
	@subpackage		HealtheducationsfiltereducationtypeField.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/
namespace JCB\Component\Ehealthportal\Administrator\Field;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Component\ComponentHelper;
use JCB\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;

/**
 * Healtheducationsfiltereducationtype Form Field class for the Ehealthportal component
 */
class HealtheducationsfiltereducationtypeField extends ListField
{
	/**
	 * The healtheducationsfiltereducationtype field type.
	 *
	 * @var        string
	 */
	public $type = 'Healtheducationsfiltereducationtype';

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
		$query->select($db->quoteName('education_type'));
		$query->from($db->quoteName('#__ehealthportal_health_education'));
		$query->order($db->quoteName('education_type') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = [];
		$_filter[] = Html::_('select.option', '', '- ' . Text::_('COM_EHEALTHPORTAL_FILTER_SELECT_TYPE') . ' -');

		if ($_results)
		{
			// get health_educationsmodel
			$_model = EhealthportalHelper::getModel('health_educations');
			$_results = array_unique($_results);
			foreach ($_results as $education_type)
			{
				// Translate the education_type selection
				$_text = $_model->selectionTranslation($education_type,'education_type');
				// Now add the education_type and its text to the options array
				$_filter[] = Html::_('select.option', $education_type, Text::_($_text));
			}
		}
		return $_filter;
	}
}
