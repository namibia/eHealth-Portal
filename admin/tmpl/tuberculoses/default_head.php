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

	@version		4.0.x
	@build			19th January, 2024
	@created		13th August, 2020
	@package		eHealth Portal
	@subpackage		default_head.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

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
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_TUBERCULOSIS_PATIENT_LABEL', 'a.patient', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_RECURRING_NIGHT_SWEATS_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_TB_FEVER_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_PERSISTENT_COUGH_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_BLOOD_STREAKED_SPUTUM_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_UNUSUAL_TIREDNESS_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_PAIN_IN_CHEST_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_SHORTNESS_OF_BREATH_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_DIAGNOSED_WITH_DISEASE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_TB_EXPOSED_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_TB_TREATMENT_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_DATE_OF_TREATMENT_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_TREATING_DHC_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_SPUTUM_COLLECTION_ONE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_TB_REASON_ONE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_SPUTUM_RESULT_ONE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_REFERRED_SECOND_SPUTUM_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_TB_REASON_TWO_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_SPUTUM_RESULT_TWO_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_WEIGHT_LOSS_WDIETING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_REFERRAL_LABEL'); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_TUBERCULOSIS_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo Text::_('COM_EHEALTHPORTAL_TUBERCULOSIS_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_TUBERCULOSIS_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>