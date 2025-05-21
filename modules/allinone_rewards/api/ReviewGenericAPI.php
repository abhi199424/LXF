<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

abstract class ReviewGenericAPI
{
    protected $instance;
    protected $context;

    public function __construct($module)
    {
        $this->instance = $module;
        $this->context = Context::getContext();
    }

    public function l($string, $lang_id=null, $specific=null)
    {
        return $this->instance->l2($string, $lang_id, isset($specific) ? $specific : Tools::strtolower(get_class($this)));
    }

    public function display($template)
    {
        return $this->instance->display($this->instance->path, $template);
    }

    public function getName() {
        return get_class($this);
    }

    public function displayForm($id_template) {
    }

    public function postValidation(&$errors) {
    }

    public function postProcess($id_template) {
    }

    public function getLastCheck($id_template, $type) {
        $date_from = MyConf::get('RREVIEW_'.$this->getCode().'_'.strtoupper($type).'_LAST_CHECK', null, $id_template);
        if (empty($date_from))
            $date_from = MyConf::get('RREVIEW_'.strtoupper($type).'_FROM', null, $id_template);
        return $date_from;
    }

    public function setLastCheck($id_template, $type, $date) {
        $date = date('Y-m-d', strtotime($date));
        $date_from = MyConf::get('RREVIEW_'.$this->getCode().'_'.strtoupper($type).'_LAST_CHECK', null, $id_template);
        if (empty($date_from) || $date > $date_from)
            MyConf::updateValue('RREVIEW_'.$this->getCode().'_'.strtoupper($type).'_LAST_CHECK', $date, null, $id_template);
    }

    abstract public function getTitle();
    abstract public function getCode();
    abstract public function getReviews($id_template, $type);
}