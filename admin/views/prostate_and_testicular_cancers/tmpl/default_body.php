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
	@subpackage		default_body.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;

$edit = "index.php?option=com_ehealthportal&view=prostate_and_testicular_cancers&task=prostate_and_testicular_cancer.edit";

?>
<?php foreach ($this->items as $i => $item): ?>
	<?php
		$canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
		$userChkOut = Factory::getUser($item->checked_out);
		$canDo = EhealthportalHelper::getActions('prostate_and_testicular_cancer',$item,'prostate_and_testicular_cancers');
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="order nowrap center hidden-phone">
		<?php if ($canDo->get('core.edit.state')): ?>
			<?php
				$iconClass = '';
				if (!$this->saveOrder)
				{
					$iconClass = ' inactive tip-top" hasTooltip" title="' . Html::tooltipText('JORDERINGDISABLED');
				}
			?>
			<span class="sortable-handler<?php echo $iconClass; ?>">
				<i class="icon-menu"></i>
			</span>
			<?php if ($this->saveOrder) : ?>
				<input type="text" style="display:none" name="order[]" size="5"
				value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
			<?php endif; ?>
		<?php else: ?>
			&#8942;
		<?php endif; ?>
		</td>
		<td class="nowrap center">
		<?php if ($canDo->get('core.edit')): ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo Html::_('grid.id', $i, $item->id); ?>
					<?php else: ?>
						&#9633;
					<?php endif; ?>
				<?php else: ?>
					<?php echo Html::_('grid.id', $i, $item->id); ?>
				<?php endif; ?>
		<?php else: ?>
			&#9633;
		<?php endif; ?>
		</td>
		<td class="nowrap">
			<div class="name">
				<?php if ($canDo->get('core.edit')): ?>
					<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?>"><?php echo Factory::getUser((int)$item->patient)->name; ?></a>
					<?php if ($item->checked_out): ?>
						<?php echo Html::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'prostate_and_testicular_cancers.', $canCheckin); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo Factory::getUser((int)$item->patient)->name; ?>
				<?php endif; ?>
			</div>
		</td>
		<td class="hidden-phone">
			<div>
			<?php echo Text::_($item->ptc_age); ?><br />
			<?php echo $this->escape($item->txt_ptc_age); ?>
			</div>
		</td>
		<td class="hidden-phone">
			<div>
			<?php echo Text::_($item->ptc_fam_history); ?><br />
			<?php echo $this->escape($item->txt_ptc_fam_history); ?>
			</div>
		</td>
		<td class="hidden-phone">
			<div>
			<?php echo Text::_($item->ptc_diet); ?><br />
			<?php echo $this->escape($item->txt_ptc_diet); ?>
			</div>
		</td>
		<td class="hidden-phone">
			<div>
			<?php echo Text::_($item->ptc_phy_activity); ?><br />
			<?php echo $this->escape($item->txt_ptc_phy_activity); ?>
			</div>
		</td>
		<td class="hidden-phone">
			<div>
			<?php echo Text::_($item->ptc_overweight); ?><br />
			<?php echo $this->escape($item->txt_ptc_overweight); ?>
			</div>
		</td>
		<td class="hidden-phone">
			<div>
			<?php echo Text::_($item->ptc_urinate); ?><br />
			<?php echo $this->escape($item->txt_ptc_urinate); ?>
			</div>
		</td>
		<td class="hidden-phone">
			<div>
			<?php echo Text::_($item->ptc_urine_freq); ?><br />
			<?php echo $this->escape($item->txt_ptc_urine_freq); ?>
			</div>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->referral_name); ?>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->reason); ?>
		</td>
		<td class="center">
		<?php if ($canDo->get('core.edit.state')) : ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo Html::_('jgrid.published', $item->published, $i, 'prostate_and_testicular_cancers.', true, 'cb'); ?>
					<?php else: ?>
						<?php echo Html::_('jgrid.published', $item->published, $i, 'prostate_and_testicular_cancers.', false, 'cb'); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo Html::_('jgrid.published', $item->published, $i, 'prostate_and_testicular_cancers.', true, 'cb'); ?>
				<?php endif; ?>
		<?php else: ?>
			<?php echo Html::_('jgrid.published', $item->published, $i, 'prostate_and_testicular_cancers.', false, 'cb'); ?>
		<?php endif; ?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>