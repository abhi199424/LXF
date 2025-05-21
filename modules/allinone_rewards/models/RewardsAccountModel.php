<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class RewardsAccountModel extends ObjectModel
{
	public $id_customer;
	public $date_last_remind;
	public $remind_active = 1;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'rewards_account',
		'primary' => 'id_customer',
		'fields' => array(
			'id_customer' 		=>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'date_last_remind' 	=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'remind_active' 	=>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'date_add' 			=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 			=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	// list all people to remind having at least 1 reward which is older than REWARDS_REMINDER_FREQUENCY so we don't remind new accounts immediately
	// TODO : send an email only to the customer available in the current shop, because configuration could be different on another shop
	static public function sendReminder($id_customer=NULL) {
		if (Configuration::get('REWARDS_REMINDER') || !is_null($id_customer)) {
			$context = Context::getContext();
			$lang = (int)Configuration::get('PS_LANG_DEFAULT');

			$where  = '
				FROM `'._DB_PREFIX_.'rewards_account` AS ra
				JOIN `'._DB_PREFIX_.'customer` AS c ON(c.id_default_group!='.Configuration::get('PS_GUEST_GROUP').' AND ra.id_customer = c.id_customer AND c.deleted = 0 AND c.active = 1'.Shop::addSqlRestriction(false, 'c').')
				JOIN `'._DB_PREFIX_.'rewards` AS r ON(ra.id_customer = r.id_customer AND r.id_reward_state='.RewardsStateModel::getValidationId().')
				WHERE
				ra.remind_active = 1 AND '.
				(empty($id_customer) ? '
				(ra.date_last_remind IS NULL OR ra.date_last_remind=0 OR DATE_ADD(ra.date_last_remind, INTERVAL '.Configuration::get('REWARDS_REMINDER_FREQUENCY').' DAY) < NOW())
				AND EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'rewards` AS r2 WHERE r2.id_customer=r.id_customer AND DATE_ADD(r2.date_add, INTERVAL '.Configuration::get('REWARDS_REMINDER_FREQUENCY').' DAY) < NOW())
				GROUP BY ra.id_customer
				HAVING SUM(r.credits) >= '.(float)Configuration::get('REWARDS_REMINDER_MINIMUM') : ' ra.id_customer='.(int)$id_customer);

			$query = 'SELECT ra.id_customer, email, firstname, lastname, SUM(credits) AS total'.(version_compare(_PS_VERSION_, '1.5.4.0', '>=') ? ', id_lang':'').$where.(Configuration::get('REWARDS_USE_CRON') ? '' : ' LIMIT 20');
			if ($rows = Db::getInstance()->executeS($query)) {
				$module = new allinone_rewards();
				foreach ($rows as $row) {
					Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_account` ra2 SET date_last_remind=NOW() WHERE id_customer='.(int)$row['id_customer']);

					$id_template_core = (int)MyConf::getIdTemplate('core', $row['id_customer']);

					// on va chercher pour chacun son total expirant prochainement
					$row2 = Db::getInstance()->getRow('SELECT SUM(credits) AS total, date_end FROM `'._DB_PREFIX_.'rewards` WHERE id_reward_state='.RewardsStateModel::getValidationId().' AND id_customer='.$row['id_customer'].' AND date_end > NOW() GROUP BY date_end ORDER BY date_end ASC');

					if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
						$lang = (int)$row['id_lang'];

					$expire = '';
					if ($row2 && $row2['date_end'] && $row2['date_end']!='0000-00-00 00:00:00') {
						$date = version_compare(_PS_VERSION_, '8.0.0', '<') ? Tools::displayDate($row2['date_end'], null, true) : Tools::displayDate($row2['date_end'], true);
						$expire = '<br><br>'.sprintf($module->getL('expire', $lang), $module->getRewardReadyForDisplay((float)$row2['total'], (int)Configuration::get('PS_CURRENCY_DEFAULT'), $lang, true, $id_template_core), $date);
					}

					$data = array(
						'{firstname}' => $row['firstname'],
						'{lastname}' => $row['lastname'],
						'{rewards}' => $module->getRewardReadyForDisplay((float)$row['total'], (int)Configuration::get('PS_CURRENCY_DEFAULT'), $lang, true, $id_template_core),
						'{link_rewards}' => $context->link->getModuleLink('allinone_rewards', 'rewards', array(), true),
						'{expire}' => $expire,
					);
					$module->sendMail($lang, 'rewards-reminder', $module->getL('reminder', $lang), $data, $row['email'], $row['firstname'].' '.$row['lastname']);
				}
			}
		}
	}
}
