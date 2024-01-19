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
	@subpackage		EhealthportalInstallerScript.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\Filesystem\Folder;
use Joomla\Database\DatabaseInterface;

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Script File of Ehealthportal Component
 *
 * @since  3.6
 */
class Com_EhealthportalInstallerScript implements InstallerScriptInterface
{
	/**
	 * The CMS Application.
	 *
	 * @var   CMSApplication
	 * @since 4.4.2
	 */
	protected CMSApplication $app;

	/**
	 * The database class.
	 *
	 * @since 4.4.2
	 */
	protected $db;

	/**
	 * The version number of the extension.
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $release;

	/**
	 * The table the parameters are stored in.
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $paramTable;

	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $extension;

	/**
	 * A list of files to be deleted
	 *
	 * @var   array
	 * @since  3.6
	 */
	protected $deleteFiles = [];

	/**
	 * A list of folders to be deleted
	 *
	 * @var   array
	 * @since  3.6
	 */
	protected $deleteFolders = [];

	/**
	 * A list of CLI script files to be copied to the cli directory
	 *
	 * @var   array
	 * @since  3.6
	 */
	protected $cliScriptFiles = [];

	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $minimumPhp;

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $minimumJoomla;

	/**
	 * Extension script constructor.
	 *
	 * @since   3.0.0
	 */
	public function __construct()
	{
		$this->minimumJoomla = '4.3';
		$this->minimumPhp = JOOMLA_MINIMUM_PHP;
		$this->app = Factory::getApplication();
		$this->db = Factory::getContainer()->get(DatabaseInterface::class);

		// check if the files exist
		if (is_file(JPATH_ROOT . '/administrator/components/com_ehealthportal/ehealthportal.php'))
		{
			// remove Joomla 3 files
			$this->deleteFiles = [
				'/administrator/components/com_ehealthportal/ehealthportal.php',
				'/administrator/components/com_ehealthportal/controller.php',
				'/components/com_ehealthportal/ehealthportal.php',
				'/components/com_ehealthportal/controller.php',
				'/components/com_ehealthportal/router.php',
			];
		}

		// check if the Folders exist
		if (is_dir(JPATH_ROOT . '/administrator/components/com_ehealthportal/modules'))
		{
			// remove Joomla 3 folder
			$this->deleteFolders = [
				'/administrator/components/com_ehealthportal/controllers',
				'/administrator/components/com_ehealthportal/helpers',
				'/administrator/components/com_ehealthportal/modules',
				'/administrator/components/com_ehealthportal/tables',
				'/administrator/components/com_ehealthportal/views',
				'/components/com_ehealthportal/controllers',
				'/components/com_ehealthportal/helpers',
				'/components/com_ehealthportal/modules',
				'/components/com_ehealthportal/views',
			];
		}
	}

	/**
	 * Function called after the extension is installed.
	 *
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function install(InstallerAdapter $adapter): bool {return true;}

	/**
	 * Function called after the extension is updated.
	 *
	 * @param   InstallerAdapter   $adapter   The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function update(InstallerAdapter $adapter): bool {return true;}

	/**
	 * Function called after the extension is uninstalled.
	 *
	 * @param   InstallerAdapter   $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function uninstall(InstallerAdapter $adapter): bool
	{
		// Remove Related Component Data.

		// Remove Payment Data
		$this->removeViewData("com_ehealthportal.payment");

		// Remove General medical check up Data
		$this->removeViewData("com_ehealthportal.general_medical_check_up");

		// Remove Antenatal care Data
		$this->removeViewData("com_ehealthportal.antenatal_care");

		// Remove Immunisation Data
		$this->removeViewData("com_ehealthportal.immunisation");

		// Remove Vmmc Data
		$this->removeViewData("com_ehealthportal.vmmc");

		// Remove Prostate and testicular cancer Data
		$this->removeViewData("com_ehealthportal.prostate_and_testicular_cancer");

		// Remove Tuberculosis Data
		$this->removeViewData("com_ehealthportal.tuberculosis");

		// Remove Hiv counseling and testing Data
		$this->removeViewData("com_ehealthportal.hiv_counseling_and_testing");

		// Remove Family planning Data
		$this->removeViewData("com_ehealthportal.family_planning");

		// Remove Health education Data
		$this->removeViewData("com_ehealthportal.health_education");

		// Remove Cervical cancer Data
		$this->removeViewData("com_ehealthportal.cervical_cancer");

		// Remove Breast cancer Data
		$this->removeViewData("com_ehealthportal.breast_cancer");

		// Remove Test Data
		$this->removeViewData("com_ehealthportal.test");

		// Remove Foetal lie Data
		$this->removeViewData("com_ehealthportal.foetal_lie");

		// Remove Immunisation vaccine type Data
		$this->removeViewData("com_ehealthportal.immunisation_vaccine_type");

		// Remove Foetal engagement Data
		$this->removeViewData("com_ehealthportal.foetal_engagement");

		// Remove Foetal presentation Data
		$this->removeViewData("com_ehealthportal.foetal_presentation");

		// Remove Testing reason Data
		$this->removeViewData("com_ehealthportal.testing_reason");

		// Remove Counseling type Data
		$this->removeViewData("com_ehealthportal.counseling_type");

		// Remove Health education topic Data
		$this->removeViewData("com_ehealthportal.health_education_topic");

		// Remove Immunisation type Data
		$this->removeViewData("com_ehealthportal.immunisation_type");

		// Remove Strength Data
		$this->removeViewData("com_ehealthportal.strength");

		// Remove Referral Data
		$this->removeViewData("com_ehealthportal.referral");

		// Remove Planning type Data
		$this->removeViewData("com_ehealthportal.planning_type");

		// Remove Diagnosis type Data
		$this->removeViewData("com_ehealthportal.diagnosis_type");

		// Remove Nonpay reason Data
		$this->removeViewData("com_ehealthportal.nonpay_reason");

		// Remove Medication Data
		$this->removeViewData("com_ehealthportal.medication");

		// Remove Payment type Data
		$this->removeViewData("com_ehealthportal.payment_type");

		// Remove Administration part Data
		$this->removeViewData("com_ehealthportal.administration_part");

		// Remove Site Data
		$this->removeViewData("com_ehealthportal.site");

		// Remove Unit Data
		$this->removeViewData("com_ehealthportal.unit");

		// Remove Clinic Data
		$this->removeViewData("com_ehealthportal.clinic");

		// Remove Asset Data.
		$this->removeAssetData();

		// Revert the assets table rules column back to the default.
		$this->removeDatabaseAssetsRulesFix();

		// Remove component from action logs extensions table.
		$this->removeActionLogsExtensions();

		// Remove Payment from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.payment');

		// Remove General_medical_check_up from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.general_medical_check_up');

		// Remove Antenatal_care from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.antenatal_care');

		// Remove Immunisation from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.immunisation');

		// Remove Vmmc from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.vmmc');

		// Remove Prostate_and_testicular_cancer from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.prostate_and_testicular_cancer');

		// Remove Tuberculosis from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.tuberculosis');

		// Remove Hiv_counseling_and_testing from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.hiv_counseling_and_testing');

		// Remove Family_planning from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.family_planning');

		// Remove Health_education from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.health_education');

		// Remove Cervical_cancer from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.cervical_cancer');

		// Remove Breast_cancer from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.breast_cancer');

		// Remove Test from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.test');

		// Remove Foetal_lie from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.foetal_lie');

		// Remove Immunisation_vaccine_type from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.immunisation_vaccine_type');

		// Remove Foetal_engagement from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.foetal_engagement');

		// Remove Foetal_presentation from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.foetal_presentation');

		// Remove Testing_reason from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.testing_reason');

		// Remove Counseling_type from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.counseling_type');

		// Remove Health_education_topic from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.health_education_topic');

		// Remove Immunisation_type from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.immunisation_type');

		// Remove Strength from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.strength');

		// Remove Referral from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.referral');

		// Remove Planning_type from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.planning_type');

		// Remove Diagnosis_type from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.diagnosis_type');

		// Remove Nonpay_reason from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.nonpay_reason');

		// Remove Medication from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.medication');

		// Remove Payment_type from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.payment_type');

		// Remove Administration_part from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.administration_part');

		// Remove Site from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.site');

		// Remove Unit from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.unit');

		// Remove Clinic from action logs config table.
		$this->removeActionLogConfig('com_ehealthportal.clinic');
		// little notice as after service, in case of bad experience with component.
		echo '<div style="background-color: #fff;" class="alert alert-info">
		<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:joomla@vdm.io">joomla@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://git.vdm.dev/joomla/eHealth-Portal" target="_blank">https://git.vdm.dev/joomla/eHealth-Portal</a> today!</p></div>';

		return true;
	}

	/**
	 * Function called before extension installation/update/removal procedure commences.
	 *
	 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function preflight(string $type, InstallerAdapter $adapter): bool
	{
		// Check for the minimum PHP version before continuing
		if (!empty($this->minimumPhp) && version_compare(PHP_VERSION, $this->minimumPhp, '<'))
		{
			Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhp), Log::WARNING, 'jerror');

			return false;
		}

		// Check for the minimum Joomla version before continuing
		if (!empty($this->minimumJoomla) && version_compare(JVERSION, $this->minimumJoomla, '<'))
		{
			Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomla), Log::WARNING, 'jerror');

			return false;
		}

		// Extension manifest file version
		$this->extension = $adapter->getName();
		$this->release   = $adapter->getManifest()->version;

		// do any updates needed
		if ($type === 'update')
		{
		}

		// do any install needed
		if ($type === 'install')
		{
		}

		return true;
	}

	/**
	 * Function called after extension installation/update/removal procedure commences.
	 *
	 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function postflight(string $type, InstallerAdapter $adapter): bool
	{
		// We check if we have dynamic folders to copy
		$this->moveFolders($adapter);

		// set the default component settings
		if ($type === 'install')
		{

			// Install Payment Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Payment',
				// typeAlias
				'com_ehealthportal.payment',
				// table
				'{"special": {"dbtable": "#__ehealthportal_payment","key": "id","type": "PaymentTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","payment_category":"payment_category","payment_type":"payment_type","payment_amount":"payment_amount","nonpay_reason":"nonpay_reason","receipt_no":"receipt_no","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","payment_type","nonpay_reason"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "payment_type","targetTable": "#__ehealthportal_payment_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "nonpay_reason","targetTable": "#__ehealthportal_nonpay_reason","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install General medical check up Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal General_medical_check_up',
				// typeAlias
				'com_ehealthportal.general_medical_check_up',
				// table
				'{"special": {"dbtable": "#__ehealthportal_general_medical_check_up","key": "id","type": "General_medical_check_upTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bp_diastolic_one":"bp_diastolic_one","bp_systolic_one":"bp_systolic_one","temp_one":"temp_one","weight":"weight","pulse":"pulse","chronic_medication":"chronic_medication","bp_diastolic_two":"bp_diastolic_two","bp_systolic_two":"bp_systolic_two","temp_two":"temp_two","height":"height","bmi":"bmi","complaint":"complaint","investigations":"investigations","notes":"notes","diagnosis":"diagnosis","referral":"referral","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/general_medical_check_up.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bp_diastolic_one","bp_systolic_one","pulse","bp_diastolic_two","bp_systolic_two","diagnosis","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_diagnosis_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Antenatal care Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Antenatal_care',
				// typeAlias
				'com_ehealthportal.antenatal_care',
				// table
				'{"special": {"dbtable": "#__ehealthportal_antenatal_care","key": "id","type": "Antenatal_careTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","foetal_lie":"foetal_lie","foetal_presentation":"foetal_presentation","foetal_engagement":"foetal_engagement","foetal_heart_rate":"foetal_heart_rate","foetal_movements":"foetal_movements","guid":"guid","caesarean_sections":"caesarean_sections","last_menstrual_period":"last_menstrual_period","normal_births":"normal_births","still_births":"still_births","miscarriages":"miscarriages","live_births":"live_births","pregnancies_excl":"pregnancies_excl"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/antenatal_care.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","foetal_lie","foetal_presentation","foetal_engagement","foetal_heart_rate","caesarean_sections","normal_births","still_births","miscarriages","live_births","pregnancies_excl"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_lie","targetTable": "#__ehealthportal_foetal_lie","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_presentation","targetTable": "#__ehealthportal_foetal_presentation","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_engagement","targetTable": "#__ehealthportal_foetal_engagement","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Immunisation Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Immunisation',
				// typeAlias
				'com_ehealthportal.immunisation',
				// table
				'{"special": {"dbtable": "#__ehealthportal_immunisation","key": "id","type": "ImmunisationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","referral":"referral","immunisation_up_to_date":"immunisation_up_to_date","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/immunisation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Vmmc Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Vmmc',
				// typeAlias
				'com_ehealthportal.vmmc',
				// table
				'{"special": {"dbtable": "#__ehealthportal_vmmc","key": "id","type": "VmmcTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","are_you_circumcised":"are_you_circumcised","info_ben_vmcc":"info_ben_vmcc","interested_in_vmmc":"interested_in_vmmc","vmmc_gender":"vmmc_gender","reason":"reason","referral":"referral","guid":"guid","partner_circumcised":"partner_circumcised"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/vmmc.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Prostate and testicular cancer Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Prostate_and_testicular_cancer',
				// typeAlias
				'com_ehealthportal.prostate_and_testicular_cancer',
				// table
				'{"special": {"dbtable": "#__ehealthportal_prostate_and_testicular_cancer","key": "id","type": "Prostate_and_testicular_cancerTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","ptc_age":"ptc_age","ptc_fam_history":"ptc_fam_history","ptc_diet":"ptc_diet","ptc_phy_activity":"ptc_phy_activity","ptc_overweight":"ptc_overweight","ptc_urinate":"ptc_urinate","ptc_urine_freq":"ptc_urine_freq","referral":"referral","reason":"reason","txt_ptc_urine_freq":"txt_ptc_urine_freq","txt_ptc_urinate":"txt_ptc_urinate","txt_ptc_age":"txt_ptc_age","txt_ptc_phy_activity":"txt_ptc_phy_activity","txt_ptc_diet":"txt_ptc_diet","txt_ptc_fam_history":"txt_ptc_fam_history","guid":"guid","txt_ptc_overweight":"txt_ptc_overweight"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/prostate_and_testicular_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Tuberculosis Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Tuberculosis',
				// typeAlias
				'com_ehealthportal.tuberculosis',
				// table
				'{"special": {"dbtable": "#__ehealthportal_tuberculosis","key": "id","type": "TuberculosisTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","recurring_night_sweats":"recurring_night_sweats","tb_fever":"tb_fever","persistent_cough":"persistent_cough","blood_streaked_sputum":"blood_streaked_sputum","unusual_tiredness":"unusual_tiredness","pain_in_chest":"pain_in_chest","shortness_of_breath":"shortness_of_breath","diagnosed_with_disease":"diagnosed_with_disease","tb_exposed":"tb_exposed","tb_treatment":"tb_treatment","date_of_treatment":"date_of_treatment","treating_dhc":"treating_dhc","sputum_collection_one":"sputum_collection_one","tb_reason_one":"tb_reason_one","sputum_result_one":"sputum_result_one","referred_second_sputum":"referred_second_sputum","tb_reason_two":"tb_reason_two","sputum_result_two":"sputum_result_two","weight_loss_wdieting":"weight_loss_wdieting","reason":"reason","guid":"guid","referral":"referral"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/tuberculosis.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Hiv counseling and testing Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Hiv_counseling_and_testing',
				// typeAlias
				'com_ehealthportal.hiv_counseling_and_testing',
				// table
				'{"special": {"dbtable": "#__ehealthportal_hiv_counseling_and_testing","key": "id","type": "Hiv_counseling_and_testingTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","counseling_type":"counseling_type","testing_reason":"testing_reason","last_test_date":"last_test_date","prev_test_result":"prev_test_result","test_result_one":"test_result_one","test_result_two":"test_result_two","final_test_result":"final_test_result","eqa":"eqa","referral":"referral","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/hiv_counseling_and_testing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","testing_reason","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "testing_reason","targetTable": "#__ehealthportal_testing_reason","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Family planning Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Family_planning',
				// typeAlias
				'com_ehealthportal.family_planning',
				// table
				'{"special": {"dbtable": "#__ehealthportal_family_planning","key": "id","type": "Family_planningTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "diagnosis","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","diagnosis":"diagnosis","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/family_planning.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","diagnosis"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_planning_type","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Health education Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Health_education',
				// typeAlias
				'com_ehealthportal.health_education',
				// table
				'{"special": {"dbtable": "#__ehealthportal_health_education","key": "id","type": "Health_educationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"education_type":"education_type","health_education_topic":"health_education_topic","patient":"patient","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/health_education.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","health_education_topic"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "health_education_topic","targetTable": "#__ehealthportal_health_education_topic","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Cervical cancer Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Cervical_cancer',
				// typeAlias
				'com_ehealthportal.cervical_cancer',
				// table
				'{"special": {"dbtable": "#__ehealthportal_cervical_cancer","key": "id","type": "Cervical_cancerTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","cc_viginal_bleeding":"cc_viginal_bleeding","cc_v_discharge":"cc_v_discharge","cc_periods":"cc_periods","cc_smoking":"cc_smoking","cc_sex_actve":"cc_sex_actve","cc_sex_partner":"cc_sex_partner","pap_smear_collection":"pap_smear_collection","cc_result":"cc_result","referral":"referral","reason":"reason","cc_reason":"cc_reason","txt_cc_v_discharge":"txt_cc_v_discharge","txt_cc_sex_partner":"txt_cc_sex_partner","txt_cc_viginal_bleeding":"txt_cc_viginal_bleeding","txt_cc_smoking":"txt_cc_smoking","txt_cc_periods":"txt_cc_periods","guid":"guid","txt_cc_sex_actve":"txt_cc_sex_actve"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/cervical_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Breast cancer Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Breast_cancer',
				// typeAlias
				'com_ehealthportal.breast_cancer',
				// table
				'{"special": {"dbtable": "#__ehealthportal_breast_cancer","key": "id","type": "Breast_cancerTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bc_age_range":"bc_age_range","bc_family_history":"bc_family_history","bc_race":"bc_race","bc_breastfeeding":"bc_breastfeeding","bc_preg_freq":"bc_preg_freq","bc_preg_age":"bc_preg_age","bc_history_hrt":"bc_history_hrt","bc_reg_exercise":"bc_reg_exercise","bc_overweight":"bc_overweight","bc_lump_near_breast":"bc_lump_near_breast","bc_dimpling":"bc_dimpling","bc_inward_nipple":"bc_inward_nipple","bc_nipple_discharge":"bc_nipple_discharge","bc_abnormal_skin":"bc_abnormal_skin","bc_breast_shape":"bc_breast_shape","referral":"referral","reason":"reason","guid":"guid","txt_bc_breast_shape":"txt_bc_breast_shape","txt_bc_abnormal_skin":"txt_bc_abnormal_skin","txt_bc_nipple_discharge":"txt_bc_nipple_discharge","txt_bc_dimpling":"txt_bc_dimpling","txt_bc_lump_near_breast":"txt_bc_lump_near_breast","txt_bc_inward_nipple":"txt_bc_inward_nipple"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/breast_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bc_preg_freq","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Test Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Test',
				// typeAlias
				'com_ehealthportal.test',
				// table
				'{"special": {"dbtable": "#__ehealthportal_test","key": "id","type": "TestTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","urine_test_result":"urine_test_result","glucose_first_reading":"glucose_first_reading","glucose_second_reading":"glucose_second_reading","haemoglobin_reading":"haemoglobin_reading","cholesterol_reading":"cholesterol_reading","syphilis_first_reading":"syphilis_first_reading","syphilis_second_reading":"syphilis_second_reading","hepatitis_first_reading":"hepatitis_first_reading","hepatitis_second_reading":"hepatitis_second_reading","malaria_first_reading":"malaria_first_reading","malaria_second_reading":"malaria_second_reading","pregnancy_first_reading":"pregnancy_first_reading","pregnancy_second_reading":"pregnancy_second_reading","referral":"referral","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/test.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","glucose_first_reading","glucose_second_reading","haemoglobin_reading","cholesterol_reading","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Foetal lie Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Foetal_lie',
				// typeAlias
				'com_ehealthportal.foetal_lie',
				// table
				'{"special": {"dbtable": "#__ehealthportal_foetal_lie","key": "id","type": "Foetal_lieTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/foetal_lie.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Immunisation vaccine type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Immunisation_vaccine_type',
				// typeAlias
				'com_ehealthportal.immunisation_vaccine_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_immunisation_vaccine_type","key": "id","type": "Immunisation_vaccine_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","administration_part":"administration_part","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/immunisation_vaccine_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealthportal_administration_part","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Foetal engagement Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Foetal_engagement',
				// typeAlias
				'com_ehealthportal.foetal_engagement',
				// table
				'{"special": {"dbtable": "#__ehealthportal_foetal_engagement","key": "id","type": "Foetal_engagementTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/foetal_engagement.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Foetal presentation Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Foetal_presentation',
				// typeAlias
				'com_ehealthportal.foetal_presentation',
				// table
				'{"special": {"dbtable": "#__ehealthportal_foetal_presentation","key": "id","type": "Foetal_presentationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/foetal_presentation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Testing reason Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Testing_reason',
				// typeAlias
				'com_ehealthportal.testing_reason',
				// table
				'{"special": {"dbtable": "#__ehealthportal_testing_reason","key": "id","type": "Testing_reasonTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/testing_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Counseling type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Counseling_type',
				// typeAlias
				'com_ehealthportal.counseling_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_counseling_type","key": "id","type": "Counseling_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/counseling_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Health education topic Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Health_education_topic',
				// typeAlias
				'com_ehealthportal.health_education_topic',
				// table
				'{"special": {"dbtable": "#__ehealthportal_health_education_topic","key": "id","type": "Health_education_topicTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/health_education_topic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Immunisation type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Immunisation_type',
				// typeAlias
				'com_ehealthportal.immunisation_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_immunisation_type","key": "id","type": "Immunisation_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/immunisation_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Strength Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Strength',
				// typeAlias
				'com_ehealthportal.strength',
				// table
				'{"special": {"dbtable": "#__ehealthportal_strength","key": "id","type": "StrengthTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/strength.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Referral Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Referral',
				// typeAlias
				'com_ehealthportal.referral',
				// table
				'{"special": {"dbtable": "#__ehealthportal_referral","key": "id","type": "ReferralTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/referral.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Planning type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Planning_type',
				// typeAlias
				'com_ehealthportal.planning_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_planning_type","key": "id","type": "Planning_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/planning_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Diagnosis type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Diagnosis_type',
				// typeAlias
				'com_ehealthportal.diagnosis_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_diagnosis_type","key": "id","type": "Diagnosis_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/diagnosis_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Nonpay reason Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Nonpay_reason',
				// typeAlias
				'com_ehealthportal.nonpay_reason',
				// table
				'{"special": {"dbtable": "#__ehealthportal_nonpay_reason","key": "id","type": "Nonpay_reasonTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/nonpay_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Medication Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Medication',
				// typeAlias
				'com_ehealthportal.medication',
				// table
				'{"special": {"dbtable": "#__ehealthportal_medication","key": "id","type": "MedicationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/medication.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Payment type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Payment_type',
				// typeAlias
				'com_ehealthportal.payment_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_payment_type","key": "id","type": "Payment_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/payment_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Administration part Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Administration_part',
				// typeAlias
				'com_ehealthportal.administration_part',
				// table
				'{"special": {"dbtable": "#__ehealthportal_administration_part","key": "id","type": "Administration_partTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/administration_part.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Site Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Site',
				// typeAlias
				'com_ehealthportal.site',
				// table
				'{"special": {"dbtable": "#__ehealthportal_site","key": "id","type": "SiteTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "site_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"site_name":"site_name","description":"description","site_region":"site_region","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/site.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Unit Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Unit',
				// typeAlias
				'com_ehealthportal.unit',
				// table
				'{"special": {"dbtable": "#__ehealthportal_unit","key": "id","type": "UnitTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/unit.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Install Clinic Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Clinic',
				// typeAlias
				'com_ehealthportal.clinic',
				// table
				'{"special": {"dbtable": "#__ehealthportal_clinic","key": "id","type": "ClinicTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "clinic_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"clinic_name":"clinic_name","description":"description","guid":"guid","clinic_type":"clinic_type","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);


			// Fix the assets table rules column size.
			$this->setDatabaseAssetsRulesFix(43040, "TEXT");
			// Install the global extension params.
			$this->setExtensionsParams(
				'{"autorName":"Llewellyn van der Merwe","autorEmail":"joomla@vdm.io","check_in":"-1 day","save_history":"1","history_limit":"10"}'
			);


			echo '<div style="background-color: #fff;" class="alert alert-info"><a target="_blank" href="https://git.vdm.dev/joomla/eHealth-Portal" title="eHealth Portal">
				<img src="components/com_ehealthportal/assets/images/vdm-component.jpg"/>
				</a></div>';

			// Add component to the action logs extensions table.
			$this->setActionLogsExtensions();

			// Add Payment to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PAYMENT',
				// typeAlias
				'com_ehealthportal.payment',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_payment',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add General_medical_check_up to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'GENERAL_MEDICAL_CHECK_UP',
				// typeAlias
				'com_ehealthportal.general_medical_check_up',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_general_medical_check_up',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Antenatal_care to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'ANTENATAL_CARE',
				// typeAlias
				'com_ehealthportal.antenatal_care',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_antenatal_care',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Immunisation to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'IMMUNISATION',
				// typeAlias
				'com_ehealthportal.immunisation',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_immunisation',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Vmmc to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'VMMC',
				// typeAlias
				'com_ehealthportal.vmmc',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_vmmc',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Prostate_and_testicular_cancer to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PROSTATE_AND_TESTICULAR_CANCER',
				// typeAlias
				'com_ehealthportal.prostate_and_testicular_cancer',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_prostate_and_testicular_cancer',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Tuberculosis to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TUBERCULOSIS',
				// typeAlias
				'com_ehealthportal.tuberculosis',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_tuberculosis',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Hiv_counseling_and_testing to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'HIV_COUNSELING_AND_TESTING',
				// typeAlias
				'com_ehealthportal.hiv_counseling_and_testing',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_hiv_counseling_and_testing',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Family_planning to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FAMILY_PLANNING',
				// typeAlias
				'com_ehealthportal.family_planning',
				// idHolder
				'id',
				// titleHolder
				'diagnosis',
				// tableName
				'#__ehealthportal_family_planning',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Health_education to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'HEALTH_EDUCATION',
				// typeAlias
				'com_ehealthportal.health_education',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_health_education',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Cervical_cancer to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'CERVICAL_CANCER',
				// typeAlias
				'com_ehealthportal.cervical_cancer',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_cervical_cancer',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Breast_cancer to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'BREAST_CANCER',
				// typeAlias
				'com_ehealthportal.breast_cancer',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_breast_cancer',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Test to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TEST',
				// typeAlias
				'com_ehealthportal.test',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_test',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Foetal_lie to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FOETAL_LIE',
				// typeAlias
				'com_ehealthportal.foetal_lie',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_foetal_lie',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Immunisation_vaccine_type to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'IMMUNISATION_VACCINE_TYPE',
				// typeAlias
				'com_ehealthportal.immunisation_vaccine_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_immunisation_vaccine_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Foetal_engagement to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FOETAL_ENGAGEMENT',
				// typeAlias
				'com_ehealthportal.foetal_engagement',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_foetal_engagement',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Foetal_presentation to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FOETAL_PRESENTATION',
				// typeAlias
				'com_ehealthportal.foetal_presentation',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_foetal_presentation',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Testing_reason to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TESTING_REASON',
				// typeAlias
				'com_ehealthportal.testing_reason',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_testing_reason',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Counseling_type to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'COUNSELING_TYPE',
				// typeAlias
				'com_ehealthportal.counseling_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_counseling_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Health_education_topic to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'HEALTH_EDUCATION_TOPIC',
				// typeAlias
				'com_ehealthportal.health_education_topic',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_health_education_topic',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Immunisation_type to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'IMMUNISATION_TYPE',
				// typeAlias
				'com_ehealthportal.immunisation_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_immunisation_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Strength to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'STRENGTH',
				// typeAlias
				'com_ehealthportal.strength',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_strength',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Referral to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'REFERRAL',
				// typeAlias
				'com_ehealthportal.referral',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_referral',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Planning_type to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PLANNING_TYPE',
				// typeAlias
				'com_ehealthportal.planning_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_planning_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Diagnosis_type to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'DIAGNOSIS_TYPE',
				// typeAlias
				'com_ehealthportal.diagnosis_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_diagnosis_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Nonpay_reason to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'NONPAY_REASON',
				// typeAlias
				'com_ehealthportal.nonpay_reason',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_nonpay_reason',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Medication to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'MEDICATION',
				// typeAlias
				'com_ehealthportal.medication',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_medication',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Payment_type to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PAYMENT_TYPE',
				// typeAlias
				'com_ehealthportal.payment_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_payment_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Administration_part to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'ADMINISTRATION_PART',
				// typeAlias
				'com_ehealthportal.administration_part',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_administration_part',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Site to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'SITE',
				// typeAlias
				'com_ehealthportal.site',
				// idHolder
				'id',
				// titleHolder
				'site_name',
				// tableName
				'#__ehealthportal_site',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Unit to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'UNIT',
				// typeAlias
				'com_ehealthportal.unit',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_unit',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add Clinic to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'CLINIC',
				// typeAlias
				'com_ehealthportal.clinic',
				// idHolder
				'id',
				// titleHolder
				'clinic_name',
				// tableName
				'#__ehealthportal_clinic',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);
		}

		// do any updates needed
		if ($type === 'update')
		{

			// Update Payment Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Payment',
				// typeAlias
				'com_ehealthportal.payment',
				// table
				'{"special": {"dbtable": "#__ehealthportal_payment","key": "id","type": "PaymentTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","payment_category":"payment_category","payment_type":"payment_type","payment_amount":"payment_amount","nonpay_reason":"nonpay_reason","receipt_no":"receipt_no","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","payment_type","nonpay_reason"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "payment_type","targetTable": "#__ehealthportal_payment_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "nonpay_reason","targetTable": "#__ehealthportal_nonpay_reason","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update General medical check up Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal General_medical_check_up',
				// typeAlias
				'com_ehealthportal.general_medical_check_up',
				// table
				'{"special": {"dbtable": "#__ehealthportal_general_medical_check_up","key": "id","type": "General_medical_check_upTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bp_diastolic_one":"bp_diastolic_one","bp_systolic_one":"bp_systolic_one","temp_one":"temp_one","weight":"weight","pulse":"pulse","chronic_medication":"chronic_medication","bp_diastolic_two":"bp_diastolic_two","bp_systolic_two":"bp_systolic_two","temp_two":"temp_two","height":"height","bmi":"bmi","complaint":"complaint","investigations":"investigations","notes":"notes","diagnosis":"diagnosis","referral":"referral","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/general_medical_check_up.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bp_diastolic_one","bp_systolic_one","pulse","bp_diastolic_two","bp_systolic_two","diagnosis","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_diagnosis_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Antenatal care Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Antenatal_care',
				// typeAlias
				'com_ehealthportal.antenatal_care',
				// table
				'{"special": {"dbtable": "#__ehealthportal_antenatal_care","key": "id","type": "Antenatal_careTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","foetal_lie":"foetal_lie","foetal_presentation":"foetal_presentation","foetal_engagement":"foetal_engagement","foetal_heart_rate":"foetal_heart_rate","foetal_movements":"foetal_movements","guid":"guid","caesarean_sections":"caesarean_sections","last_menstrual_period":"last_menstrual_period","normal_births":"normal_births","still_births":"still_births","miscarriages":"miscarriages","live_births":"live_births","pregnancies_excl":"pregnancies_excl"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/antenatal_care.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","foetal_lie","foetal_presentation","foetal_engagement","foetal_heart_rate","caesarean_sections","normal_births","still_births","miscarriages","live_births","pregnancies_excl"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_lie","targetTable": "#__ehealthportal_foetal_lie","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_presentation","targetTable": "#__ehealthportal_foetal_presentation","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_engagement","targetTable": "#__ehealthportal_foetal_engagement","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Immunisation Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Immunisation',
				// typeAlias
				'com_ehealthportal.immunisation',
				// table
				'{"special": {"dbtable": "#__ehealthportal_immunisation","key": "id","type": "ImmunisationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","referral":"referral","immunisation_up_to_date":"immunisation_up_to_date","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/immunisation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Vmmc Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Vmmc',
				// typeAlias
				'com_ehealthportal.vmmc',
				// table
				'{"special": {"dbtable": "#__ehealthportal_vmmc","key": "id","type": "VmmcTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","are_you_circumcised":"are_you_circumcised","info_ben_vmcc":"info_ben_vmcc","interested_in_vmmc":"interested_in_vmmc","vmmc_gender":"vmmc_gender","reason":"reason","referral":"referral","guid":"guid","partner_circumcised":"partner_circumcised"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/vmmc.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Prostate and testicular cancer Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Prostate_and_testicular_cancer',
				// typeAlias
				'com_ehealthportal.prostate_and_testicular_cancer',
				// table
				'{"special": {"dbtable": "#__ehealthportal_prostate_and_testicular_cancer","key": "id","type": "Prostate_and_testicular_cancerTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","ptc_age":"ptc_age","ptc_fam_history":"ptc_fam_history","ptc_diet":"ptc_diet","ptc_phy_activity":"ptc_phy_activity","ptc_overweight":"ptc_overweight","ptc_urinate":"ptc_urinate","ptc_urine_freq":"ptc_urine_freq","referral":"referral","reason":"reason","txt_ptc_urine_freq":"txt_ptc_urine_freq","txt_ptc_urinate":"txt_ptc_urinate","txt_ptc_age":"txt_ptc_age","txt_ptc_phy_activity":"txt_ptc_phy_activity","txt_ptc_diet":"txt_ptc_diet","txt_ptc_fam_history":"txt_ptc_fam_history","guid":"guid","txt_ptc_overweight":"txt_ptc_overweight"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/prostate_and_testicular_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Tuberculosis Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Tuberculosis',
				// typeAlias
				'com_ehealthportal.tuberculosis',
				// table
				'{"special": {"dbtable": "#__ehealthportal_tuberculosis","key": "id","type": "TuberculosisTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","recurring_night_sweats":"recurring_night_sweats","tb_fever":"tb_fever","persistent_cough":"persistent_cough","blood_streaked_sputum":"blood_streaked_sputum","unusual_tiredness":"unusual_tiredness","pain_in_chest":"pain_in_chest","shortness_of_breath":"shortness_of_breath","diagnosed_with_disease":"diagnosed_with_disease","tb_exposed":"tb_exposed","tb_treatment":"tb_treatment","date_of_treatment":"date_of_treatment","treating_dhc":"treating_dhc","sputum_collection_one":"sputum_collection_one","tb_reason_one":"tb_reason_one","sputum_result_one":"sputum_result_one","referred_second_sputum":"referred_second_sputum","tb_reason_two":"tb_reason_two","sputum_result_two":"sputum_result_two","weight_loss_wdieting":"weight_loss_wdieting","reason":"reason","guid":"guid","referral":"referral"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/tuberculosis.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Hiv counseling and testing Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Hiv_counseling_and_testing',
				// typeAlias
				'com_ehealthportal.hiv_counseling_and_testing',
				// table
				'{"special": {"dbtable": "#__ehealthportal_hiv_counseling_and_testing","key": "id","type": "Hiv_counseling_and_testingTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","counseling_type":"counseling_type","testing_reason":"testing_reason","last_test_date":"last_test_date","prev_test_result":"prev_test_result","test_result_one":"test_result_one","test_result_two":"test_result_two","final_test_result":"final_test_result","eqa":"eqa","referral":"referral","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/hiv_counseling_and_testing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","testing_reason","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "testing_reason","targetTable": "#__ehealthportal_testing_reason","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Family planning Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Family_planning',
				// typeAlias
				'com_ehealthportal.family_planning',
				// table
				'{"special": {"dbtable": "#__ehealthportal_family_planning","key": "id","type": "Family_planningTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "diagnosis","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","diagnosis":"diagnosis","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/family_planning.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","diagnosis"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealthportal_planning_type","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Health education Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Health_education',
				// typeAlias
				'com_ehealthportal.health_education',
				// table
				'{"special": {"dbtable": "#__ehealthportal_health_education","key": "id","type": "Health_educationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"education_type":"education_type","health_education_topic":"health_education_topic","patient":"patient","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/health_education.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","health_education_topic"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "health_education_topic","targetTable": "#__ehealthportal_health_education_topic","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Cervical cancer Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Cervical_cancer',
				// typeAlias
				'com_ehealthportal.cervical_cancer',
				// table
				'{"special": {"dbtable": "#__ehealthportal_cervical_cancer","key": "id","type": "Cervical_cancerTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","cc_viginal_bleeding":"cc_viginal_bleeding","cc_v_discharge":"cc_v_discharge","cc_periods":"cc_periods","cc_smoking":"cc_smoking","cc_sex_actve":"cc_sex_actve","cc_sex_partner":"cc_sex_partner","pap_smear_collection":"pap_smear_collection","cc_result":"cc_result","referral":"referral","reason":"reason","cc_reason":"cc_reason","txt_cc_v_discharge":"txt_cc_v_discharge","txt_cc_sex_partner":"txt_cc_sex_partner","txt_cc_viginal_bleeding":"txt_cc_viginal_bleeding","txt_cc_smoking":"txt_cc_smoking","txt_cc_periods":"txt_cc_periods","guid":"guid","txt_cc_sex_actve":"txt_cc_sex_actve"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/cervical_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Breast cancer Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Breast_cancer',
				// typeAlias
				'com_ehealthportal.breast_cancer',
				// table
				'{"special": {"dbtable": "#__ehealthportal_breast_cancer","key": "id","type": "Breast_cancerTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bc_age_range":"bc_age_range","bc_family_history":"bc_family_history","bc_race":"bc_race","bc_breastfeeding":"bc_breastfeeding","bc_preg_freq":"bc_preg_freq","bc_preg_age":"bc_preg_age","bc_history_hrt":"bc_history_hrt","bc_reg_exercise":"bc_reg_exercise","bc_overweight":"bc_overweight","bc_lump_near_breast":"bc_lump_near_breast","bc_dimpling":"bc_dimpling","bc_inward_nipple":"bc_inward_nipple","bc_nipple_discharge":"bc_nipple_discharge","bc_abnormal_skin":"bc_abnormal_skin","bc_breast_shape":"bc_breast_shape","referral":"referral","reason":"reason","guid":"guid","txt_bc_breast_shape":"txt_bc_breast_shape","txt_bc_abnormal_skin":"txt_bc_abnormal_skin","txt_bc_nipple_discharge":"txt_bc_nipple_discharge","txt_bc_dimpling":"txt_bc_dimpling","txt_bc_lump_near_breast":"txt_bc_lump_near_breast","txt_bc_inward_nipple":"txt_bc_inward_nipple"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/breast_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","bc_preg_freq","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Test Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Test',
				// typeAlias
				'com_ehealthportal.test',
				// table
				'{"special": {"dbtable": "#__ehealthportal_test","key": "id","type": "TestTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "patient","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","urine_test_result":"urine_test_result","glucose_first_reading":"glucose_first_reading","glucose_second_reading":"glucose_second_reading","haemoglobin_reading":"haemoglobin_reading","cholesterol_reading":"cholesterol_reading","syphilis_first_reading":"syphilis_first_reading","syphilis_second_reading":"syphilis_second_reading","hepatitis_first_reading":"hepatitis_first_reading","hepatitis_second_reading":"hepatitis_second_reading","malaria_first_reading":"malaria_first_reading","malaria_second_reading":"malaria_second_reading","pregnancy_first_reading":"pregnancy_first_reading","pregnancy_second_reading":"pregnancy_second_reading","referral":"referral","reason":"reason","guid":"guid"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/test.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","glucose_first_reading","glucose_second_reading","haemoglobin_reading","cholesterol_reading","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealthportal_referral","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Foetal lie Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Foetal_lie',
				// typeAlias
				'com_ehealthportal.foetal_lie',
				// table
				'{"special": {"dbtable": "#__ehealthportal_foetal_lie","key": "id","type": "Foetal_lieTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/foetal_lie.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Immunisation vaccine type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Immunisation_vaccine_type',
				// typeAlias
				'com_ehealthportal.immunisation_vaccine_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_immunisation_vaccine_type","key": "id","type": "Immunisation_vaccine_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","administration_part":"administration_part","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/immunisation_vaccine_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealthportal_administration_part","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Foetal engagement Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Foetal_engagement',
				// typeAlias
				'com_ehealthportal.foetal_engagement',
				// table
				'{"special": {"dbtable": "#__ehealthportal_foetal_engagement","key": "id","type": "Foetal_engagementTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/foetal_engagement.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Foetal presentation Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Foetal_presentation',
				// typeAlias
				'com_ehealthportal.foetal_presentation',
				// table
				'{"special": {"dbtable": "#__ehealthportal_foetal_presentation","key": "id","type": "Foetal_presentationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/foetal_presentation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Testing reason Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Testing_reason',
				// typeAlias
				'com_ehealthportal.testing_reason',
				// table
				'{"special": {"dbtable": "#__ehealthportal_testing_reason","key": "id","type": "Testing_reasonTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/testing_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Counseling type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Counseling_type',
				// typeAlias
				'com_ehealthportal.counseling_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_counseling_type","key": "id","type": "Counseling_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/counseling_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Health education topic Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Health_education_topic',
				// typeAlias
				'com_ehealthportal.health_education_topic',
				// table
				'{"special": {"dbtable": "#__ehealthportal_health_education_topic","key": "id","type": "Health_education_topicTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/health_education_topic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Immunisation type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Immunisation_type',
				// typeAlias
				'com_ehealthportal.immunisation_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_immunisation_type","key": "id","type": "Immunisation_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/immunisation_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Strength Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Strength',
				// typeAlias
				'com_ehealthportal.strength',
				// table
				'{"special": {"dbtable": "#__ehealthportal_strength","key": "id","type": "StrengthTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/strength.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Referral Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Referral',
				// typeAlias
				'com_ehealthportal.referral',
				// table
				'{"special": {"dbtable": "#__ehealthportal_referral","key": "id","type": "ReferralTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/referral.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Planning type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Planning_type',
				// typeAlias
				'com_ehealthportal.planning_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_planning_type","key": "id","type": "Planning_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/planning_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Diagnosis type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Diagnosis_type',
				// typeAlias
				'com_ehealthportal.diagnosis_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_diagnosis_type","key": "id","type": "Diagnosis_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/diagnosis_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Nonpay reason Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Nonpay_reason',
				// typeAlias
				'com_ehealthportal.nonpay_reason',
				// table
				'{"special": {"dbtable": "#__ehealthportal_nonpay_reason","key": "id","type": "Nonpay_reasonTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/nonpay_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Medication Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Medication',
				// typeAlias
				'com_ehealthportal.medication',
				// table
				'{"special": {"dbtable": "#__ehealthportal_medication","key": "id","type": "MedicationTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/medication.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Payment type Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Payment_type',
				// typeAlias
				'com_ehealthportal.payment_type',
				// table
				'{"special": {"dbtable": "#__ehealthportal_payment_type","key": "id","type": "Payment_typeTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/payment_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Administration part Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Administration_part',
				// typeAlias
				'com_ehealthportal.administration_part',
				// table
				'{"special": {"dbtable": "#__ehealthportal_administration_part","key": "id","type": "Administration_partTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/administration_part.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Site Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Site',
				// typeAlias
				'com_ehealthportal.site',
				// table
				'{"special": {"dbtable": "#__ehealthportal_site","key": "id","type": "SiteTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "site_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"site_name":"site_name","description":"description","site_region":"site_region","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/site.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Unit Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Unit',
				// typeAlias
				'com_ehealthportal.unit',
				// table
				'{"special": {"dbtable": "#__ehealthportal_unit","key": "id","type": "UnitTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","guid":"guid","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/unit.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);
			// Update Clinic Content Types.
			$this->setContentType(
				// typeTitle
				'Ehealthportal Clinic',
				// typeAlias
				'com_ehealthportal.clinic',
				// table
				'{"special": {"dbtable": "#__ehealthportal_clinic","key": "id","type": "ClinicTable","prefix": "JCB\Component\Ehealthportal\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "clinic_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"clinic_name":"clinic_name","description":"description","guid":"guid","clinic_type":"clinic_type","alias":"alias"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_ehealthportal/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}'
			);



			echo '<div style="background-color: #fff;" class="alert alert-info"><a target="_blank" href="https://git.vdm.dev/joomla/eHealth-Portal" title="eHealth Portal">
				<img src="components/com_ehealthportal/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 4.0.0 Was Successful! Let us know if anything is not working as expected.</h3></div>';

			// Add/Update component in the action logs extensions table.
			$this->setActionLogsExtensions();

			// Add/Update Payment in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PAYMENT',
				// typeAlias
				'com_ehealthportal.payment',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_payment',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update General_medical_check_up in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'GENERAL_MEDICAL_CHECK_UP',
				// typeAlias
				'com_ehealthportal.general_medical_check_up',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_general_medical_check_up',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Antenatal_care in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'ANTENATAL_CARE',
				// typeAlias
				'com_ehealthportal.antenatal_care',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_antenatal_care',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Immunisation in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'IMMUNISATION',
				// typeAlias
				'com_ehealthportal.immunisation',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_immunisation',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Vmmc in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'VMMC',
				// typeAlias
				'com_ehealthportal.vmmc',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_vmmc',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Prostate_and_testicular_cancer in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PROSTATE_AND_TESTICULAR_CANCER',
				// typeAlias
				'com_ehealthportal.prostate_and_testicular_cancer',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_prostate_and_testicular_cancer',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Tuberculosis in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TUBERCULOSIS',
				// typeAlias
				'com_ehealthportal.tuberculosis',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_tuberculosis',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Hiv_counseling_and_testing in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'HIV_COUNSELING_AND_TESTING',
				// typeAlias
				'com_ehealthportal.hiv_counseling_and_testing',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_hiv_counseling_and_testing',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Family_planning in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FAMILY_PLANNING',
				// typeAlias
				'com_ehealthportal.family_planning',
				// idHolder
				'id',
				// titleHolder
				'diagnosis',
				// tableName
				'#__ehealthportal_family_planning',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Health_education in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'HEALTH_EDUCATION',
				// typeAlias
				'com_ehealthportal.health_education',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_health_education',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Cervical_cancer in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'CERVICAL_CANCER',
				// typeAlias
				'com_ehealthportal.cervical_cancer',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_cervical_cancer',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Breast_cancer in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'BREAST_CANCER',
				// typeAlias
				'com_ehealthportal.breast_cancer',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_breast_cancer',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Test in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TEST',
				// typeAlias
				'com_ehealthportal.test',
				// idHolder
				'id',
				// titleHolder
				'patient',
				// tableName
				'#__ehealthportal_test',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Foetal_lie in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FOETAL_LIE',
				// typeAlias
				'com_ehealthportal.foetal_lie',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_foetal_lie',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Immunisation_vaccine_type in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'IMMUNISATION_VACCINE_TYPE',
				// typeAlias
				'com_ehealthportal.immunisation_vaccine_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_immunisation_vaccine_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Foetal_engagement in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FOETAL_ENGAGEMENT',
				// typeAlias
				'com_ehealthportal.foetal_engagement',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_foetal_engagement',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Foetal_presentation in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'FOETAL_PRESENTATION',
				// typeAlias
				'com_ehealthportal.foetal_presentation',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_foetal_presentation',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Testing_reason in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TESTING_REASON',
				// typeAlias
				'com_ehealthportal.testing_reason',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_testing_reason',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Counseling_type in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'COUNSELING_TYPE',
				// typeAlias
				'com_ehealthportal.counseling_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_counseling_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Health_education_topic in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'HEALTH_EDUCATION_TOPIC',
				// typeAlias
				'com_ehealthportal.health_education_topic',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_health_education_topic',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Immunisation_type in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'IMMUNISATION_TYPE',
				// typeAlias
				'com_ehealthportal.immunisation_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_immunisation_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Strength in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'STRENGTH',
				// typeAlias
				'com_ehealthportal.strength',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_strength',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Referral in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'REFERRAL',
				// typeAlias
				'com_ehealthportal.referral',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_referral',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Planning_type in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PLANNING_TYPE',
				// typeAlias
				'com_ehealthportal.planning_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_planning_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Diagnosis_type in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'DIAGNOSIS_TYPE',
				// typeAlias
				'com_ehealthportal.diagnosis_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_diagnosis_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Nonpay_reason in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'NONPAY_REASON',
				// typeAlias
				'com_ehealthportal.nonpay_reason',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_nonpay_reason',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Medication in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'MEDICATION',
				// typeAlias
				'com_ehealthportal.medication',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_medication',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Payment_type in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PAYMENT_TYPE',
				// typeAlias
				'com_ehealthportal.payment_type',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_payment_type',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Administration_part in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'ADMINISTRATION_PART',
				// typeAlias
				'com_ehealthportal.administration_part',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_administration_part',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Site in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'SITE',
				// typeAlias
				'com_ehealthportal.site',
				// idHolder
				'id',
				// titleHolder
				'site_name',
				// tableName
				'#__ehealthportal_site',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Unit in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'UNIT',
				// typeAlias
				'com_ehealthportal.unit',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__ehealthportal_unit',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);

			// Add/Update Clinic in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'CLINIC',
				// typeAlias
				'com_ehealthportal.clinic',
				// idHolder
				'id',
				// titleHolder
				'clinic_name',
				// tableName
				'#__ehealthportal_clinic',
				// textPrefix
				'COM_EHEALTHPORTAL'
			);
		}

		// move CLI files
		$this->moveCliFiles();

		// remove old files and folders
		$this->removeFiles();

		return true;
	}

	/**
	 * Remove the files and folders in the given array from
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function removeFiles()
	{
		if (!empty($this->deleteFiles))
		{
			foreach ($this->deleteFiles as $file)
			{
				if (is_file(JPATH_ROOT . $file) && !File::delete(JPATH_ROOT . $file))
				{
					echo Text::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $file) . '<br>';
				}
			}
		}

		if (!empty($this->deleteFolders))
		{
			foreach ($this->deleteFolders as $folder)
			{
				if (is_dir(JPATH_ROOT . $folder) && !Folder::delete(JPATH_ROOT . $folder))
				{
					echo Text::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $folder) . '<br>';
				}
			}
		}
	}

	/**
	 * Moves the CLI scripts into the CLI folder in the CMS
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function moveCliFiles()
	{
		if (!empty($this->cliScriptFiles))
		{
			foreach ($this->cliScriptFiles as $file)
			{
				$name = basename($file);

				if (file_exists(JPATH_ROOT . $file) && !File::move(JPATH_ROOT . $file, JPATH_ROOT . '/cli/' . $name))
				{
					echo Text::sprintf('JLIB_INSTALLER_FILE_ERROR_MOVE', $name);
				}
			}
		}
	}

	/**
	 * Set content type integration
	 *
	 * @param   string   $typeTitle
	 * @param   string   $typeAlias
	 * @param   string   $table
	 * @param   string   $rules
	 * @param   string   $fieldMappings
	 * @param   string   $router
	 * @param   string   $contentHistoryOptions
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setContentType(
		string $typeTitle,
		string $typeAlias,
		string $table,
		string $rules,
		string $fieldMappings,
		string $router,
		string $contentHistoryOptions): void
	{
		// Create the content type object.
		$content = new stdClass();
		$content->type_title = $typeTitle;
		$content->type_alias = $typeAlias;
		$content->table = $table;
		$content->rules = $rules;
		$content->field_mappings = $fieldMappings;
		$content->router = $router;
		$content->content_history_options = $contentHistoryOptions;

		// Check if content type is already in content_type DB.
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('type_id')));
		$query->from($this->db->quoteName('#__content_types'));
		$query->where($this->db->quoteName('type_alias') . ' LIKE '. $this->db->quote($content->type_alias));

		$this->db->setQuery($query);
		$this->db->execute();

		// Check if the type alias is already in the content types table.
		if ($this->db->getNumRows())
		{
			$content->type_id = $this->db->loadResult();
			if ($this->db->updateObject('#__content_types', $content, 'type_id'))
			{
				// If its successfully update.
				$this->app->enqueueMessage(
					Text::sprintf('The (%s) was found in the <b>#__content_types</b> table, and updated.', $content->type_alias)
				);
			}
		}
		elseif ($this->db->insertObject('#__content_types', $content))
		{
			// If its successfully added.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) was added to the <b>#__content_types</b> table.', $content->type_alias)
			);
		}
	}

	/**
	 * Set action log config integration
	 *
	 * @param   string   $typeTitle
	 * @param   string   $typeAlias
	 * @param   string   $idHolder
	 * @param   string   $titleHolder
	 * @param   string   $tableName
	 * @param   string   $textPrefix
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setActionLogConfig(
		string $typeTitle,
		string $typeAlias,
		string $idHolder,
		string $titleHolder,
		string $tableName,
		string $textPrefix): void
	{
		// Create the content action log config object.
		$content = new stdClass();
		$content->type_title = $typeTitle;
		$content->type_alias = $typeAlias;
		$content->id_holder = $idHolder;
		$content->title_holder = $titleHolder;
		$content->table_name = $tableName;
		$content->text_prefix = $textPrefix;

		// Check if the action log config is already in action_log_config DB.
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(['id']));
		$query->from($this->db->quoteName('#__action_log_config'));
		$query->where($this->db->quoteName('type_alias') . ' LIKE '. $this->db->quote($content->type_alias));

		$this->db->setQuery($query);
		$this->db->execute();

		// Check if the type alias is already in the action log config table.
		if ($this->db->getNumRows())
		{
			$content->id = $this->db->loadResult();
			if ($this->db->updateObject('#__action_log_config', $content, 'id'))
			{
				// If its successfully update.
				$this->app->enqueueMessage(
					Text::sprintf('The (%s) was found in the <b>#__action_log_config</b> table, and updated.', $content->type_alias)
				);
			}
		}
		elseif ($this->db->insertObject('#__action_log_config', $content))
		{
			// If its successfully added.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) was added to the <b>#__action_log_config</b> table.', $content->type_alias)
			);
		}
	}

	/**
	 * Set action logs extensions integration
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setActionLogsExtensions(): void
	{
		// Create the extension action logs object.
		$data = new stdClass();
		$data->extension = 'com_ehealthportal';

		// Check if ehealthportal action log extension is already in action logs extensions DB.
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(['id']));
		$query->from($this->db->quoteName('#__action_logs_extensions'));
		$query->where($this->db->quoteName('extension') . ' = '. $this->db->quote($data->extension));

		$this->db->setQuery($query);
		$this->db->execute();

		// Set the object into the action logs extensions table if not found.
		if ($this->db->getNumRows())
		{
			// If its already set don't set it again.
			$this->app->enqueueMessage(
				Text::_('The (com_ehealthportal) is already in the <b>#__action_logs_extensions</b> table.')
			);
		}
		elseif ($this->db->insertObject('#__action_logs_extensions', $data))
		{
			// give a success message
			$this->app->enqueueMessage(
				Text::_('The (com_ehealthportal) was successfully added to the <b>#__action_logs_extensions</b> table.')
			);
		}
	}

	/**
	 * Set global extension assets permission of this component
	 *   (on install only)
	 *
	 * @param   string   $rules   The component rules
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setAssetsRules(string $rules): void
	{
		// Condition.
		$conditions = [
			$this->db->quoteName('name') . ' = ' . $this->db->quote('com_ehealthportal')
		];

		// Field to update.
		$fields = [
			$this->db->quoteName('rules') . ' = ' . $this->db->quote($rules),
		];

		$query = $this->db->getQuery(true);
		$query->update(
			$this->db->quoteName('#__assets')
		)->set($fields)->where($conditions);

		$this->db->setQuery($query);

		$done = $this->db->execute();
		if ($done)
		{
			// give a success message
			$this->app->enqueueMessage(
				Text::_('The (com_ehealthportal) rules was successfully added to the <b>#__assets</b> table.')
			);
		}
	}

	/**
	 * Set global extension params of this component
	 *   (on install only)
	 *
	 * @param   string   $params   The component rules
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setExtensionsParams(string $params): void
	{
		// Condition.
		$conditions = [
			$this->db->quoteName('element') . ' = ' . $this->db->quote('com_ehealthportal')
		];

		// Field to update.
		$fields = [
			$this->db->quoteName('params') . ' = ' . $this->db->quote($params),
		];

		$query = $this->db->getQuery(true);
		$query->update(
			$this->db->quoteName('#__extensions')
		)->set($fields)->where($conditions);

		$this->db->setQuery($query);

		$done = $this->db->execute();
		if ($done)
		{
			// give a success message
			$this->app->enqueueMessage(
				Text::_('The (com_ehealthportal) params was successfully added to the <b>#__extensions</b> table.')
			);
		}
	}

	/**
	 * Set database fix (if needed)
	 *  => WHY DO WE NEED AN ASSET TABLE FIX?
	 *   https://git.vdm.dev/joomla/Component-Builder/issues/616#issuecomment-12085
	 *   https://www.mysqltutorial.org/mysql-varchar/
	 *   https://stackoverflow.com/a/15227917/1429677
	 *   https://forums.mysql.com/read.php?24,105964,105964
	 *
	 * @param   int     $accessWorseCase   This is the max rules column size com_ehealthportal would needs.
	 * @param   string  $dataType          This datatype we will change the rules column to if it to small.
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setDatabaseAssetsRulesFix(int $accessWorseCase, string $dataType): void
	{
		// Get the biggest rule column in the assets table at this point.
		$length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$this->db->setQuery($length);
		if ($this->db->execute())
		{
			$rule_length = $this->db->loadResult();
			// Check the size of the rules column
			if ($rule_length <= $accessWorseCase)
			{
				// Fix the assets table rules column size
				$fix = "ALTER TABLE `#__assets` CHANGE `rules` `rules` {$dataType} NOT NULL COMMENT 'JSON encoded access control. Enlarged to {$dataType} by Ehealthportal';";
				$this->db->setQuery($fix);

				$done = $this->db->execute();
				if ($done)
				{
					$this->app->enqueueMessage(
						Text::sprintf('The <b>#__assets</b> table rules column was resized to the %s datatype for the components possible large permission rules.', $dataType)
					);
				}
			}
		}
	}

	/**
	 * Remove remnant data related to this view
	 *
	 * @param   string   $context   The view context
	 * @param   bool     $fields    The switch to also remove related field data
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeViewData(string $context, bool $fields = false): void
	{
		$this->removeContentTypes($context);
		$this->removeViewHistory($context);
		$this->removeUcmContent($context); // this might be obsolete...
		$this->removeContentItemTagMap($context);
		$this->removeActionLogConfig($context);

		if ($fields)
		{
			$this->removeFields($context);
			$this->removeFieldsGroups($context);
		}
	}

	/**
	 * Remove content types related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeContentTypes(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Select id from content type table
		$query->select($this->db->quoteName('type_id'));
		$query->from($this->db->quoteName('#__content_types'));

		// Where Item alias is found
		$query->where($this->db->quoteName('type_alias') . ' = '. $this->db->quote($context));
		$this->db->setQuery($query);

		// Execute query to see if alias is found
		$this->db->execute();
		$found = $this->db->getNumRows();

		// Now check if there were any rows
		if ($found)
		{
			// Since there are load the needed  item type ids
			$ids = $this->db->loadColumn();

			// Remove Item from the content type table
			$condition = [
				$this->db->quoteName('type_alias') . ' = '. $this->db->quote($context)
			];

			// Create a new query object.
			$query = $this->db->getQuery(true);
			$query->delete($this->db->quoteName('#__content_types'));
			$query->where($condition);
			$this->db->setQuery($query);

			// Execute the query to remove Item items
			$done = $this->db->execute();
			if ($done)
			{
				// If successfully remove Item add queued success message.
				$this->app->enqueueMessage(
					Text::sprintf('The (%s) type alias was removed from the <b>#__content_type</b> table.', $context)
				);
			}

			// Make sure that all the items are cleared from DB
			$this->removeUcmBase($ids);
		}
	}

	/**
	 * Remove fields related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeFields(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Select ids from fields
		$query->select($this->db->quoteName('id'));
		$query->from($this->db->quoteName('#__fields'));

		// Where context is found
		$query->where(
			$this->db->quoteName('context') . ' = '. $this->db->quote($context)
		);
		$this->db->setQuery($query);

		// Execute query to see if context is found
		$this->db->execute();
		$found = $this->db->getNumRows();

		// Now check if there were any rows
		if ($found)
		{
			// Since there are load the needed  release_check field ids
			$ids = $this->db->loadColumn();

			// Create a new query object.
			$query = $this->db->getQuery(true);

			// Remove context from the field table
			$condition = [
				$this->db->quoteName('context') . ' = '. $this->db->quote($context)
			];

			$query->delete($this->db->quoteName('#__fields'));
			$query->where($condition);

			$this->db->setQuery($query);

			// Execute the query to remove release_check items
			$done = $this->db->execute();
			if ($done)
			{
				// If successfully remove context add queued success message.
				$this->app->enqueueMessage(
					Text::sprintf('The fields with context (%s) was removed from the <b>#__fields</b> table.', $context)
				);
			}

			// Make sure that all the field values are cleared from DB
			$this->removeFieldsValues($context, $ids);
		}
	}

	/**
	 * Remove fields values related to fields
	 *
	 * @param   string   $context   The view context
	 * @param   array    $ids       The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeFieldsValues(string $context, array $ids): void
	{
		$condition = [
			$this->db->quoteName('field_id') . ' IN ('. implode(',', $ids) .')'
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__fields_values'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove field values
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully remove release_check add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The fields values for (%s) was removed from the <b>#__fields_values</b> table.', $context)
			);
		}
	}

	/**
	 * Remove fields groups related to fields
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeFieldsGroups(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Select ids from fields
		$query->select($this->db->quoteName('id'));
		$query->from($this->db->quoteName('#__fields_groups'));

		// Where context is found
		$query->where(
			$this->db->quoteName('context') . ' = '. $this->db->quote($context)
		);
		$this->db->setQuery($query);

		// Execute query to see if context is found
		$this->db->execute();
		$found = $this->db->getNumRows();

		// Now check if there were any rows
		if ($found)
		{
			// Create a new query object.
			$query = $this->db->getQuery(true);

			// Remove context from the field table
			$condition = [
				$this->db->quoteName('context') . ' = '. $this->db->quote($context)
			];

			$query->delete($this->db->quoteName('#__fields_groups'));
			$query->where($condition);

			$this->db->setQuery($query);

			// Execute the query to remove release_check items
			$done = $this->db->execute();
			if ($done)
			{
				// If successfully remove context add queued success message.
				$this->app->enqueueMessage(
					Text::sprintf('The fields with context (%s) was removed from the <b>#__fields_groups</b> table.', $context)
				);
			}
		}
	}

	/**
	 * Remove history related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeViewHistory(string $context): void
	{
		// Remove Item items from the ucm content table
		$condition = [
			$this->db->quoteName('item_id') . ' LIKE ' . $this->db->quote($context . '.%')
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__history'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove Item items
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed Items add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) items were removed from the <b>#__history</b> table.', $context)
			);
		}
	}

	/**
	 * Remove ucm base values related to these IDs
	 *
	 * @param   array   $ids   The type ids
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeUcmBase(array $ids): void
	{
		// Make sure that all the items are cleared from DB
		foreach ($ids as $type_id)
		{
			// Remove Item items from the ucm base table
			$condition = [
				$this->db->quoteName('ucm_type_id') . ' = ' . $type_id
			];

			// Create a new query object.
			$query = $this->db->getQuery(true);
			$query->delete($this->db->quoteName('#__ucm_base'));
			$query->where($condition);
			$this->db->setQuery($query);

			// Execute the query to remove Item items
			$this->db->execute();
		}

		$this->app->enqueueMessage(
			Text::_('All related items was removed from the <b>#__ucm_base</b> table.')
		);
	}

	/**
	 * Remove ucm content values related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeUcmContent(string $context): void
	{
		// Remove Item items from the ucm content table
		$condition = [
			$this->db->quoteName('core_type_alias') . ' = ' . $this->db->quote($context)
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__ucm_content'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove Item items
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed Item add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) type alias was removed from the <b>#__ucm_content</b> table.', $context)
			);
		}
	}

	/**
	 * Remove content item tag map related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeContentItemTagMap(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Remove Item items from the contentitem tag map table
		$condition = [
			$this->db->quoteName('type_alias') . ' = '. $this->db->quote($context)
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__contentitem_tag_map'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove Item items
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully remove Item add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) type alias was removed from the <b>#__contentitem_tag_map</b> table.', $context)
			);
		}
	}

	/**
	 * Remove action log config related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeActionLogConfig(string $context): void
	{
		// Remove ehealthportal view from the action_log_config table
		$condition = [
			$this->db->quoteName('type_alias') . ' = '. $this->db->quote($context)
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__action_log_config'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove com_ehealthportal.view
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed ehealthportal view add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) type alias was removed from the <b>#__action_log_config</b> table.', $context)
			);
		}
	}

	/**
	 * Remove Asset Table Integrated
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeAssetData(): void
	{
		// Remove ehealthportal assets from the assets table
		$condition = [
			$this->db->quoteName('name') . ' LIKE ' . $this->db->quote('com_ehealthportal.%')
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__assets'));
		$query->where($condition);
		$this->db->setQuery($query);
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed ehealthportal add queued success message.
			$this->app->enqueueMessage(
				Text::_('All related (com_ehealthportal) items was removed from the <b>#__assets</b> table.')
			);
		}
	}

	/**
	 * Remove action logs extensions integrated
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeActionLogsExtensions(): void
	{
		// Remove ehealthportal from the action_logs_extensions table
		$extension = [
			$this->db->quoteName('extension') . ' = ' . $this->db->quote('com_ehealthportal')
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__action_logs_extensions'));
		$query->where($extension);
		$this->db->setQuery($query);

		// Execute the query to remove ehealthportal
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully remove ehealthportal add queued success message.
			$this->app->enqueueMessage(
				Text::_('The (com_ehealthportal) extension was removed from the <b>#__action_logs_extensions</b> table.')
			);
		}
	}

	/**
	 * Remove remove database fix (if possible)
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeDatabaseAssetsRulesFix(): void
	{
		// Get the biggest rule column in the assets table at this point.
		$length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$this->db->setQuery($length);
		if ($this->db->execute())
		{
			$rule_length = $this->db->loadResult();
			// Check the size of the rules column
			if ($rule_length < 5120)
			{
				// Revert the assets table rules column back to the default
				$revert_rule = "ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';";
				$this->db->setQuery($revert_rule);
				$this->db->execute();

				$this->app->enqueueMessage(
					Text::_('Reverted the <b>#__assets</b> table rules column back to its default size of varchar(5120).')
				);
			}
			else
			{
				$this->app->enqueueMessage(
					Text::_('Could not revert the <b>#__assets</b> table rules column back to its default size of varchar(5120), since there is still one or more components that still requires the column to be larger.')
				);
			}
		}
	}

	/**
	 * Method to move folders into place.
	 *
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function moveFolders(InstallerAdapter $adapter): void
	{
		// get the installation path
		$installer = $adapter->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = Folder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = ['media','admin','site']; // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!Folder::copy($src, $dest, '', true))
					{
						$this->app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
