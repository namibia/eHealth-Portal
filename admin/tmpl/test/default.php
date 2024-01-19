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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

Html::_('behavior.formvalidator');
Html::_('formbehavior.chosen', 'select');
Html::_('behavior.keepalive');
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;

?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = document.querySelector('body');
	var loadingDiv = document.createElement('div');
	loadingDiv.id = 'loading';
	loadingDiv.style.cssText = "background: rgba(255, 255, 255, .8) url('components/com_ehealthportal/assets/images/import.gif') 50% 15% no-repeat; top: " + (outerDiv.getBoundingClientRect().top + window.pageYOffset) + "px; left: " + (outerDiv.getBoundingClientRect().left + window.pageXOffset) + "px; width: " + outerDiv.offsetWidth + "px; height: " + outerDiv.offsetHeight + "px; position: fixed; opacity: 0.80; -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80); filter: alpha(opacity=80); display: none;";
	outerDiv.appendChild(loadingDiv);
	loadingDiv.style.display = 'block';
	// when page is ready remove and show
	window.addEventListener('load', function() {
		var componentLoader = document.getElementById('ehealthportal_loader');
		if (componentLoader) componentLoader.style.display = 'block';
		loadingDiv.style.display = 'none';
	});
</script>
<div id="ehealthportal_loader" style="display: none;">
<form action="<?php echo Route::_('index.php?option=com_ehealthportal&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo LayoutHelper::render('test.urine_above', $this); ?>
<div class="main-card">

	<?php echo Html::_('uitab.startTabSet', 'testTab', array('active' => 'urine')); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'urine', Text::_('COM_EHEALTHPORTAL_TEST_URINE', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.urine_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'glucose', Text::_('COM_EHEALTHPORTAL_TEST_GLUCOSE', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.glucose_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'haemoglobin', Text::_('COM_EHEALTHPORTAL_TEST_HAEMOGLOBIN', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.haemoglobin_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'cholesterol', Text::_('COM_EHEALTHPORTAL_TEST_CHOLESTEROL', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.cholesterol_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'syphillis', Text::_('COM_EHEALTHPORTAL_TEST_SYPHILLIS', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.syphillis_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'hepatitis_b', Text::_('COM_EHEALTHPORTAL_TEST_HEPATITIS_B', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.hepatitis_b_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'malaria', Text::_('COM_EHEALTHPORTAL_TEST_MALARIA', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.malaria_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'pregnancy', Text::_('COM_EHEALTHPORTAL_TEST_PREGNANCY', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<?php echo LayoutHelper::render('test.pregnancy_left', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php echo Html::_('uitab.addTab', 'testTab', 'referrals', Text::_('COM_EHEALTHPORTAL_TEST_REFERRALS', true)); ?>
		<div class="row">
			<div class="col-md-6">
				<?php echo LayoutHelper::render('test.referrals_left', $this); ?>
			</div>
			<div class="col-md-6">
				<?php echo LayoutHelper::render('test.referrals_right', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'testTab'; ?>
	<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('core.edit.created_by') || $this->canDo->get('core.edit.created') || $this->canDo->get('core.edit.state') || ($this->canDo->get('core.delete') && $this->canDo->get('core.edit.state'))) : ?>
	<?php echo Html::_('uitab.addTab', 'testTab', 'publishing', Text::_('COM_EHEALTHPORTAL_TEST_PUBLISHING', true)); ?>
		<div class="row">
			<div class="col-md-6">
				<?php echo LayoutHelper::render('test.publishing', $this); ?>
			</div>
			<div class="col-md-6">
				<?php echo LayoutHelper::render('test.publlshing', $this); ?>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo Html::_('uitab.addTab', 'testTab', 'permissions', Text::_('COM_EHEALTHPORTAL_TEST_PERMISSION', true)); ?>
		<div class="row">
			<div class="col-md-12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo Html::_('uitab.endTab'); ?>
	<?php endif; ?>

	<?php echo Html::_('uitab.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="test.edit" />
		<?php echo Html::_('form.token'); ?>
	</div>
</div>
</form>
</div>
