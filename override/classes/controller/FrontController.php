<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class FrontController extends FrontControllerCore
{
    
    /*
    * module: ets_superspeed
    * date: 2025-08-04 17:27:06
    * version: 2.0.5
    */
    public function initContent()
    {
        if(Tools::isSubmit('ets_superseed_load_content') && Module::isEnabled('ets_superspeed'))
        {
            parent::initContent();
            Hook::exec('actionPageCacheAjax');
        }
        parent::initContent();
    }
    /*
    * module: ets_superspeed
    * date: 2025-08-04 17:27:06
    * version: 2.0.5
    */
    protected function smartyOutputContent($content)
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            ob_start();
            parent::smartyOutputContent($content);
            $html = ob_get_contents();
            ob_clean();
            Hook::exec('actionOutputHTMLBefore',  array('html' => &$html));
            echo $html;
        } else
            return parent::smartyOutputContent($content);
    }
    
    /*
    * module: ets_seo
    * date: 2025-08-04 17:34:40
    * version: 3.0.6
    */
    protected $redirectionExtraExcludedKeys = ['rewrite', 'category'];
    /*
    * module: ets_seo
    * date: 2025-08-04 17:34:40
    * version: 3.0.6
    */
    protected function redirect()
    {
        if (Module::isEnabled('ets_seo')) {
            Hook::exec('actionFrontControllerRedirectBefore', ['redirect_after' => $this->redirect_after, 'controller' => $this]);
        }
        parent::redirect();
    }
}
