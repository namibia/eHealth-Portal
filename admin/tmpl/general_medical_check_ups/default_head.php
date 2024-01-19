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
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;

?>
<tr>
	<?php if ($this->canEdit&& $this->canState): ?>
		<th width="1%" class="nowrap center hidden-phone">
			<?php echo Html::_('searchtools.sort', '', 'a.ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
		</th>
		<th width="20" class="nowrap center">
			<?php echo Html::_('grid.checkall'); ?>
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
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_PATIENT_LABEL', 'a.patient', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_DIASTOLIC_ONE_LABEL', 'a.bp_diastolic_one', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_SYSTOLIC_ONE_LABEL', 'a.bp_systolic_one', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_TEMP_ONE_LABEL', 'a.temp_one', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_WEIGHT_LABEL', 'a.weight', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_PULSE_LABEL', 'a.pulse', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_CHRONIC_MEDICATION_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_DIASTOLIC_TWO_LABEL', 'a.bp_diastolic_two', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_SYSTOLIC_TWO_LABEL', 'a.bp_systolic_two', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_TEMP_TWO_LABEL', 'a.temp_two', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_HEIGHT_LABEL', 'a.height', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BMI_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_COMPLAINT_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_INVESTIGATIONS_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_NOTES_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_DIAGNOSIS_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_REFERRAL_LABEL', 'h.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_REASON_LABEL'); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>