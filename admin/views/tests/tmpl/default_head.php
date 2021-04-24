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
	@subpackage		default_head.php
	@author			Oh Martin <https://github.com/namibia/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<tr>
	<?php if ($this->canEdit&& $this->canState): ?>
		<th width="1%" class="nowrap center hidden-phone">
			<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
		</th>
		<th width="20" class="nowrap center">
			<?php echo JHtml::_('grid.checkall'); ?>
		</th>
	<?php else: ?>
		<th width="20" class="nowrap center hidden-phone">
			&#9662;
		</th>
		<th width="20" class="nowrap center">
			&#9632;
		</th>
	<?php endif; ?>
	<th class="nowrap" >
			<?php echo JHtml::_('searchtools.sort', 'COM_EHEALTH_PORTAL_TEST_PATIENT_LABEL', 'a.patient', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_URINE_TEST_RESULT_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_GLUCOSE_FIRST_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_GLUCOSE_SECOND_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_HAEMOGLOBIN_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_CHOLESTEROL_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_SYPHILIS_FIRST_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_SYPHILIS_SECOND_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_HEPATITIS_FIRST_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_HEPATITIS_SECOND_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_MALARIA_FIRST_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_MALARIA_SECOND_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_PREGNANCY_FIRST_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_PREGNANCY_SECOND_READING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_REFERRAL_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_REASON_LABEL'); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo JHtml::_('searchtools.sort', 'COM_EHEALTH_PORTAL_TEST_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo JText::_('COM_EHEALTH_PORTAL_TEST_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo JHtml::_('searchtools.sort', 'COM_EHEALTH_PORTAL_TEST_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>