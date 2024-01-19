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
	@subpackage		default_head.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

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
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_CERVICAL_CANCER_PATIENT_LABEL', 'a.patient', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_CC_VIGINAL_BLEEDING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_CC_V_DISCHARGE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_CC_PERIODS_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_CC_SMOKING_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_CC_SEX_ACTVE_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_CC_SEX_PARTNER_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_PAP_SMEAR_COLLECTION_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_CC_RESULT_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_REFERRAL_LABEL'); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_REASON_LABEL'); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_CERVICAL_CANCER_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo Text::_('COM_EHEALTHPORTAL_CERVICAL_CANCER_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_EHEALTHPORTAL_CERVICAL_CANCER_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>