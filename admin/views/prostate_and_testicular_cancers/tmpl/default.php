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
	@subpackage		default.php
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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
Html::_('behavior.multiselect');
Html::_('dropdown.init');
Html::_('formbehavior.chosen', 'select');
Html::_('formbehavior.chosen', '.multipleAccessLevels', null, ['placeholder_text_multiple' => '- ' . Text::_('COM_EHEALTHPORTAL_FILTER_SELECT_ACCESS') . ' -']);

if ($this->saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_ehealthportal&task=prostate_and_testicular_cancers.saveOrderAjax&tmpl=component';
	Html::_('sortablelist.sortable', 'prostate_and_testicular_cancerList', 'adminForm', strtolower($this->listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo Route::_('index.php?option=com_ehealthportal&view=prostate_and_testicular_cancers'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
<?php
	// Add the searchtools
	echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
?>
<?php if (empty($this->items)): ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<table class="table table-striped" id="prostate_and_testicular_cancerList">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
	<?php // Load the batch processing form. ?>
	<?php if ($this->canCreate && $this->canEdit) : ?>
		<?php echo Html::_(
			'bootstrap.renderModal',
			'collapseModal',
			array(
				'title' => Text::_('COM_EHEALTHPORTAL_PROSTATE_AND_TESTICULAR_CANCERS_BATCH_OPTIONS'),
				'footer' => $this->loadTemplate('batch_footer')
			),
			$this->loadTemplate('batch_body')
		); ?>
	<?php endif; ?>
	<input type="hidden" name="boxchecked" value="0" />
	</div>
<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<?php echo Html::_('form.token'); ?>
</form>
