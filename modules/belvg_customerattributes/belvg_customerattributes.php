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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'belvg_customerattributes/includer.php';

class belvg_customerattributes extends Module
{
    const PREFIX = 'belvg_';

    protected $_hooks = array(
        'displayHeader',
        'displayBackOfficeHeader',
        'displayCustomerAccountFormTop',
        'displayCustomerAccountForm',
        'displayAdminOrder',
        'displayPDFInvoice',
        'actionCustomerAccountAdd',
        'displayAdminCustomers',
        'displayCustomerAccount',
        'actionObjectCustomerDeleteAfter'
    );

    protected $_tabs = array(array(
        'class_name' => 'AdminBelvgCustomerAttributes',
        'parent' => 'AdminParentCustomer',
        'name' => 'Belvg Customer Attributes'));

    public function __construct()
    {
        $this->name = 'belvg_customerattributes';
        $this->tab = 'front_office_features';
        $this->version = '1.6.5';
        $this->author = 'BelVG';
        $this->need_instance = 0;
        $this->module_key = '0d6c0409d470533407430fccda68d3bf';

        parent::__construct();

        $this->displayName = $this->l('Belvg Customer Attributes');
        $this->description = $this->l('Belvg Customer Attributes');
    }

    public static function getDbPrefix()
    {
        return _DB_PREFIX_ . self::PREFIX;
    }

    public static function getAllValues()
    {
        return $_POST + $_GET;
    }

    public function getDir($file = '')
    {
        return _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . $file;
    }

    public function install()
    {
        if (!$this->isWriteable()) {
            $this->_errors[] = $this->l('Please set 777 rights for "') . $this->getLocalPath() . $this->l('" folder!');
            return false;
        }

        $sql = include($this->getDir('sql/install.php'));
        foreach ($sql as $_sql) {
            Db::getInstance()->Execute($_sql);
        }

        $languages = Language::getLanguages();
        foreach ($this->_tabs as $tab) {
            $_tab = new Tab();
            $_tab->class_name = $tab['class_name'];
            $_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
            $_tab->module = $this->name;
            foreach ($languages as $language) {
                $_tab->name[$language['id_lang']] = $this->l($tab['name']);
            }

            $_tab->add();
        }

        $install = parent::install();
        foreach ($this->_hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return $install;
    }

    public function uninstall()
    {
        $sql = include($this->getDir('sql/uninstall.php'));
        foreach ($sql as $_sql) {
            Db::getInstance()->Execute($_sql);
        }

        foreach ($this->_tabs as $tab) {
            $idTab = Tab::getIdFromClassName($tab['class_name']);
            if ($idTab) {
                $_tab = new Tab($idTab);
                $_tab->delete();
            }
        }

        $uninstall = parent::uninstall();
        foreach ($this->_hooks as $hook) {
            if (!$this->unregisterHook($hook)) {
                return false;
            }
        }

        @self::deleteUserFiles($this->getLocalPath() . CustomerAttributes::FILES_DIR);

        return $uninstall;
    }

    public function deleteUserFiles($folder)
    {
        if (is_dir($folder)) {
            $handle = opendir($folder);
            while ($subfile = readdir($handle)) {
                if ($subfile == '.' or $subfile == '..') {
                    continue;
                }

                if (is_file($subfile)) {
                    unlink("{$folder}/{$subfile}");
                } else {
                    self::deleteUserFiles("{$folder}/{$subfile}");
                }
            }

            closedir($handle);
            rmdir($folder);
        } else {
            unlink($folder);
        }
    }

    public function isWriteable()
    {
        return is_writable($this->getLocalPath());
    }

    public function saveAttributes($page, $id_customer = null)
    {
        if (Tools::getIsset($this->name)) {
            $errors = array();
            if (is_null($id_customer)) {
                $id_customer = (int)Tools::getValue('belvg_customer_id');
            }

            $sections = CustomerAttributesSection::getAllSections(Customer::getGroupsStatic($id_customer), $page, $this->context->language->id);
            foreach ($sections as $section) {
                foreach ($section->attributes as $attribute) {
                    $attr_key = 'belvg_customerattributes_' . $attribute->code;
                    if (in_array($attribute->type, array('image', 'attachment'))) {
                        $file = $_FILES[$attr_key];
                        $old_value = $attribute->getCustomerValue($id_customer);

                        if ($file['error'] == 4 && $attribute->required && !$old_value) {
                            $errors[] = $this->l('File ') . $attribute->name . ' ' . $this->l('is required!');
                            continue;
                        }

                        if ($file['error'] == 0) {
                            $name = explode('.', $file['name']);
                            $ext = $name[count($name) - 1];

                            $allowedExt = CustomerAttributes::getAllowedImagesExt();
                            if ($attribute->type == 'attachment') {
                                $allowedExt = explode(';', $attribute->file_extensions);
                            }

                            if (!in_array($ext, $allowedExt)) {
                                $errors[] = $this->l('Incorrect file format for ') . $attribute->name;
                            }
                        }
                    } else {
                        $value = Tools::getValue($attr_key);
                        if ($attribute->required && !$value) {
                            $errors[] = $attribute->name . ' ' . $this->l('is required!');
                            continue;
                        }

                        if (in_array($attribute->type, array('text', 'textarea'))) {
                            if ($attribute->validation && !call_user_func(array('Validate', $attribute->validation), $value)) {
                                $errors[] = $attribute->name . ' ' . $this->l('is invalid!');
                            } else if ($attribute->max_text_length && ($attribute->max_text_length < strlen($value))) {
                                $errors[] = $attribute->name . ' ' . $this->l('exceeds max length');
                            }
                        }
                    }
                }

            }

            if (count($errors)) {
                return $errors;
            } else {
                $this->save($sections, $id_customer);
            }

            return true;
        }
    }

    public function save($sections, $id_customer)
    {
        foreach ($sections as $section) {
            foreach ($section->attributes as $attribute) {
                $attr_key = 'belvg_customerattributes_' . $attribute->code;
                if (in_array($attribute->type, array('image', 'attachment'))) {
                    $file = $_FILES[$attr_key];
                    $name = explode('.', $file['name']);
                    $ext = $name[count($name) - 1];

                    $value = $attribute->code . '.' . $ext;
                    $dest = CustomerAttributes::getFilePath($id_customer, $value, $attribute->type);
                    $dir = dirname($dest);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777, true);
                    }

                    if (!@move_uploaded_file($file['tmp_name'], $dest)) {
                        $value = false;
                    }
                } else {
                    $value = Tools::getValue($attr_key);
                }

                if ($value !== false) {
                    $attribute->setCustomerValue($id_customer, $value);
                }
            }
        }

    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCss($this->_path . 'views/css/front.css', 'all');
        $this->context->controller->addJqueryUI('ui.datepicker');
    }

    public function hookActionObjectCustomerDeleteAfter($params)
    {
        $id_customer = (int)$params['object']->id;
        if ($id_customer) {
            @self::deleteUserFiles($this->getLocalPath() . CustomerAttributes::FILES_DIR . '/' . $id_customer);
            Db::getInstance()->Execute('
                DELETE FROM `' . self::getDbPrefix() . 'customerattributes_customer`
                WHERE `id_customer` = ' . $id_customer . ';
            ');
        }
    }

    public function hookActionCustomerAccountAdd($params)
    {
        $result = $this->saveAttributes('create', $params['newCustomer']->id);
        $this->context->cookie->belvg_errors = false;
        if (is_array($result)) {
            $this->context->cookie->belvg_errors = implode('<br/>', $result);
            Tools::redirect($this->context->link->getModuleLink('belvg_customerattributes', 'myaccount') . '?belvg_customerattributes=1');
        }
    }

    public function hookDisplayCustomerAccount($params)
    {
        $groups = Customer::getGroupsStatic($params['cookie']->id_customer);
        $secctions = CustomerAttributesSection::getAllSections($groups, 'myaccount');
        if (count($secctions)) {
            $this->context->smarty->assign('belvg_customerattributes_path', $this->_path);
            return $this->display(__FILE__, 'my-account.tpl');
        }

        return null;
    }

    public function hookDisplayCustomerAccountFormTop($params)
    {
        $page = 'create';
        $placement = 'top';
        if ($this->context->controller->php_self == 'my-account') {
            $page = 'myaccount';
        }

        $_customer = $this->context->customer;
        $sections = CustomerAttributesSection::getAllSections($_customer->getGroups(), $page, $this->context->language->id, $placement);

        if (count($sections)) {
            $this->context->smarty->assign(array(
                'belvg_customer_id' => (int)$_customer->id,
                'belvg_attr_sections' => $sections,
                'set_enctype' => true,
            ));

            return $this->display(__FILE__, 'createaccount.tpl');
        }
        return null;
    }

    public function hookDisplayCustomerAccountForm($params)
    {
        $page = 'create';
        $placement = 'bottom';
        if ($this->context->controller->php_self == 'my-account') {
            $page = 'myaccount';
        }
        $_customer = $this->context->customer;
        $sections = CustomerAttributesSection::getAllSections($_customer->getGroups(), $page, $this->context->language->id, $placement);

        if (count($sections)) {
            $this->context->smarty->assign(array(
                'belvg_customer_id' => (int)$_customer->id,
                'belvg_attr_sections' => $sections,
                'set_enctype' => true,
            ));

            return $this->display(__FILE__, 'createaccount.tpl');
        }

        return null;
    }

    public function hookDisplayAdminCustomers($params)
    {
        $id_customer = (int)$params['id_customer'];
        $page = 'both';
        $sections = CustomerAttributesSection::getAllSections(Customer::getGroupsStatic($id_customer), $page, $this->context->language->id);
        if (count($sections)) {
            if (Tools::getIsset($this->name)) {
                $errors = $this->saveAttributes($page);
                $postState = 'alert-success';
                if (is_array($errors)) {
                    $postState = 'alert-danger';
                    $this->context->smarty->assign('errors', implode('<br/>', $errors));
                }

                $this->context->smarty->assign('postState', $postState);
            }

            $this->context->smarty->assign(array(
                'belvg_customer_id' => $id_customer,
                'belvg_attr_sections' => $sections,
            ));

            return $this->display(__FILE__, 'admincustomer.tpl');
        }

        return null;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path . 'views/css/admin.css');
    }

    public function hookDisplayAdminOrder($params)
    {
        $id_customer = (int)$params['cart']->id_customer;
        $groups = Customer::getGroupsStatic($id_customer);
        $lang = $this->context->language->id;
        $page = 'both';
        $sections = CustomerAttributesSection::getAllSections($groups, $page, $lang, $placement = null, $adminDisplay = 'order');
        $this->context->smarty->assign(array(
            'belvg_customer_id' => $id_customer,
            'belvg_attr_sections' => $sections
        ));

        return $this->display(__FILE__, 'views/templates/hook/display-order.tpl');
    }

    public function hookDisplayPDFInvoice($params)
    {
        $id_customer = (int)$params['object']->id;
        $groups = Customer::getGroupsStatic($id_customer);
        $lang = $this->context->language->id;
        $page = 'both';
        $sections = CustomerAttributesSection::getAllSections($groups, $page, $lang, $placement = null, $adminDisplay = 'invoice');
        $this->context->smarty->assign(array(
            'belvg_customer_id' => $id_customer,
            'belvg_attr_sections' => $sections
        ));

        return $this->display(__FILE__, 'views/templates/hook/display-invoice.tpl');
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getLocalPath()
    {
        return $this->local_path;
    }

    public function checkAttributeBeforeCreate($page = 'create', $id_customer = null)
    {
        $errors = array();
        if (Tools::getIsset($this->name)) {
            $sections = CustomerAttributesSection::getAllSections(Customer::getGroupsStatic($id_customer), $page, $this->context->language->id);
            foreach ($sections as $section) {
                foreach ($section->attributes as $attribute) {
                    $attr_key = 'belvg_customerattributes_' . $attribute->code;
                    if (in_array($attribute->type, array('image', 'attachment'))) {
                        $file = $_FILES[$attr_key];
                        $old_value = $attribute->getCustomerValue($id_customer);

                        if ($file['error'] == 4 && $attribute->required && !$old_value) {
                            $errors[] = $this->l('File ') . $attribute->name . ' ' . $this->l('is required!');
                            continue;
                        }

                        if ($file['error'] == 0) {
                            $name = explode('.', $file['name']);
                            $ext = $name[count($name) - 1];

                            $allowedExt = CustomerAttributes::getAllowedImagesExt();
                            if ($attribute->type == 'attachment') {
                                $allowedExt = explode(';', $attribute->file_extensions);
                            }

                            if (!in_array($ext, $allowedExt)) {
                                $errors[] = $this->l('Incorrect file format for ') . $attribute->name;
                            }
                        }
                    } else {
                        $value = Tools::getValue($attr_key);
                        if ($attribute->required && !$value) {
                            $errors[] = $attribute->name . ' ' . $this->l('is required!');
                            continue;
                        }

                        if (in_array($attribute->type, array('text', 'textarea'))) {
                            if ($attribute->validation && !call_user_func(array('Validate', $attribute->validation), $value)) {
                                $errors[] = $attribute->name . ' ' . $this->l('is invalid!');
                            } else if ($attribute->max_text_length && ($attribute->max_text_length < strlen($value))) {
                                $errors[] = $attribute->name . ' ' . $this->l('exceeds max length');
                            }
                        }
                    }
                }

            }
        }
        return $errors;
    }
}
