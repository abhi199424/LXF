<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class VerifiedReviewsAPI extends ReviewGenericAPI
{
    public function getTitle() {
        return $this->l('Verified Reviews (Skeepers)');
    }

    public function getCode() {
        return 'VR';
    }

    public function displayForm($id_template) {
        $this->context->smarty->assign(array(
            'review_vr_folder' => Tools::getValue('review_vr_folder', Configuration::get('RREVIEW_VR_FOLDER') ? Configuration::get('RREVIEW_VR_FOLDER') : _PS_ROOT_DIR_.'/'),
        ));
        return $this->instance->display($this->instance->path, 'views/templates/admin/api/verifiedreviews.tpl');
    }

    public function postValidation(&$errors)
    {
        if (empty(Tools::getValue('review_vr_folder')))
            $errors[] = $this->l('The folder path is required.');
        if (!file_exists(Tools::getValue('review_vr_folder')))
            $errors[] = $this->l('The folder path is invalid, that folder doesn\'t exist.');
    }

    public function postProcess($id_template) {
        Configuration::updateValue('RREVIEW_VR_FOLDER', Tools::getValue('review_vr_folder'));
    }

    public function getReviews($id_template, $type) {
        $date_from = DateTime::createFromFormat('Y-m-d H:i:s', $this->getLastCheck($id_template, $type).' 00:00:00');
        $folder = Configuration::get('RREVIEW_VR_FOLDER');
        $valid_reviews = [];
        $reviews_ids = [];
        if (file_exists($folder)) {
            // on traite les fichiers all_reviews les plus récents et les fichiers quotidiens dont la date est supérieure à la date_from
            foreach (glob($folder . '/*.xml') as $file) {
                $filename = basename($file);
                // Extrait la date à la fin du nom de fichier (au format aaaammjj)
                if (preg_match('/reviews_' . preg_quote($type, '/') . '.*_(\d{8})\.xml$/', $filename, $match)) {
                    $date_file = DateTime::createFromFormat('Ymd', $match[1]);
                    if ($date_file && $date_file >= $date_from) {
                        $xml = simplexml_load_file($file);
                        if (!$xml)
                            continue;
                        foreach ($xml->review as $review) {
                            $publish_date = DateTime::createFromFormat('Y-m-d H:i:s', (string)$review->publish_date);
                            if ((string)$review['action']=='NEW' && $publish_date && $publish_date >= $date_from && !isset($reviews_ids[(string)$review->review_id])) {
                                $reviews_ids[(string)$review->review_id] = 1;
                                $reward_review = new RewardsReviewModel();
                                $reward_review->id_review = (string)$review->review_id;
                                $reward_review->api = $this->getName();
                                $reward_review->id_order = (int)$review->order_ref;
                                $reward_review->email = (string)$review->email;
                                $reward_review->rating = (int)$review->rate;
                                $reward_review->comment = $review->review;
                                $reward_review->type = $type;
                                $reward_review->id_product = $type=='product' ? (int)18 : 0;
                                $reward_review->date_add = (string)$review->publish_date;
                                $valid_reviews[] = $reward_review;
                            }
                        }
                    }
                }
            }
        }
        return $valid_reviews;
    }
}