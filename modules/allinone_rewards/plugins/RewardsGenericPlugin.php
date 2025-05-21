<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

abstract class RewardsGenericPlugin
{
	protected $_errors = array();
	public $instance;
	public $context;
	public $name;
	public $id_template = 0;
	public $rewardStateDefault;
	public $rewardStateValidation;
	public $rewardStateCancel;
	public $rewardStateConvert;
	public $rewardStateReturnPeriod;
	public $rewardStateWaitingPayment;
	public $rewardStatePaid;
	private static $_cache = array();

	public function __construct($module)
	{
		$this->instance = $module;
		$this->context = Context::getContext();
	}

	public function checkWarning() {
	}

	protected function registerHook($hookName)
	{
		// register hook only once during installation, else crash on prestashop 8.x
		if (!isset(self::$_cache[$hookName])) {
			self::$_cache[$hookName] = true;
			return $this->instance->registerHook($hookName);
		}
		return true;
	}

	public function l($string, $lang_id=null, $specific=null)
	{
		return $this->instance->l2($string, $lang_id, isset($specific) ? $specific : Tools::strtolower(get_class($this)));
	}

	protected function initTemplate()
	{
		// traitement global des actions sur le template, valable pour tous les plugins
		$this->id_template = (int)Tools::getValue('rewards_'.$this->name.'_template_id');
		$reward_action=Tools::getValue('rewards_template_action');
		if ($reward_action && $this->name==Tools::getValue('plugin')) {
			switch($reward_action) {
				case 'add_groups':
					die(json_encode(RewardsTemplateModel::addGroups($this->id_template, $this->name, Tools::getValue('add_groups'))));
				case 'list_customer':
					die(json_encode(RewardsTemplateModel::getCustomersForFilter($this->name, version_compare(_PS_VERSION_, '1.6', '>=') ? Tools::getValue('q') : Tools::getValue('term'))));
				case 'add_customer':
					die(json_encode(RewardsTemplateModel::addCustomer($this->id_template, Tools::getValue('id_customer'))));
				case 'delete_customer':
					die(json_encode(RewardsTemplateModel::deleteCustomer($this->id_template, Tools::getValue('id_customer'))));
				case 'add_customers_from_group':
					die(json_encode(RewardsTemplateModel::addCustomersFromGroup($this->id_template, $this->name, Tools::getValue('add_from_group'))));
				case 'create':
					$template = new RewardsTemplateModel();
					$template->name = Tools::getValue('rewards_template_name');
					$template->plugin = $this->name;
					try {
						if (!$template->add())
							$this->instance->errors = $this->instance->displayError($this->l('That name is already used by another template', null, 'rewardsgenericplugin'));
						$this->id_template = $template->id;
					} catch (Exception $e) {
						$this->instance->errors = $this->instance->displayError($this->l('That name is already used by another template', null, 'rewardsgenericplugin'));
					}
					break;
				case 'duplicate':
					if ($this->id_template != 0) {
						$template = new RewardsTemplateModel($this->id_template);
						try {
							if (!$template->duplicate(Tools::getValue('rewards_template_name')))
								$this->instance->errors = $this->instance->displayError($this->l('That name is already used by another template', null, 'rewardsgenericplugin'));
							$this->id_template = $template->id;
						} catch (Exception $e) {
							$this->instance->errors = $this->instance->displayError($this->l('That name is already used by another template', null, 'rewardsgenericplugin'));
						}
					}
					break;
				case 'rename':
					if ($this->id_template != 0) {
						$template = new RewardsTemplateModel($this->id_template);
						$template->name = Tools::getValue('rewards_template_name');
						try {
							if (!$template->save())
								$this->instance->errors = $this->instance->displayError($this->l('That name is already used by another template', null, 'rewardsgenericplugin'));
						} catch (Exception $e) {
							$this->instance->errors = $this->instance->displayError($this->l('That name is already used by another template', null, 'rewardsgenericplugin'));
						}
					}
					break;
				case 'delete':
					if ($this->id_template != 0) {
						$template = new RewardsTemplateModel($this->id_template);
						$template->delete();
						$this->id_template = 0;
					}
					break;
				default:
					break;
			}
		}
	}

	protected function getTemplateForm($title) {
		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'title' => $title,
			'templates' => RewardsTemplateModel::getList($this->name),
			'plugin' => Tools::getValue('plugin'),
			'groups' => Group::getGroups($this->context->language->id),
			'groups_off' => array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP')),
			'add_groups' => explode(',', RewardsTemplateModel::getGroups($this->id_template)),
			'customers' => RewardsTemplateModel::getCustomers($this->id_template)
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-template-form.tpl');
	}

	protected function getCategoriesTree($categories) {
		$root = Category::getRootCategory();
		if (version_compare(_PS_VERSION_, '1.6', '>=')) {
			$tree = new HelperTreeCategories('categoryBox');
			$tree->setRootCategory($root->id)
				->setInputName('categoryBox')
				->setUseCheckBox(true)
				->setUseSearch(false)
				->setSelectedCategories($categories);
			return $tree->render();
		} else {
			$tab_root = array('id_category' => $root->id, 'name' => $root->name);
			$helper = new Helper();
			return $helper->renderCategoryTree($tab_root, $categories, 'categoryBox', false, false, array(), false, true);
		}
	}

	public function instanceDefaultStates() {
		$this->rewardStateDefault = new RewardsStateModel(RewardsStateModel::getDefaultId());
		$this->rewardStateValidation = new RewardsStateModel(RewardsStateModel::getValidationId());
		$this->rewardStateCancel = new RewardsStateModel(RewardsStateModel::getCancelId());
		$this->rewardStateConvert = new RewardsStateModel(RewardsStateModel::getConvertId());
		$this->rewardStateReturnPeriod = new RewardsStateModel(RewardsStateModel::getReturnPeriodId());
		$this->rewardStateWaitingPayment = new RewardsStateModel(RewardsStateModel::getWaitingPaymentId());
		$this->rewardStatePaid = new RewardsStateModel(RewardsStateModel::getPaidId());
	}

	abstract public function install();
	abstract public function uninstall();
	abstract protected function postProcess($params=null);
	abstract public function isActive();
	abstract public function isRewardsAccountVisible();
	abstract public function getTitle();
	abstract public function getDetails($reward, $admin);
	abstract public function displayForm();
}