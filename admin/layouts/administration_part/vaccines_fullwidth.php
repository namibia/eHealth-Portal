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
	@subpackage		vaccines_fullwidth.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use JCB\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;
use VDM\Joomla\Utilities\StringHelper;

// set the defaults
$items = $displayData->vvyvaccines;
$user = Factory::getApplication()->getIdentity();
$id = $displayData->item->id;
// set the edit URL
$edit = "index.php?option=com_ehealthportal&view=immunisation_vaccine_types&task=immunisation_vaccine_type.edit";
// set a return value
$return = ($id) ? "index.php?option=com_ehealthportal&view=administration_part&layout=edit&id=" . $id : "";
// check for a return value
$jinput = Factory::getApplication()->input;
if ($_return = $jinput->get('return', null, 'base64'))
{
	$return .= "&return=" . $_return;
}
// check if return value was set
if (StringHelper::check($return))
{
	// set the referral values
	$ref = ($id) ? "&ref=administration_part&refid=" . $id . "&return=" . urlencode(base64_encode($return)) : "&return=" . urlencode(base64_encode($return));
}
else
{
	$ref = ($id) ? "&ref=administration_part&refid=" . $id : "";
}
// set the create new URL
$new = "index.php?option=com_ehealthportal&view=immunisation_vaccine_types&task=immunisation_vaccine_type.edit" . $ref;
// set the create new and close URL
$close_new = "index.php?option=com_ehealthportal&view=immunisation_vaccine_types&task=immunisation_vaccine_type.edit";
// load the action object
$can = EhealthportalHelper::getActions('immunisation_vaccine_type');

?>
<div class="form-vertical">
<?php if ($can->get('core.create')): ?>
	<div class="btn-group">
		<a class="btn btn-small btn-success" href="<?php echo $new; ?>"><span class="icon-new icon-white"></span> <?php echo Text::_('COM_EHEALTHPORTAL_NEW'); ?></a>
		<a class="btn btn-small" onclick="Joomla.submitbutton('administration_part.cancel');" href="<?php echo $close_new; ?>"><span class="icon-new"></span> <?php echo Text::_('COM_EHEALTHPORTAL_CLOSE_NEW'); ?></a>
	</div><br /><br />
<?php endif; ?>
<?php if (EhealthportalHelper::checkArray($items)): ?>
<table class="footable table data immunisation_vaccine_types metro-blue" data-page-size="20" data-filter="#filter_immunisation_vaccine_types">
<thead>
	<tr>
		<th data-toggle="true">
			<?php echo Text::_('COM_EHEALTHPORTAL_IMMUNISATION_VACCINE_TYPE_NAME_LABEL'); ?>
		</th>
		<th data-hide="phone">
			<?php echo Text::_('COM_EHEALTHPORTAL_IMMUNISATION_VACCINE_TYPE_ADMINISTRATION_PART_LABEL'); ?>
		</th>
		<th data-hide="phone">
			<?php echo Text::_('COM_EHEALTHPORTAL_IMMUNISATION_VACCINE_TYPE_DESCRIPTION_LABEL'); ?>
		</th>
		<th width="10" data-hide="phone,tablet">
			<?php echo Text::_('COM_EHEALTHPORTAL_IMMUNISATION_VACCINE_TYPE_STATUS'); ?>
		</th>
		<th width="5" data-type="numeric" data-hide="phone,tablet">
			<?php echo Text::_('COM_EHEALTHPORTAL_IMMUNISATION_VACCINE_TYPE_ID'); ?>
		</th>
	</tr>
</thead>
<tbody>
<?php foreach ($items as $i => $item): ?>
	<?php
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
		$userChkOut = Factory::getContainer()->
			get(\Joomla\CMS\User\UserFactoryInterface::class)->
				loadUserById($item->checked_out);
		$canDo = EhealthportalHelper::getActions('immunisation_vaccine_type',$item,'immunisation_vaccine_types');
	?>
	<tr>
		<td>
			<?php if ($canDo->get('core.edit')): ?>
				<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->name); ?></a>
				<?php if ($item->checked_out): ?>
					<?php echo Html::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'immunisation_vaccine_types.', $canCheckin); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo $displayData->escape($item->name); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo $displayData->escape($item->administration_part_name); ?>
		</td>
		<td>
			<?php echo $displayData->escape($item->description); ?>
		</td>
		<?php if ($item->published == 1): ?>
			<td class="center"  data-value="1">
				<span class="status-metro status-published" title="<?php echo Text::_('COM_EHEALTHPORTAL_PUBLISHED');  ?>">
					<?php echo Text::_('COM_EHEALTHPORTAL_PUBLISHED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 0): ?>
			<td class="center"  data-value="2">
				<span class="status-metro status-inactive" title="<?php echo Text::_('COM_EHEALTHPORTAL_INACTIVE');  ?>">
					<?php echo Text::_('COM_EHEALTHPORTAL_INACTIVE'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 2): ?>
			<td class="center"  data-value="3">
				<span class="status-metro status-archived" title="<?php echo Text::_('COM_EHEALTHPORTAL_ARCHIVED');  ?>">
					<?php echo Text::_('COM_EHEALTHPORTAL_ARCHIVED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == -2): ?>
			<td class="center"  data-value="4">
				<span class="status-metro status-trashed" title="<?php echo Text::_('COM_EHEALTHPORTAL_TRASHED');  ?>">
					<?php echo Text::_('COM_EHEALTHPORTAL_TRASHED'); ?>
				</span>
			</td>
		<?php endif; ?>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
<tfoot class="hide-if-no-paging">
	<tr>
		<td colspan="5">
			<div class="pagination pagination-centered"></div>
		</td>
	</tr>
</tfoot>
</table>
<?php else: ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php endif; ?>
</div>
