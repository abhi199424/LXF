<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class ShopProductReviewsAPI extends ReviewGenericAPI
{
    public function getTitle() {
        return $this->l('Shop Product Reviews : Customer Reviews Product & Shop (Business Tech)');
    }

    public function getCode() {
        return 'SPR';
    }

    public function displayForm($id_template) {
        return $this->instance->display($this->instance->path, 'views/templates/admin/api/shopproductreviews.tpl');
    }

    public function getReviews($id_template, $type) {
        if (Module::isInstalled('gsnippetreviews') && $mod=Module::getInstanceByName('gsnippetreviews')) {
            $date_from = $this->getLastCheck($id_template, $type);
            $valid_reviews = [];
            if ($reviews = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'bt_spr_'.($type=='product' ? 'products':'shop').'_reviews` WHERE `review_status`=1 AND `id_customer`!=0 AND date_upd >= \''.pSQL($this->getLastCheck($id_template, $type)).'\' AND id_review NOT IN (SELECT id_review FROM `'._DB_PREFIX_.'rewards_review` WHERE api=\''.pSQL($this->getName()).'\')')) {
                foreach($reviews as $review) {
                    $reward_review = new RewardsReviewModel();
                    $reward_review->id_review = $review['id_review'];
                    $reward_review->api = $this->getName();
                    $reward_review->id_order = (int)$review['id_order'];
                    $reward_review->id_customer = $review['id_customer'];
                    $reward_review->rating = (int)$review['rating_value'];
                    $reward_review->comment = $review['text_review'];
                    $reward_review->type = $type;
                    $reward_review->id_product = $type=='product' ? (int)$review['id_product'] : 0;
                    $reward_review->date_add = $review['date_add'];
                    $valid_reviews[] = $reward_review;
                }
            }
            return $valid_reviews;
        } else
            return false;
    }
}