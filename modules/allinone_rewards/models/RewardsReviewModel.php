<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class RewardsReviewModel extends ObjectModel
{
	public $id_customer;
	public $id_review;
	public $api;
	public $type;
	public $id_product;
	public $date_add;

	public static $definition = array(
		'table' => 'rewards_review',
		'primary' => 'id_rewards_review',
		'fields' => array(
			'id_reward' 	=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_review' 	=> array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 50),
			'api' 			=> array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 50),
			'type' 			=> array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 20),
			'id_product'	=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'date_add' 		=>	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	static public function getRewardDetails($id_reward)
	{
		$query = 'SELECT r.`id_order`, rr.`type`, rr.`id_product`, o.`reference`
				FROM `'._DB_PREFIX_.'rewards_review` rr
				JOIN `'._DB_PREFIX_.'rewards` r USING (`id_reward`)
				LEFT JOIN `'._DB_PREFIX_.'orders` o USING (`id_order`)
				WHERE `id_reward`='.(int)$id_reward;
		return Db::getInstance()->getRow($query);
	}

	static public function getValidatedReviews($api, $type='product', $id_template) {
		$context = Context::getContext();

        $shopGroup = Shop::getGroupFromShop(Shop::getContextShopID(), false);
        if (Shop::getContext() == Shop::CONTEXT_SHOP && $shopGroup['share_customer'])
            $where = ' AND `id_shop_group`='.(int)Shop::getContextShopGroupID();
        else
            $where = ' AND `id_shop` IN ('.implode(', ', Shop::getContextListShopID(Shop::SHARE_CUSTOMER)).')';

        $reviews = $api->getReviews($id_template, $type);
        if ($reviews !== false) {
            $valid_reviews = [];
            $nb_rewards_per_customer = [];
            $nb_rewards_per_product = [];
            foreach($reviews as $review) {
            	// si elle n'existe pas déjà
            	if (!Db::getInstance()->getValue('SELECT 1 FROM `'._DB_PREFIX_.'rewards_review` WHERE id_review=\''.pSQL($review->id_review).'\' AND api=\''.pSQL($review->api).'\' AND type=\''.pSQL($type).'\'')) {
	                // on regarde si on trouve le client actif, s'il est bien attaché à ce template là, et s'il respecte les conditions
	                if (isset($review->id_customer))
	                	$customer = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE deleted=0 AND active=1 AND id_customer=\''.(int)$review->id_customer.'\''.$where);
	                else
	                	$customer = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE deleted=0 AND active=1 AND email=\''.pSQL($review->email).'\''.$where);
	                if ($customer) {
	                    $customer_template = (int)MyConf::getIdTemplate('review', (int)$customer['id_customer']);
	                    if ($customer_template==$id_template) {
							$rating = (int)MyConf::get('RREVIEW_'.strtoupper($type).'_RATING', null, $id_template);
							if (!$rating || $review->rating >= $rating) {
								// check condition maximum review
								$max_rewards = (int)MyConf::get('RREVIEW_'.strtoupper($type).'_MAX', null, $id_template);
								if (!isset($nb_rewards_per_customer[(int)$customer['id_customer']]))
									$nb_rewards_per_customer[(int)$customer['id_customer']] = (int)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'rewards_review` JOIN `'._DB_PREFIX_.'rewards` USING (`id_reward`)  WHERE type=\''.pSQL($type).'\' AND id_reward_state!='.RewardsStateModel::getCancelId().' AND plugin=\'review\' AND `id_customer`='.(int)$customer['id_customer']);

								if (!$max_rewards || $max_rewards > $nb_rewards_per_customer[(int)$customer['id_customer']]) {
									// for list of reviews displayed in configuration
									$review->customer_name = $customer['firstname'].' '.$customer['lastname'];

									if ($type=='product') {
										// check condition maximum review per product
										$max_rewards_per_product = (int)MyConf::get('RREVIEW_MAX_PER_PRODUCT', null, $id_template);
										if (!isset($nb_rewards_per_product[(int)$customer['id_customer']][(int)$review->id_product]))
											$nb_rewards_per_product[(int)$customer['id_customer']][(int)$review->id_product] = (int)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'rewards_review` JOIN `'._DB_PREFIX_.'rewards` USING (`id_reward`)  WHERE type=\'product\' AND id_product='.(int)$review->id_product.' AND id_reward_state!='.RewardsStateModel::getCancelId().' AND plugin=\'review\' AND `id_customer`='.(int)$customer['id_customer']);

										if (!$max_rewards_per_product || $max_rewards_per_product > $nb_rewards_per_product[(int)$customer['id_customer']][(int)$review->id_product]) {
											$nb_rewards_per_customer[(int)$customer['id_customer']]++;
											$nb_rewards_per_product[(int)$customer['id_customer']][(int)$review->id_product]++;

											$review->id_customer = (int)$customer['id_customer'];

											// for list of reviews displayed in configuration
											$product = new Product((int)$review->id_product);
											$review->product_name = isset($product->name[$context->language->id]) ? $product->name[$context->language->id] : (int)$review->id_product;

											$valid_reviews[] = $review;
										}
									} else {
										$nb_rewards_per_customer[(int)$customer['id_customer']]++;
										$review->id_customer = (int)$customer['id_customer'];
										$valid_reviews[] = $review;
									}
								}
							}
	                    }
	                }
	            }
            }
            return $valid_reviews;
        }
        return false;
    }

	static public function generateRewards()
    {
    	$context = Context::getContext();
		$module = new allinone_rewards();
		$reviews = [];

		$classname = Configuration::get('RREVIEW_API');
		require_once(_PS_MODULE_DIR_.'/allinone_rewards/api/'.$classname.'.php');
		$api = new $classname($module);

        if ($templates=RewardsTemplateModel::getList('review'))
            $templates[] = ['id_template' => 0];
        else {
            $templates = [
                ['id_template' => 0]
            ];
        }

		$types = ['product', 'site'];
        foreach($templates as $template) {
        	foreach($types as $type) {
	            if ((int)MyConf::get('RREVIEW_ACTIVE', null, $template['id_template'])) {
		        	$reviews = [];
	                if ((int)MyConf::get('RREVIEW_'.strtoupper($type), null, $template['id_template']) && false!==($reviews=self::getValidatedReviews($api, $type, $template['id_template']))) {
				        foreach($reviews as $review) {
			            	$reference ='';
			            	$id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
							$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			            	if ((int)$review->id_order && $order=Db::getInstance()->getRow('SELECT reference, id_currency, id_lang FROM `'._DB_PREFIX_.'orders` WHERE id_order='.(int)$review->id_order)) {
			            		$reference = $order['reference'];
			            		$id_currency = $order['id_currency'];
			            		$id_lang = $order['id_lang'];
			            	}
			            	$currency = new Currency((int)$id_currency);

							$credits = (float)MyConf::get('RREVIEW_REWARD_'.strtoupper($type).'_VALUE_'.(int)$currency->id, null, $template['id_template']);
							$customer = new Customer((int)$review->id_customer);

							$reward = new RewardsModel();
							$reward->plugin = 'review';
							$reward->id_customer = (int)$customer->id;
							$reward->id_order = (int)$review->id_order;
							$reward->id_reward_state = RewardsStateModel::getValidationId();
							$reward->credits = round(Tools::convertPrice($credits, $currency, false), 2);
							if (Configuration::get('REWARDS_DURATION'))
								$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('REWARDS_DURATION'), date('Y')));
							if ($reward->save()) {
			                	// on enregistre la review
			                	$review->id_reward = $reward->id;
			                	$review->add(false);
			                	$api->setLastCheck($template['id_template'], $type, $review->date_add);

								if (Configuration::get('RREVIEW_MAIL', null, $template['id_template'])) {
									$reward_amount = $module->getRewardReadyForDisplay($credits, (int)$currency->id, $id_lang, false);
									$data = array(
										'{customer_firstname}' => $customer->firstname,
										'{customer_lastname}' => $customer->lastname,
										'{customer_reward}' => $reward_amount,
										'{order_reference}' => $reference,
										'{link_rewards}' => $context->link->getModuleLink('allinone_rewards', 'rewards', array(), true)
									);
									if ($review->id_product) {
										$product = new Product((int)$review->id_product);
										$data['{product}'] = isset($product->name[$id_lang]) ? $product->name[$id_lang] : (int)$review->id_product;
									}
									// DO NOT REMOVE
									// $this->l('You just got a new reward')
									//$module->sendMail($id_lang, 'review-'.$type.'-validation', $module->l2('You just got a new reward', (int)Configuration::get('PS_LANG_DEFAULT'), 'rewardsreviewmodel'), $data, $customer->email, $customer->firstname.' '.$customer->lastname);
								}
							}
			            }
			            $api->setLastCheck($template['id_template'], $type, date('Y-m-d'));
			        }
			    }
	        }
	    }
    }
}
