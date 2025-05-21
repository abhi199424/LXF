<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class GuaranteedReviewsAPI extends ReviewGenericAPI
{
    const MAX_REVIEW = 1000;
    const API_URL = 'https://api.guaranteed-reviews.com/private/v3/reviews';

    public function getTitle() {
        return $this->l('Guaranteed Reviews');
    }

    public function getCode() {
        return 'GR';
    }

    public function displayForm($id_template) {
        $this->context->smarty->assign(array(
            'review_gr_key' => Tools::getValue('review_gr_key', Configuration::get('RREVIEW_GR_KEY')),
        ));
        return $this->instance->display($this->instance->path, 'views/templates/admin/api/guaranteedreviews.tpl');
    }

    public function postValidation(&$errors)
    {
        if (empty(Tools::getValue('review_gr_key')))
            $errors[] = $this->l('The API key is required.');
    }

    public function postProcess($id_template) {
        Configuration::updateValue('RREVIEW_GR_KEY', Tools::getValue('review_gr_key'));
    }

    public function getReviews($id_template, $type) {
        $date_from = $this->getLastCheck($id_template, $type);
        $valid_reviews = [];
        $page = 1;
        if ($this->_getPaginatedReview($valid_reviews, $type, $date_from, $page))
            return $valid_reviews;
        return false;
    }

    private function _getPaginatedReview(&$valid_reviews, $type, $date_from, &$page) {
        $data = $this->_getApiReviews(['type' => $type, 'date_from' => $date_from, 'page' => $page]);
        if ($data && is_array($data) && isset($data['pagination']['total_results'])) {
            $total_pages = $data['pagination']['total_pages'];
            $reviews = $data['reviews'];
            if (isset($reviews) && is_array($reviews)) {
                foreach($reviews as $review) {
                    $reward_review = new RewardsReviewModel();
                    $reward_review->id_review = $review['id'];
                    $reward_review->api = $this->getName();
                    $reward_review->id_order = (int)$review['order'];
                    $reward_review->rating = (int)$review['review_rating'];
                    $reward_review->comment = $review['review_text'];
                    $reward_review->email = $review['reviewer_email'];
                    $reward_review->type = $type;
                    $reward_review->id_product = $type=='product' ? (int)$review['product'] : 0;
                    $reward_review->date_add = $review['date_time'];
                    $valid_reviews[] = $reward_review;
                }
                if ($page < $total_pages) {
                    $page++;
                    return $this->_getPaginatedReview($result, $type, $date_from, $page, $id_template, $where);
                }
                return true;
            }
            return true;
        }
        return false;
    }

    private function _getApiReviews($params) {
        if (function_exists('curl_init') && $curl = curl_init()) {
            curl_setopt_array($curl, [
                CURLOPT_URL => self::API_URL.'?api_key='.Configuration::get('RREVIEW_GR_KEY').'&sort=asc&status=1&review_type='.$params['type'].'&update_from='.$params['date_from'].'&limit='.self::MAX_REVIEW.'&page='.(int)$params['page'],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = @curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        }
        return false;
    }
}