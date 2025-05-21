<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class ProductCommentsAPI extends ReviewGenericAPI
{
    public function getTitle() {
        return $this->l('Products comments (native Prestashop module)');
    }

    public function getCode() {
        return 'PC';
    }

    // on repart toujours de la date indiquée, car il n'y a pas de date d'update sur la table et la validation d'un commentaire ne modifie pas cette date
    public function getLastCheck($id_template, $type) {
        return MyConf::get('RREVIEW_'.strtoupper($type).'_FROM', null, $id_template);
    }

    public function setLastCheck($id_template, $type, $date) {
        return false;
    }

    public function getReviews($id_template, $type) {
        $date_from = $this->getLastCheck($id_template, $type);
        $valid_reviews = [];
        if ($type=='product' && $reviews = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'product_comment` WHERE '.(Configuration::get('PRODUCT_COMMENTS_MODERATE') ? '`validate`=1 AND ' : '').'`deleted`=0 AND `id_customer`!=0 AND date_add >= \''.pSQL($this->getLastCheck($id_template, $type)).'\' AND id_product_comment NOT IN (SELECT id_review FROM `'._DB_PREFIX_.'rewards_review` WHERE api=\''.pSQL($this->getName()).'\')')) {
            foreach($reviews as $review) {
                $reward_review = new RewardsReviewModel();
                $reward_review->id_review = $review['id_product_comment'];
                $reward_review->api = $this->getName();
                $reward_review->id_order = 0;
                $reward_review->id_customer = $review['id_customer'];
                $reward_review->rating = (int)$review['grade'];
                $reward_review->comment = $review['content'];
                $reward_review->type = 'product';
                $reward_review->id_product = (int)$review['id_product'];
                $reward_review->date_add = $review['date_add'];
                $valid_reviews[] = $reward_review;
            }
        }
        return $valid_reviews;
    }
}