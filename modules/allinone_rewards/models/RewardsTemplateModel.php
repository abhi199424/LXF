<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class RewardsTemplateModel extends ObjectModel
{
	public $id_template;
	public $name;
	public $groups;
	public $plugin;

	public static $definition = array(
		'table' => 'rewards_template',
		'primary' => 'id_template',
		'fields' => array(
			'name' =>			array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 100),
			'plugin' =>			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 20),
			'groups' =>			array('type' => self::TYPE_STRING),
		)
	);

	static private function _loadConfiguration($id_template, $force=false)
	{
		if (!Cache::isStored('aior_templates_config') || $force){
			$cache = array();
			$cache[$id_template] = array();
			$query = 'SELECT r.`name`, rl.`id_lang`, IF(rl.`id_lang` IS NULL, r.`value`, rl.`value`) AS value
					FROM `'._DB_PREFIX_.'rewards_template_config` r
					LEFT JOIN `'._DB_PREFIX_.'rewards_template_config_lang` rl ON (r.`id_template_config` = rl.`id_template_config`)
					WHERE r.`id_template`='.(int)$id_template;
			$rows = Db::getInstance()->executeS($query);
			if (is_array($rows)) {
				foreach ($rows as $row) {
					$lang = ($row['id_lang']) ? $row['id_lang'] : 0;
					$cache[$id_template][$lang][$row['name']] = $row['value'];
				}
			}
			Cache::store('aior_templates_config', $cache);
		}
		return Cache::retrieve('aior_templates_config');
	}

	static private function _getIdByName($id_template, $key)
	{
		$query = 'SELECT `id_template_config`
				FROM `'._DB_PREFIX_.'rewards_template_config`
				WHERE name = \''.pSQL($key).'\'
				AND id_template = '.(int)$id_template;
		return (int)Db::getInstance()->getValue($query);
	}

	static private function _hasKey($id_template, $key, $id_lang)
	{
		$cache = self::_loadConfiguration($id_template);
		return isset($cache[$id_template][$id_lang]) && array_key_exists($key, $cache[$id_template][$id_lang]);
	}

	static public function get($id_template, $key, $id_lang=0)
	{
		$cache = self::_loadConfiguration($id_template);
		return self::_hasKey($id_template, $key, $id_lang) ? $cache[$id_template][$id_lang][$key] : false;
	}

	static public function updateValue($id_template, $key, $values, $html = false)
	{
		if (!Validate::isConfigName($key))
			die(Tools::displayError());

		if (!is_array($values))
			$values = array($values);

		$result = true;
		foreach ($values as $id_lang => $value) {
			// if there isn't a $stored_value, we must insert $value
			if ($value === self::get($id_template, $key, $id_lang))
				continue;

			// If key already exists, update value
			if (self::_hasKey($id_template, $key, $id_lang))	{
				if (!$id_lang) {
					// Update config not linked to lang
					$query = 'UPDATE '._DB_PREFIX_.'rewards_template_config c
							SET c.value = \''.pSQL($value, $html).'\'
							WHERE c.name = \''.pSQL($key).'\'
							AND c.id_template = '.(int)$id_template;
					$result &= Db::getInstance()->execute($query);
				} else {
					// Update multi lang
					$query = 'UPDATE '._DB_PREFIX_.'rewards_template_config_lang cl
							SET cl.value = \''.pSQL($value, $html).'\'
							WHERE cl.id_lang = '.(int)$id_lang.'
							AND cl.id_template_config = (
								SELECT c.id_template_config
								FROM '._DB_PREFIX_.'rewards_template_config c
								WHERE c.name = \''.pSQL($key).'\'
								AND c.id_template = '.(int)$id_template.'
							)';
					$result &= Db::getInstance()->execute($query);
				}
			} else {
				if (!$id_template_config = self::_getIdByName($id_template, $key)) {
					Db::getInstance()->insert('rewards_template_config', array(
						'id_template' => (int)$id_template,
						'name' => $key,
						'value' => !$id_lang ? pSQL($value, $html) : ''
					));

					$id_template_config = (int)Db::getInstance()->getValue('
						SELECT `id_template_config`
						FROM `'._DB_PREFIX_.'rewards_template_config`
						WHERE `name` = \''.pSQL($key).'\'
						AND id_template = '.(int)$id_template);
				}
				if ($id_lang) {
					$result &= Db::getInstance()->insert('rewards_template_config_lang', array(
						'id_template_config' => (int)$id_template_config,
						'id_lang' => (int)$id_lang,
						'value' => pSQL($value, $html)
					));
				}
			}
		}
		self::_loadConfiguration($id_template, true);
		return $result;
	}

	static public function isActiveAtLeastOnce($key)
	{
		$row = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'rewards_template_config` WHERE `value`=1 AND `name`=\''.pSQL($key).'\'');
		if ($row)
			return true;
		return false;
	}

	public function duplicate($name)
	{
		$id_template = $this->id;
		$cache = self::_loadConfiguration($id_template);

		$this->id = null;
		$this->name = $name;
		$this->groups = '';

		if ($this->add()) {
			$done = array();
			foreach($cache[$id_template] as $id_lang => $tabs) {
				foreach($tabs as $key => $value) {
					if (!isset($done[$key])) {
						$result = Db::getInstance()->insert('rewards_template_config', array(
							'id_template' => (int)$this->id,
							'name' => pSQL($key),
							'value' => !$id_lang ? pSQL($value, true) : ''
						));
						$id_template_config = (int)Db::getInstance()->getValue('
							SELECT `id_template_config`
							FROM `'._DB_PREFIX_.'rewards_template_config`
							WHERE `name` = \''.pSQL($key).'\'
							AND id_template = '.(int)$this->id);
						$done[$key] = $id_template_config;
					}
					if ($id_lang) {
						$result &= Db::getInstance()->insert('rewards_template_config_lang', array(
							'id_template_config' => (int)$done[$key],
							'id_lang' => (int)$id_lang,
							'value' => pSQL($value, true)
						));
					}
				}
			}
			self::_loadConfiguration($id_template, true);
			return true;
		}
		return false;
	}

	public function delete()
	{
		if ($this->id) {
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_template_config_lang` WHERE `id_template_config` IN (SELECT id_template_config FROM `'._DB_PREFIX_.'rewards_template_config` WHERE `id_template`='.(int)$this->id.')');
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_template_config` WHERE `id_template`='.(int)$this->id);
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_template_customer` WHERE `id_template`='.(int)$this->id);
			return parent::delete();
		}
		return true;
	}

	static public function deleteByName($key)
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_template_config` WHERE `name`=\''.pSQL($key).'\'');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_template_config_lang` WHERE id_template_config NOT IN (SELECT id_template_config FROM `'._DB_PREFIX_.'rewards_template_config`)');
		return true;
	}

	// retourne la liste des templates pour un plugin
	static public function getList($plugin)
	{
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'rewards_template` WHERE `plugin`=\''.pSQL($plugin).'\' ORDER BY id_template ASC');
	}

	// retourne la liste des groupes clients associés à un template
	static public function getGroups($id_template) {
		return Db::getInstance()->getValue('SELECT `groups` FROM `'._DB_PREFIX_.'rewards_template` WHERE `id_template`='.(int)$id_template);
	}

	// retourne la liste des clients associés à un template
	static public function getCustomers($id_template) {
		return Db::getInstance()->executeS('
			SELECT c.id_customer, c.firstname, c.lastname, c.email
			FROM `'._DB_PREFIX_.'rewards_template_customer`
			JOIN `'._DB_PREFIX_.'customer` c USING (id_customer)
			WHERE `id_template`='.(int)$id_template);
	}

	// charge le cache des templates pour un client
	static private function _loadTemplatesForCustomer($id_customer, $force=false) {
		if (!Cache::isStored('aior_template_'.$id_customer) || $force){
			$cache_customer = array();
			$query = 'SELECT DISTINCT t.`id_template`, t.`plugin`, t.`name`
				FROM `'._DB_PREFIX_.'rewards_template` t
				JOIN `'._DB_PREFIX_.'rewards_template_customer` USING (`id_template`) WHERE `id_customer`='.(int)$id_customer;
			$rows = Db::getInstance()->executeS($query);
			if (is_array($rows)) {
				foreach ($rows as $row)
					$cache_customer[$id_customer][$row['plugin']] = $row['id_template'];
			}

			$query = 'SELECT DISTINCT t.`id_template`, t.`plugin`, t.`name`
				FROM `'._DB_PREFIX_.'rewards_template` t
				WHERE t.`groups` LIKE (SELECT CONCAT(\'%,\', `id_default_group`, \',%\') FROM `'._DB_PREFIX_.'customer` WHERE `id_customer`='.(int)$id_customer.')';

			$rows = Db::getInstance()->executeS($query);
			if (is_array($rows)) {
				foreach ($rows as $row) {
					if (!isset($cache_customer[$id_customer][$row['plugin']]))
						$cache_customer[$id_customer][$row['plugin']] = $row['id_template'];
				}
			}
			Cache::store('aior_template_'.$id_customer, $cache_customer);
		}
		return Cache::retrieve('aior_template_'.$id_customer);
	}

	// retourne l'id template associé à un client pour un plugin donné, s'il existe
	static public function getIdTemplate($plugin, $id_customer) {
		$cache = self::_loadTemplatesForCustomer($id_customer);
		return isset($cache[$id_customer][$plugin]) ? (int)$cache[$id_customer][$plugin] : 0;
	}

	static public function getCustomersForFilter($plugin, $filter) {
		$query = '	SELECT c.`id_customer`, c.`firstname`, c.`lastname`, c.`email`
					FROM `'._DB_PREFIX_.'customer` c
					WHERE `id_customer` NOT IN (
						SELECT `id_customer`
						FROM `'._DB_PREFIX_.'rewards_template_customer`
						JOIN `'._DB_PREFIX_.'rewards_template` USING (`id_template`)
						WHERE `plugin`=\''.pSQL($plugin).'\'
					)
					AND (
						c.`id_customer` = '.(int)$filter.'
						OR c.`firstname` LIKE "%'.pSQL($filter).'%"
						OR c.`lastname` LIKE "%'.pSQL($filter).'%"
						OR c.`email` LIKE "%'.pSQL($filter).'%"
					)';
		return Db::getInstance()->executeS($query);
	}

	static public function addGroups($id_template, $plugin, $groups) {
		if (is_array($groups)) {
			// delete the checked groups from other templates
			foreach($groups as $group)
				Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_template` SET `groups`=IF(`groups`=\','.(int)$group.',\', \'\', REPLACE(`groups`, \','.(int)$group.',\', \',\')) WHERE `id_template`!='.(int)$id_template.' AND `plugin`=\''.pSQL($plugin).'\'');
			$groups = ','.implode(',', array_map('intval', $groups)).',';
		} else
			$groups = '';
		$result = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_template` SET `groups`="'.$groups.'" WHERE `id_template`='.(int)$id_template);
		return $result;
	}

	static public function addCustomer($id_template, $id_customer) {
		$result = Db::getInstance()->insert('rewards_template_customer', array(
			'id_template' => (int)$id_template,
			'id_customer' => (int)$id_customer
		));
		self::_loadTemplatesForCustomer($id_customer, true);
		return $result;
	}

	static public function deleteCustomer($id_template, $id_customer) {
		$result = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_template_customer` WHERE `id_template`='.(int)$id_template.' AND `id_customer`='.(int)$id_customer);
		self::_loadTemplatesForCustomer($id_customer, true);
		return $result;
	}

	static public function addCustomersFromGroup($id_template, $plugin, $id_group) {
		$query = '
			DELETE FROM `'._DB_PREFIX_.'rewards_template_customer`
			WHERE `id_template`!='.(int)$id_template.'
				AND `id_template` IN (
					SELECT `id_template`
					FROM `'._DB_PREFIX_.'rewards_template`
					WHERE plugin=\''.pSQL($plugin).'\')
				AND `id_customer` IN (
					SELECT `id_customer`
					FROM `'._DB_PREFIX_.'customer_group`
					WHERE `id_group`='.(int)$id_group.')';
		Db::getInstance()->execute($query);

		$query = '
			SELECT c.id_customer, c.firstname, c.lastname, c.email
			FROM `'._DB_PREFIX_.'customer_group` cg
			JOIN `'._DB_PREFIX_.'customer` c USING (`id_customer`)
			WHERE cg.`id_group` = '.(int)$id_group.'
				AND c.`deleted` != 1
				AND c.`id_customer` NOT IN (
					SELECT `id_customer`
					FROM `'._DB_PREFIX_.'rewards_template_customer`
					WHERE `id_template`='.(int)$id_template.'
				)';
		$result = Db::getInstance()->executeS($query);

		$query = '
			INSERT INTO `'._DB_PREFIX_.'rewards_template_customer`
			SELECT '.(int)$id_template.', c.id_customer
			FROM `'._DB_PREFIX_.'customer_group` cg
			JOIN `'._DB_PREFIX_.'customer` c USING (`id_customer`)
			WHERE cg.`id_group` = '.(int)$id_group.'
				AND c.`deleted` != 1
				AND c.`id_customer` NOT IN (
					SELECT `id_customer`
					FROM `'._DB_PREFIX_.'rewards_template_customer`
					WHERE `id_template`='.(int)$id_template.'
				)';
		Db::getInstance()->execute($query);
		return $result;
	}
}

class MyConf {
	static public function get($key, $id_lang=null, $id_template=0)
	{
		// try to load the key from the template, else return the default value
		if (!empty($id_template)) {
			$value = RewardsTemplateModel::get($id_template, $key, (int)$id_lang);
			if ($value!==false)
				return $value;
		}
		return Configuration::get($key, $id_lang);
	}

	static public function updateValue($key, $values, $html = false, $id_template=0)
	{
		if (!empty($id_template))
			return RewardsTemplateModel::updateValue($id_template, $key, $values, $html);
		else {
			$result = Configuration::updateValue($key, $values, $html);
			if (version_compare(_PS_VERSION_, '1.5.2', '<') && $html)
				Configuration::set($key, $values);
			return $result;
		}
	}

	static public function deleteByName($key)
	{
		return RewardsTemplateModel::deleteByName($key);
	}

	static public function getIdTemplate($plugin, $id_customer)
	{
		if ($id_customer)
			return RewardsTemplateModel::getIdTemplate($plugin, (int)$id_customer);
		return 0;
	}

	static public function isActiveAtLeastOnce($key)
	{
		return RewardsTemplateModel::isActiveAtLeastOnce($key);
	}
}
