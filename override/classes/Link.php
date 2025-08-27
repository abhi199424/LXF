<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class Link extends LinkCore
{
    /*
    * module: ets_superspeed
    * date: 2025-08-04 17:27:06
    * version: 2.0.5
    */
    public function getImageLink($name, $ids, $type = null, string $extension = 'jpg')
    {
        $is_webp = false;
        if (Tools::strpos($ids, 'default') !== false) {
            $uriPath = _THEME_PROD_DIR_ . $ids . ($type ? '-' . $type : '') . '.jpg';
            if(file_exists(_PS_PROD_IMG_DIR_ . $ids . ($type ? '-' . $type : '')  . '.webp'))
                $is_webp = true;
        } else {
            $splitIds = explode('-', $ids);
            $idImage = (isset($splitIds[1]) ? $splitIds[1] : $splitIds[0]);
            if ($this->allow == 1) {
                $uriPath = __PS_BASE_URI__ . $idImage . ($type ? '-' . $type : '')  . '/' . $name . '.jpg';
            } else {
                $uriPath = _THEME_PROD_DIR_ . Image::getImgFolderStatic($idImage) . $idImage . ($type ? '-' . $type : '')  . '.jpg';
            }
            if(file_exists(_PS_PROD_IMG_DIR_ . Image::getImgFolderStatic($idImage) . $idImage . ($type ? '-' . $type : '') . '.webp'))
                $is_webp = true;
        }
        if($is_webp)
        {
            $url = $this->protocol_content . Tools::getMediaServer($uriPath) . $uriPath;
            return str_replace('.jpg','.webp',$url);
        }
        else
            return $this->protocol_content . Tools::getMediaServer($uriPath) . $uriPath;
    }
    /*
    * module: ets_seo
    * date: 2025-08-04 17:34:40
    * version: 3.0.6
    */
    protected function getLangLink($idLang = null, Context $context = null, $idShop = null)
    {
        $langLink = parent::getLangLink($idLang, $context, $idShop);
        if (!Module::getInstanceByName('ets_seo')) {
            return $langLink;
        }
        if (!$context) {
            $context = Ets_Seo::getContextStatic();
        }
        if (!$idLang) {
            $idLang = $context->language->id;
        }
        if (Language::isMultiLanguageActivated($idShop) && (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL') && $idLang == (int) Configuration::get('PS_LANG_DEFAULT')) {
            return '';
        }
        return $langLink;
    }
    /*
    * module: ets_seo
    * date: 2025-08-04 17:34:40
    * version: 3.0.6
    */
    public function getCategoryLink(
        $category,
        $alias = null,
        $idLang = null,
        $selectedFilters = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!Module::isEnabled('ets_seo')) {
            return parent::getCategoryLink($category, $alias, $idLang, $selectedFilters, $idShop, $relativeProtocol);
        }
        
        $dispatcher = Dispatcher::getInstance();
        if (!$idLang) {
            $idLang = Ets_Seo::getContextStatic()->language->id;
        }
        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);
        $params = [];
        if (Validate::isLoadedObject($category)) {
            $params['id'] = $category->id;
        } elseif (isset($category['id_category'])) {
            $params['id'] = $category['id_category'];
        } elseif (is_int($category) or ctype_digit($category)) {
            $params['id'] = (int) $category;
        } else {
            throw new \InvalidArgumentException('Invalid category parameter');
        }
        $selectedFilters = null === $selectedFilters ? '' : $selectedFilters;
        if (empty($selectedFilters)) {
            $rule = 'category_rule';
        } else {
            $rule = 'layered_rule';
            $params['selected_filters'] = $selectedFilters;
        }
        if (!$alias) {
            $category = $this->getCategoryObject($category, $idLang);
        }
        $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
        if ($dispatcher->hasKeyword($rule, $idLang, 'parent_rewrite', $idShop)) {
            $params['parent_rewrite'] = '';
            try {
                $cats = [];
                
                $currentCategory = $this->getCategoryObject($category, $idLang);
                foreach ($currentCategory->getParentsCategories($idLang) as $cat) {
                    if (!in_array($cat['id_category'], [1, 2, $currentCategory->id])) {
                        $cats[] = $cat['link_rewrite'];
                    }
                }
                if (count($cats)) {
                    $params['parent_rewrite'] = implode('/', array_reverse($cats));
                }
            } catch (PrestaShopException $e) {
            }
        }
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_keywords', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
        }
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_title', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
        }
        return $url . $dispatcher->createUrl($rule, $idLang, $params, $this->allow, '', $idShop);
    }
}
