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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;

$edit = "index.php?option=com_ehealthportal&view=breast_cancers&task=breast_cancer.edit";

?>
<?php foreach ($this->items as $i => $item): ?>
	<?php
		$canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
		$userChkOut = Factory::getUser($item->checked_out);
		$canDo = EhealthportalHelper::getActions('breast_cancer',$item,'breast_cancers');
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
						<?php echo Html::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'breast_cancers.', $canCheckin); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo Factory::getUser((int)$item->patient)->name; ?>
				<?php endif; ?>
			</div>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_age_range); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_family_history); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_race); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_breastfeeding); ?>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->bc_preg_freq); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_preg_age); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_history_hrt); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_reg_exercise); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_overweight); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_lump_near_breast); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_dimpling); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_inward_nipple); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_nipple_discharge); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_abnormal_skin); ?>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->bc_breast_shape); ?>
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
						<?php echo Html::_('jgrid.published', $item->published, $i, 'breast_cancers.', true, 'cb'); ?>
					<?php else: ?>
						<?php echo Html::_('jgrid.published', $item->published, $i, 'breast_cancers.', false, 'cb'); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo Html::_('jgrid.published', $item->published, $i, 'breast_cancers.', true, 'cb'); ?>
				<?php endif; ?>
		<?php else: ?>
			<?php echo Html::_('jgrid.published', $item->published, $i, 'breast_cancers.', false, 'cb'); ?>
		<?php endif; ?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>