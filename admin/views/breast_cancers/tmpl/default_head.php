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
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_BREAST_CANCER_PATIENT_LABEL', 'a.patient', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_AGE_RANGE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_FAMILY_HISTORY_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_RACE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_BREASTFEEDING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_PREG_FREQ_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_PREG_AGE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_HISTORY_HRT_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_REG_EXERCISE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_OVERWEIGHT_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_LUMP_NEAR_BREAST_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_DIMPLING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_INWARD_NIPPLE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_NIPPLE_DISCHARGE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_ABNORMAL_SKIN_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_BC_BREAST_SHAPE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_REFERRAL_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_REASON_LABEL'); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_BREAST_CANCER_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo Text::_('COM_EHEALTHPORTAL_BREAST_CANCER_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_BREAST_CANCER_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>