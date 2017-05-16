<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @category   Belvg
 * @package    Belvg_CustomerAttributes
 * @author    Dzianis Yurevich (dzianis.yurevich@gmail.com)
 * @copyright Copyright (c) 2010 - 2016 BelVG LLC. (http://www.belvg.com)
 * @license   http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class belvg_customerattributesmyaccountModuleFrontController extends ModuleFrontController 
{
    public $auth = true;

    protected $_module = null;

    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();
    }

    public function getModule()
    {
        if (is_NULL($this->_module)) {
            $this->_module = new belvg_customerattributes;
        }

        return $this->_module;
    }

    protected function l($string, $class = 'belvg_customerattributesmyaccountModuleFrontController', $addslashes = false, $htmlentities = true)
    {
        return $this->getModule()->l($string, $class, $addslashes, $htmlentities);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_ . 'authentication.css');
    }

    public function init()
    {
        parent::init();
        require_once ($this->module->getLocalPath() . 'includer.php');
        $this->display_column_left = false;
    }

    public function initContent()
    {
        parent::initContent();
        $_customer = $this->context->customer;
        $attr_sections = CustomerAttributesSection::getAllSections($_customer->getGroups(), 'myaccount', $this->context->language->id);
        $this->context->smarty->assign(array(
            'belvg_customer_id' => (int)$_customer->id,
            'belvg_attr_sections' => $attr_sections,
        ));
        $this->setTemplate('myaccount.tpl');
    }

    public function postProcess()
    {
        if (Tools::getIsset($this->getModule()->name)) {
            $postState = 'success';
            $errors = $this->getModule()->saveAttributes('myaccount', $this->context->customer->id);
            $this->context->cookie->belvg_errors = false;
            if (is_array($errors)) {
                $this->context->cookie->belvg_errors = implode('<br/>', $errors);
                    parent::initContent();
                        $_customer = $this->context->customer;
                        $sections = CustomerAttributesSection::getAllSections(Customer::getGroupsStatic($_customer->id), 'myaccount', $this->context->language->id);
                        $this->context->smarty->assign(array(
                                'belvg_errors' => $errors,
                                'belvg_customer_id' => (int)$_customer->id,
                                'belvg_attr_sections' => $sections
                        ));
                    $this->setTemplate('myaccount.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('belvg_customerattributes', 'myaccount'));
            }
        }
    }

}

