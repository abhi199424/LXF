<?php
/**
* 2005-2017 Magic Toolbox
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    Magic Toolbox <support@magictoolbox.com>
*  @copyright Copyright (c) 2017 Magic Toolbox <support@magictoolbox.com>. All rights reserved
*  @license   https://www.magictoolbox.com/license/
*/

if (!defined('MAGICZOOMPLUS_MODULE_CORE_CLASS_LOADED')) {

    define('MAGICZOOMPLUS_MODULE_CORE_CLASS_LOADED', true);

    require_once(dirname(__FILE__) . '/magictoolbox.params.class.php');

    /**
     * MagicZoomPlusModuleCoreClass
     *
     */
    class MagicZoomPlusModuleCoreClass
    {

        /**
         * MagicToolboxParamsClass class
         *
         * @var   MagicToolboxParamsClass
         *
         */
        public $params;

        /**
         * Tool type
         *
         * @var   string
         *
         */
        public $type = 'standard';

        /**
         * Constructor
         *
         * @return void
         */
        public function __construct()
        {
            $this->params = new MagicToolboxParamsClass();
            $this->params->setScope('magiczoomplus');
            $this->params->setMapping(array(
                'zoomWidth' => array('0' => 'auto'),
                'zoomHeight' => array('0' => 'auto'),
                'expandCaption' => array('Yes' => 'true', 'No' => 'false'),
                'upscale' => array('Yes' => 'true', 'No' => 'false'),
                'lazyZoom' => array('Yes' => 'true', 'No' => 'false'),
                'closeOnClickOutside' => array('Yes' => 'true', 'No' => 'false'),
                'rightClick' => array('Yes' => 'true', 'No' => 'false'),
                'transitionEffect' => array('Yes' => 'true', 'No' => 'false'),
                'variableZoom' => array('Yes' => 'true', 'No' => 'false'),
                'autostart' => array('Yes' => 'true', 'No' => 'false'),
                'cssClass' => array('blurred' => '', 'dark' => 'dark-bg', 'white' => 'white-bg'),
                'smoothing' => array('Yes' => 'true', 'No' => 'false'),
            ));
            $this->loadDefaults();
        }

        /**
         * Method to get headers string
         *
         * @param string $jsPath  Path to JS file
         * @param string $cssPath Path to CSS file
         *
         * @return string
         */
        public function getHeadersTemplate($jsPath = '', $cssPath = null)
        {
            //to prevent multiple displaying of headers
            if (!defined('MAGICZOOMPLUS_MODULE_HEADERS')) {
                define('MAGICZOOMPLUS_MODULE_HEADERS', true);
            } else {
                return '';
            }
            if ($cssPath == null) {
                $cssPath = $jsPath;
            }
            $headers = array();
            $headers[] = '<!-- Magic Zoom Plus Prestashop module version v5.10.3 [v1.6.94:v5.3.7] -->';
            $headers[] = '<script type="text/javascript">window["mgctlbx$Pltm"] = "Prestashop";</script>';
            $headers[] = '<link type="text/css" href="' . $cssPath . '/magiczoomplus.css" rel="stylesheet" media="screen" />';
            $headers[] = '<link type="text/css" href="' . $cssPath . '/magiczoomplus.module.css" rel="stylesheet" media="screen" />';
            $headers[] = '<script type="text/javascript" src="' . $jsPath . '/magiczoomplus.js"></script>';
            $headers[] = '<script type="text/javascript" src="' . $jsPath . '/magictoolbox.utils.js"></script>';
            $headers[] = $this->getOptionsTemplate();
            return "\r\n" . implode("\r\n", $headers) . "\r\n";
        }

        /**
         * Method to get options string
         *
         * @return string
         */
        public function getOptionsTemplate()
        {
            $autostart = $this->params->getValue('autostart');//NOTE: true | false
            if ($autostart !== null) {
                $autostart = "\n\t\t'autostart':" . $autostart . ',';
            } else {
                $autostart = '';
            }
            //$autostart .= "\n\t\t'history':false,";
            return "<script type=\"text/javascript\">\n\tvar mzOptions = {{$autostart}\n\t\t" . $this->params->serialize(true, ",\n\t\t") . "\n\t}\n</script>\n" .
                   "<script type=\"text/javascript\">\n\tvar mzMobileOptions = {" .
                   "\n\t\t'zoomMode':'" . str_replace('\'', '\\\'', $this->params->getValue('zoomModeForMobile')) . "'," .
                   "\n\t\t'textHoverZoomHint':'" . str_replace('\'', '\\\'', $this->params->getValue('textHoverZoomHintForMobile')) . "'," .
                   "\n\t\t'textClickZoomHint':'" . str_replace('\'', '\\\'', $this->params->getValue('textClickZoomHintForMobile')) . "'," .
                   "\n\t\t'textExpandHint':'" . str_replace('\'', '\\\'', $this->params->getValue('textExpandHintForMobile')) . "'" .
                   "\n\t}\n</script>";
        }

        /**
         * Method to get main image HTML
         *
         * @param array $params Params
         *
         * @return string
         */
        public function getMainTemplate($params)
        {
            $img = '';
            $thumb = '';
            $thumb2x = '';
            $id = '';
            $alt = '';
            $title = '';
            $width = '';
            $height = '';
            $link = '';
            $group = '';//data-gallery

            extract($params);

            if (empty($img)) {
                return false;
            }
            if (empty($thumb)) {
                $thumb = $img;
            }
            if (empty($id)) {
                $id = hash('md5', $img, false);
            }

            if (!empty($title)) {
                $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
                if (empty($alt)) {
                    $alt = $title;
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
                $title = " title=\"{$title}\"";
            } else {
                $title = '';
                if (empty($alt)) {
                    $alt = '';
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
            }

            if (empty($width)) {
                $width = '';
            } else {
                $width = " width=\"{$width}\"";
            }
            if (empty($height)) {
                $height = '';
            } else {
                $height = " height=\"{$height}\"";
            }

            if ($this->params->checkValue('show-message', 'Yes')) {
                $message = '<div class="MagicToolboxMessage">' . $this->params->getValue('message') . '</div>';
            } else {
                $message = '';
            }

            if (empty($link)) {
                $link = '';
            } else {
                $link = " data-link=\"{$link}\"";
            }

            if (empty($group)) {
                $group = '';
            } else {
                $group = " data-gallery=\"{$group}\"";
            }

            $options = $this->params->serialize();

            if (!empty($options)) {
                $options = " data-options=\"{$options}\"";
            }

            $mobileOptions = array(
                'zoomModeForMobile'          => 'zoomMode',
                'textHoverZoomHintForMobile' => 'textHoverZoomHint',
                'textClickZoomHintForMobile' => 'textClickZoomHint',
                'textExpandHintForMobile'    => 'textExpandHint',
            );
            $profile = $this->params->getProfile();
            foreach ($mobileOptions as $mId => $option) {
                if (!$this->params->paramExists($mId, $profile) || $this->params->checkValue($mId, $this->params->getValue($mId, $this->params->generalProfile), $profile)) {
                    $mobileOptions[$mId] = '';
                    continue;
                }
                $mobileOptions[$mId] = "{$option}:" . str_replace('"', '&quot;', $this->params->getValue($mId, $profile)) . ';';
            }
            $mobileOptions = implode('', $mobileOptions);
            if (!empty($mobileOptions)) {
                $options .= " data-mobile-options=\"{$mobileOptions}\"";
            }

            if (!empty($thumb2x)) {
                //NOTICE: temporary disabled because of issue with zoom images (when the picture size is not big enough)
                //$dataImage2x = ' data-zoom-image-2x="' . $img . '" data-image-2x="' . $thumb2x . '" ';
                $dataImage2x = ' data-image-2x="' . $thumb2x . '" ';
                //$thumb2x = ' srcset="' . $thumb2x . ' 2x"';
                //$thumb2x = ' srcset="' . $thumb . ' 1x, ' . $thumb2x . ' 2x"';
                $thumb2x = ' srcset="' . str_replace(' ', '%20', $thumb) . ' 1x, ' . str_replace(' ', '%20', $thumb2x) . ' 2x"';
            } else {
                $dataImage2x = '';
            }

            return "<a id=\"MagicZoomPlusImage{$id}\" {$dataImage2x} class=\"MagicZoom\" href=\"{$img}\"{$group}{$link}{$title}{$options}><img class=\"no-sirv-lazy-load\" itemprop=\"image\" src=\"{$thumb}\" {$thumb2x}  alt=\"{$alt}\"{$width}{$height} /></a>{$message}";
        }

        /**
         * Method to get selectors HTML
         *
         * @param array $params Params
         *
         * @return string
         */
        public function getSelectorTemplate($params)
        {
            $img = '';
            $medium = '';
            $medium2x = '';
            $thumb = '';
            $thumb2x = '';
            $id = '';
            $alt = '';
            $title = '';
            $width = '';
            $height = '';

            extract($params);

            if (empty($img)) {
                return false;
            }
            if (empty($medium)) {
                $medium = $img;
            }
            if (empty($thumb)) {
                $thumb = $img;
            }

            if (empty($id)) {
                $id = hash('md5', $img, false);
            }

            if (!empty($title)) {
                $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
                if (empty($alt)) {
                    $alt = $title;
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
                $title = " title=\"{$title}\"";
            } else {
                $title = '';
                if (empty($alt)) {
                    $alt = '';
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
            }

            if (empty($width)) {
                $width = '';
            } else {
                $width = " width=\"{$width}\"";
            }
            if (empty($height)) {
                $height = '';
            } else {
                $height = " height=\"{$height}\"";
            }

            if (!empty($thumb2x)) {
                //$thumb2x = ' srcset="' . $thumb2x . ' 2x"';
                //$thumb2x = ' srcset="' . $thumb . ' 1x, ' . $thumb2x . ' 2x"';
                $thumb2x = ' srcset="' . str_replace(' ', '%20', $thumb) . ' 1x, ' . str_replace(' ', '%20', $thumb2x) . ' 2x"';
            }

            if (!empty($medium2x)) {
                //NOTICE: temporary disabled because of issue with zoom images (when the picture size is not big enough)
                //$medium2x = ' data-zoom-image-2x="' . $img . '" data-image-2x="' . $medium2x . '" ';
                $medium2x = ' data-image-2x="' . $medium2x . '" ';
            }

            return "<a data-zoom-id=\"MagicZoomPlusImage{$id}\" href=\"{$img}\" {$medium2x} data-image=\"{$medium}\"{$title}><img src=\"{$thumb}\" {$thumb2x} alt=\"{$alt}\"{$width}{$height} /></a>";
        }

        /**
         * Method to load defaults options
         *
         * @return void
         */
        public function loadDefaults()
        {
            $params = array(
                'template'=>array('id'=>'template','group'=>'General','order'=>'20','default'=>'bottom','label'=>'Thumbnail layout','type'=>'array','subType'=>'select','values'=>array('original','bottom','left','right','top'),'scope'=>'module'),
                'include-headers-on-all-pages'=>array('id'=>'include-headers-on-all-pages','group'=>'General','order'=>'20','default'=>'No','label'=>'Include headers on all pages','description'=>'To be able to apply an effect on any page','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'module'),
                'magicscroll'=>array('id'=>'magicscroll','group'=>'General','order'=>'22','default'=>'No','label'=>'Scroll thumbnails','description'=>"Powered by the versatile <a target='_blank' href='https://www.magictoolbox.com/magiczoomplus/magicscroll/'>Magic Scroll</a>™. Normally £29, yours is discounted to £19. <a target='_blank' href='https://www.magictoolbox.com/buy/magicscroll/?coupon=psms'>Buy a license</a> and upload magicscroll.js to your server. <a target='_blank' href='https://www.magictoolbox.com/contact/'>Contact us</a> for help.",'type'=>'array','subType'=>'select','values'=>array('Yes','No'),'scope'=>'module'),
                'thumb-image'=>array('id'=>'thumb-image','group'=>'Image type','order'=>'10','default'=>'large','label'=>'Main image','type'=>'array','subType'=>'select','values'=>array('original','large'),'scope'=>'module'),
                'selector-image'=>array('id'=>'selector-image','group'=>'Image type','order'=>'20','default'=>'small','label'=>'Thumbnail images','type'=>'array','subType'=>'select','values'=>array('original','small'),'scope'=>'module'),
                'large-image'=>array('id'=>'large-image','group'=>'Image type','order'=>'30','default'=>'thickbox','label'=>'Zoom image','type'=>'array','subType'=>'select','values'=>array('original','thickbox'),'scope'=>'module'),
                'zoomWidth'=>array('id'=>'zoomWidth','group'=>'Positioning and Geometry','order'=>'20','default'=>'auto','label'=>'Width of zoom window','description'=>'pixels or percentage, e.g. 400 or 100%.','type'=>'text','scope'=>'magiczoomplus'),
                'zoomHeight'=>array('id'=>'zoomHeight','group'=>'Positioning and Geometry','order'=>'30','default'=>'auto','label'=>'Height of zoom window','description'=>'pixels or percentage, e.g. 400 or 100%.','type'=>'text','scope'=>'magiczoomplus'),
                'zoomPosition'=>array('id'=>'zoomPosition','group'=>'Positioning and Geometry','order'=>'40','default'=>'right','label'=>'Position of zoom window','type'=>'array','subType'=>'radio','values'=>array('top','right','bottom','left','inner'),'scope'=>'magiczoomplus'),
                'zoomDistance'=>array('id'=>'zoomDistance','group'=>'Positioning and Geometry','order'=>'50','default'=>'15','label'=>'Zoom distance','description'=>'Distance between small image and zoom window (in pixels).','type'=>'num','scope'=>'magiczoomplus'),
                'selectorTrigger'=>array('id'=>'selectorTrigger','advanced'=>'1','group'=>'Multiple images','order'=>'10','default'=>'click','label'=>'Swap trigger','description'=>'Mouse event used to switch between multiple images.','type'=>'array','subType'=>'radio','values'=>array('click','hover'),'scope'=>'magiczoomplus','desktop-only'=>''),
                'transitionEffect'=>array('id'=>'transitionEffect','advanced'=>'1','group'=>'Multiple images','order'=>'20','default'=>'Yes','label'=>'Transition effect on swap','description'=>'Whether to enable dissolve effect when switching between images.','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus'),
                '360-as-primary-image'=>array('id'=>'360-as-primary-image','group'=>'Settings for using Magic Zoom Plus and Magic 360 together','order'=>'10','default'=>'Yes','label'=>'360 as primary image','description'=>'Load the 360 spin as the main image on page load.','type'=>'array','subType'=>'select','values'=>array('Yes','No'),'scope'=>'module'),
                'lazyZoom'=>array('id'=>'lazyZoom','group'=>'Miscellaneous','order'=>'10','default'=>'No','label'=>'Lazy load of zoom image','description'=>'Whether to load large image on demand (on first activation).','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus'),
                'enable-effect'=>array('id'=>'enable-effect','group'=>'Miscellaneous','order'=>'10','default'=>'Yes','label'=>'Enable effect','type'=>'array','subType'=>'select','values'=>array('Yes','No'),'scope'=>'module'),
                'rightClick'=>array('id'=>'rightClick','group'=>'Miscellaneous','order'=>'20','default'=>'No','label'=>'Right-click menu on image','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus','desktop-only'=>''),
                'link-to-product-page'=>array('id'=>'link-to-product-page','group'=>'Miscellaneous','order'=>'20','default'=>'Yes','label'=>'Link image to the product page','type'=>'array','subType'=>'select','values'=>array('Yes','No'),'scope'=>'module'),
                'cssClass'=>array('id'=>'cssClass','advanced'=>'1','group'=>'Miscellaneous','order'=>'30','default'=>'','label'=>'Extra CSS','description'=>'Extra CSS class(es) to apply to zoom instance.','type'=>'text','scope'=>'magiczoomplus'),
                'show-message'=>array('id'=>'show-message','group'=>'Miscellaneous','order'=>'370','default'=>'No','label'=>'Show message under images','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'module'),
                'message'=>array('id'=>'message','group'=>'Miscellaneous','order'=>'380','default'=>'Move your mouse over image or click to enlarge','label'=>'Enter message to appear under images','type'=>'text','scope'=>'module'),
                'zoomMode'=>array('id'=>'zoomMode','group'=>'Zoom mode','order'=>'10','default'=>'zoom','label'=>'Zoom mode','description'=>'How to zoom image. off - disable zoom.','type'=>'array','subType'=>'radio','values'=>array('zoom','magnifier','preview','off'),'scope'=>'magiczoomplus','desktop-only'=>'preview'),
                'zoomOn'=>array('id'=>'zoomOn','group'=>'Zoom mode','order'=>'20','default'=>'hover','label'=>'Zoom on','description'=>'When to activate zoom.','type'=>'array','subType'=>'radio','values'=>array('hover','click'),'scope'=>'magiczoomplus','desktop-only'=>''),
                'upscale'=>array('id'=>'upscale','advanced'=>'1','group'=>'Zoom mode','order'=>'30','default'=>'Yes','label'=>'Upscale image','description'=>'Whether to scale up the large image if its original size is not enough for a zoom effect.','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus'),
                'smoothing'=>array('id'=>'smoothing','advanced'=>'1','group'=>'Zoom mode','order'=>'35','default'=>'Yes','label'=>'Smooth zoom movement','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus'),
                'variableZoom'=>array('id'=>'variableZoom','advanced'=>'1','group'=>'Zoom mode','order'=>'40','default'=>'No','label'=>'Variable zoom','description'=>'Whether to allow changing zoom ratio with mouse wheel.','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus','desktop-only'=>''),
                'zoomCaption'=>array('id'=>'zoomCaption','group'=>'Zoom mode','order'=>'50','default'=>'off','label'=>'Caption in zoom window','description'=>'Position of caption on zoomed image. off - disable caption on zoom window.','type'=>'array','subType'=>'radio','values'=>array('top','bottom','off'),'scope'=>'magiczoomplus'),
                'expand'=>array('id'=>'expand','group'=>'Expand mode','order'=>'10','default'=>'window','label'=>'Expand mode','description'=>'How to show expanded view. off - disable expanded view.','type'=>'array','subType'=>'radio','values'=>array('window','fullscreen','off'),'scope'=>'magiczoomplus'),
                'expandZoomMode'=>array('id'=>'expandZoomMode','group'=>'Expand mode','order'=>'20','default'=>'zoom','label'=>'Expand zoom mode','description'=>'How to zoom image in expanded view. off - disable zoom in expanded view.','type'=>'array','subType'=>'radio','values'=>array('zoom','magnifier','off'),'scope'=>'magiczoomplus'),
                'expandZoomOn'=>array('id'=>'expandZoomOn','group'=>'Expand mode','order'=>'21','default'=>'click','label'=>'Expand zoom on','description'=>'When and how activate zoom in expanded view. ‘always’ - zoom automatically activates upon entering the expanded view and remains active.','type'=>'array','subType'=>'radio','values'=>array('click','always'),'scope'=>'magiczoomplus'),
                'expandCaption'=>array('id'=>'expandCaption','group'=>'Expand mode','order'=>'30','default'=>'Yes','label'=>'Show caption in expand window','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus','desktop-only'=>''),
                'closeOnClickOutside'=>array('id'=>'closeOnClickOutside','group'=>'Expand mode','order'=>'40','default'=>'Yes','label'=>'Close expanded image on click outside','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magiczoomplus'),
                'hint'=>array('id'=>'hint','group'=>'Hint','order'=>'10','default'=>'once','label'=>'Display hint to suggest image is zoomable','description'=>'How to show hint. off - disable hint.','type'=>'array','subType'=>'radio','values'=>array('once','always','off'),'scope'=>'magiczoomplus'),
                'textHoverZoomHint'=>array('id'=>'textHoverZoomHint','advanced'=>'1','group'=>'Hint','order'=>'20','default'=>'Hover to zoom','label'=>'Hint to suggest image is zoomable (on hover)','description'=>'Hint that shows when zoom mode is enabled, but inactive, and zoom activates on hover (Zoom on: hover).','type'=>'text','scope'=>'magiczoomplus'),
                'textClickZoomHint'=>array('id'=>'textClickZoomHint','advanced'=>'1','group'=>'Hint','order'=>'21','default'=>'Click to zoom','label'=>'Hint to suggest image is zoomable (on click)','description'=>'Hint that shows when zoom mode is enabled, but inactive, and zoom activates on click (Zoom on: click).','type'=>'text','scope'=>'magiczoomplus'),
                'textExpandHint'=>array('id'=>'textExpandHint','advanced'=>'1','group'=>'Hint','order'=>'30','default'=>'Click to expand','label'=>'Hint to suggest image is expandable','description'=>'Hint that shows when zoom mode activated, or in inactive state if zoom mode is disabled.','type'=>'text','scope'=>'magiczoomplus'),
                'textBtnClose'=>array('id'=>'textBtnClose','group'=>'Hint','order'=>'40','default'=>'Close','label'=>'Hint for “close” button','description'=>'Text label that appears on mouse over the “close” button in expanded view.','type'=>'text','scope'=>'magiczoomplus','desktop-only'=>''),
                'textBtnNext'=>array('id'=>'textBtnNext','group'=>'Hint','order'=>'50','default'=>'Next','label'=>'Hint for “next” button','description'=>'Text label that appears on mouse over the “next” button arrow in expanded view.','type'=>'text','scope'=>'magiczoomplus','desktop-only'=>''),
                'textBtnPrev'=>array('id'=>'textBtnPrev','group'=>'Hint','order'=>'60','default'=>'Previous','label'=>'Hint for “previous” button','description'=>'Text label that appears on mouse over the “previous” button arrow in expanded view.','type'=>'text','scope'=>'magiczoomplus','desktop-only'=>''),
                'zoomModeForMobile'=>array('id'=>'zoomModeForMobile','group'=>'Mobile','order'=>'10','default'=>'off','label'=>'Zoom mode','description'=>'How to zoom image. off - disable zoom.','type'=>'array','subType'=>'radio','values'=>array('zoom','magnifier','off'),'scope'=>'magiczoomplus-mobile'),
                'textHoverZoomHintForMobile'=>array('id'=>'textHoverZoomHintForMobile','advanced'=>'1','group'=>'Mobile','order'=>'20','default'=>'Touch to zoom','label'=>'Hint to suggest image is zoomable (on hover)','description'=>'Hint that shows when zoom mode is enabled, but inactive, and zoom activates on hover (Zoom on: hover).','type'=>'text','scope'=>'magiczoomplus-mobile'),
                'textClickZoomHintForMobile'=>array('id'=>'textClickZoomHintForMobile','advanced'=>'1','group'=>'Mobile','order'=>'21','default'=>'Double tap or pinch to zoom','label'=>'Hint to suggest image is zoomable (on click)','description'=>'Hint that shows when zoom mode is enabled, but inactive, and zoom activates on click (Zoom on: click).','type'=>'text','scope'=>'magiczoomplus-mobile'),
                'textExpandHintForMobile'=>array('id'=>'textExpandHintForMobile','advanced'=>'1','group'=>'Mobile','order'=>'30','default'=>'Tap to expand','label'=>'Hint to suggest image is expandable','description'=>'Hint that shows when zoom mode activated, or in inactive state if zoom mode is disabled.','type'=>'text','scope'=>'magiczoomplus-mobile'),
                'width'=>array('id'=>'width','group'=>'Scroll','order'=>'10','default'=>'auto','label'=>'Scroll width','description'=>'auto | pixels | percentage','type'=>'text','scope'=>'magicscroll'),
                'height'=>array('id'=>'height','group'=>'Scroll','order'=>'20','default'=>'auto','label'=>'Scroll height','description'=>'auto | pixels | percentage','type'=>'text','scope'=>'magicscroll'),
                'orientation'=>array('id'=>'orientation','group'=>'Scroll','order'=>'30','default'=>'horizontal','label'=>'Orientation of scroll','type'=>'array','subType'=>'radio','values'=>array('horizontal','vertical'),'scope'=>'magicscroll'),
                'mode'=>array('id'=>'mode','group'=>'Scroll','order'=>'40','default'=>'scroll','label'=>'Scroll mode','type'=>'array','subType'=>'radio','values'=>array('scroll','animation','carousel','cover-flow'),'scope'=>'magicscroll'),
                'items'=>array('id'=>'items','group'=>'Scroll','order'=>'50','default'=>'3','label'=>'Items to show','description'=>'auto | fit | integer | array','type'=>'text','scope'=>'magicscroll'),
                'speed'=>array('id'=>'speed','group'=>'Scroll','order'=>'60','default'=>'600','label'=>'Scroll speed (in milliseconds)','description'=>'e.g. 5000 = 5 seconds','type'=>'num','scope'=>'magicscroll'),
                'autoplay'=>array('id'=>'autoplay','group'=>'Scroll','order'=>'70','default'=>'0','label'=>'Autoplay speed (in milliseconds)','description'=>'e.g. 0 = disable autoplay; 600 = 0.6 seconds','type'=>'num','scope'=>'magicscroll'),
                'loop'=>array('id'=>'loop','group'=>'Scroll','order'=>'80','advanced'=>'1','default'=>'infinite','label'=>'Continue scroll after the last(first) image','description'=>'infinite - scroll in loop; rewind - rewind to the first image; off - stop on the last image','type'=>'array','subType'=>'radio','values'=>array('infinite','rewind','off'),'scope'=>'magicscroll'),
                'step'=>array('id'=>'step','group'=>'Scroll','order'=>'90','default'=>'auto','label'=>'Number of items to scroll','description'=>'auto | integer','type'=>'text','scope'=>'magicscroll'),
                'arrows'=>array('id'=>'arrows','group'=>'Scroll','order'=>'100','default'=>'inside','label'=>'Prev/Next arrows','type'=>'array','subType'=>'radio','values'=>array('inside','outside','off'),'scope'=>'magicscroll'),
                'pagination'=>array('id'=>'pagination','group'=>'Scroll','order'=>'110','advanced'=>'1','default'=>'No','label'=>'Show pagination (bullets)','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magicscroll'),
                'easing'=>array('id'=>'easing','group'=>'Scroll','order'=>'120','advanced'=>'1','default'=>'cubic-bezier(.8, 0, .5, 1)','label'=>'CSS3 Animation Easing','description'=>'see cubic-bezier.com','type'=>'text','scope'=>'magicscroll'),
                'scrollOnWheel'=>array('id'=>'scrollOnWheel','group'=>'Scroll','order'=>'130','advanced'=>'1','default'=>'auto','label'=>'Scroll On Wheel mode','description'=>"auto - automatically turn off scrolling on mouse wheel in the 'scroll' and 'animation' modes, and enable it in 'carousel' and 'cover-flow' modes",'type'=>'array','subType'=>'radio','values'=>array('auto','turn on','turn off'),'scope'=>'magicscroll'),
                'lazy-load'=>array('id'=>'lazy-load','group'=>'Scroll','order'=>'140','advanced'=>'1','default'=>'No','label'=>'Lazy load','description'=>'Delay image loading. Images outside of view will be loaded on demand.','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'magicscroll'),
                'scroll-extra-styles'=>array('id'=>'scroll-extra-styles','group'=>'Scroll','order'=>'150','advanced'=>'1','default'=>'','label'=>'Scroll extra styles','description'=>'mcs-rounded | mcs-shadows | bg-arrows | mcs-border','type'=>'text','scope'=>'module'),
                'show-image-title'=>array('id'=>'show-image-title','group'=>'Scroll','order'=>'160','default'=>'No','label'=>'Show image title','type'=>'array','subType'=>'radio','values'=>array('Yes','No'),'scope'=>'module')
            );
            $this->params->appendParams($params);
        }
    }

}
