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

require_once _PS_MODULE_DIR_ . 'belvg_customerattributes/includer.php';

class AdminBelvgCustomerAttributesController extends AdminController
{
    public $isSection = false;
    protected $_module = null;
    protected $_js = '<script type="text/javascript" src="../modules/belvg_customerattributes/views/js/admin.js"></script>';

    public function getModule()
    {
        if (is_NULL($this->_module)) {
            $this->_module = new belvg_customerattributes;
        }

        return $this->_module;
    }

    protected function l($string, $class = 'AdminBelvgCustomerAttributes', $addslashes = false, $htmlentities = true)
    {
        return $this->getModule()->l($string, $class, $addslashes, $htmlentities);
    }


    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = true;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function initProcess()
    {
        $allValues = (method_exists("Tools", "getAllValues")) ? Tools::getAllValues() : belvg_customerattributes::getAllValues();
        foreach ($allValues as $param => $value) {
            if (preg_match('/belvg_customerattributes_section/', $param)) {
                $this->isSection = true;
                break;
            }
        }
        if ($this->isSection) {
            $this->setEntity('section');
        } else {
            $this->setEntity('attribute');
        }
        $this->setAction();

        return parent::initProcess();
    }

    /**
     * set data entity ,table,classname, identifier
     * used in controller initialization
     * @param $type - entity type
     */
    public function setEntity($type)
    {
        switch ($type) {
            case 'section':
                list($this->table, $this->className, $this->identifier) =
                    array('belvg_customerattributes_section', 'CustomerAttributesSection', 'id_section');
                break;
            case 'attribute':
                list($this->table, $this->className, $this->identifier) =
                    array('belvg_customerattributes', 'CustomerAttributes', 'id_attribute');
                break;
        }
    }

    /**
     * set $this->action property
     * used in toggle status
     *
     */
    public function setAction()
    {
        if (Tools::getIsset('show_on_order_status'.$this->table)) {
            $this->action = 'show_on_order_status';
        } elseif (Tools::getIsset('show_on_invoice_status'.$this->table)) {
            $this->action = 'show_on_invoice_status';
        }
    }

    public function renderList()
    {
        if (!$this->getModule()->isWriteable()) {
            return $this->getModule()->display($this->getModule()->name, 'admingrid.tpl');
        }

        if (Tools::getValue('id_section')) {
            return $this->renderAttributeList();
        } else {
            return $this->renderSectionList();
        }

    }

    public function renderSectionList()
    {
        $content = Db::getInstance()->executeS('
            SELECT a.*, l.name FROM `' .$this->getModule()->getDbPrefix() . 'customerattributes_section` a
            JOIN `' .$this->getModule()->getDbPrefix() . 'customerattributes_section_lang` l
            ON a.id_section = l.id_section where l.id_lang ='. $this->context->language->id);

        $fields_list = array(
            'id_section' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 30),
            'name' => array('title' => $this->l('Name'), 'align' => 'center', 'width' => 70),
            'display_order' => array('title' => $this->l('Display order'), 'width' => 70),
            'placement' => array('title' => $this->l('Placement'), 'width' => 70),
            'show_on_invoice' => array(
                'title' => $this->l('Show on invoice'),
                'width' => 25,
                'align' => 'center',
                'active' => 'show_on_invoice_status',
                'type' => 'bool',
                'orderby' => false,
            ),
            'show_on_order' => array(
                'title' => $this->l('Show on order'),
                'width' => 25,
                'align' => 'center',
                'active' => 'show_on_order_status',
                'type' => 'bool',
                'orderby' => false,
            ),
            'enabled' => array(
                'title' => $this->l('Enabled'),
                'width' => 25,
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ));

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->actions = array("view", "edit", "delete");
        $helper->show_toolbar = true;
        $helper->module = $this->getModule();
        $helper->listTotal = count($content);
        $helper->identifier = 'id_section';
        $helper->title = $this->l('CUSTOMER ATTRIBUTES SECTIONS');
        $helper->table = 'belvg_customerattributes_section';
        $helper->token = $this->token;
        $helper->currentIndex = self::$currentIndex.'&configure='.$this->getModule()->name;
        $helper->toolbar_btn = array(
            'new' =>
                array(
                    'desc' => $this->l('New'),
                    'href' => self::$currentIndex . '&configure=' . $this->getModule()->name . '&add' . CustomerAttributesSection::$definition['table'] .
                        '&token=' . $this->token,
                ),

        );
        $helper->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        return $helper->generateList($content, $fields_list);
    }

    public function renderAttributeList()
    {
        $content = Db::getInstance()->executeS('SELECT a.*,
            (SELECT name from `'.$this->getModule()->getDbPrefix().'customerattributes_lang` l
             WHERE a.id_attribute = l.id_attribute AND l.id_lang ='. $this->context->language->id.'
             ) as name FROM `'.$this->getModule()->getDbPrefix().'customerattributes` a
             WHERE a.id_section ='. Tools::getValue('id_section'));

        $fields_list = array(
            'id_attribute' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 30),
            'name' => array('title' => $this->l('Name'), 'width' => 70),
            'code' => array('title' => $this->l('Code'), 'width' => 70),
            'type' => array('title' => $this->l('Type'), 'width' => 70),
            'show_on_order' => array(
                'title' => $this->l('Show on order'),
                'width' => 25,
                'align' => 'center',
                'active' => 'show_on_order_status',
                'type' => 'bool',
                'orderby' => false,
            ),
            'show_on_invoice' => array(
                'title' => $this->l('Show on invoice'),
                'width' => 25,
                'align' => 'center',
                'active' => 'show_on_invoice_status',
                'type' => 'bool',
                'orderby' => false,
            ),
            'enabled' => array(
                'title' => $this->l('Enabled'),
                'width' => 25,
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ),
            'date_add' => array('title' => $this->l('Date Add'), 'width' => 30),
            'date_upd' => array('title' => $this->l('Date Last Update'), 'width' => 30));

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->actions = array("edit", "delete");
        $helper->show_toolbar = true;
        $helper->module = $this->getModule();
        $helper->listTotal = count($content);
        $helper->identifier = 'id_attribute';
        $helper->title = $this->l('CUSTOMER ATTRIBUTES ATTRIBUTE');
        $helper->table = 'belvg_customerattributes';
        $helper->token = $this->token;
        $helper->currentIndex = self::$currentIndex.'&configure='. $this->getModule()->name .'&id_section='. Tools::getValue('id_section');
        $helper->toolbar_btn = array(
            'new' =>
                array(
                    'desc' => $this->l('New'),
                    'href' => self::$currentIndex . '&add' . CustomerAttributes::$definition['table'] . '&id_section='. Tools::getValue('id_section') .
                        '&token=' . $this->token,
                ),

        );
        $helper->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        return $helper->generateList($content, $fields_list);
    }


    public function renderView()
    {
        return $this->renderAttributeList();
    }

    public function renderForm()
    {
        if ($this->table == 'belvg_customerattributes_section') {
           return $this->renderSectionForm();
        } else {
            return $this->renderAttributeForm();
        }
    }

    public function renderSectionForm()
    {
        $this->initToolbar();
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array('title' => $this->l('Customer Attribute Section'), 'image' =>
                '../img/admin/tab-categories.gif'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name:'),
                    'name' => 'name',
                    'id' => 'name',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    'lang' => true
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Placement:'),
                    'name' => 'placement',
                    'required' => false,
                    'class' => 't',
                    'values' => array(array(
                        'id' => 'top',
                        'value' => 'top',
                        'label' => $this->l('Top')), array(
                        'id' => 'bottom',
                        'value' => 'bottom',
                        'label' => $this->l('Bottom'))),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Display Order:'),
                    'name' => 'display_order',
                    'required' => false,
                    'size' => 50,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show on invoice:'),
                    'name' => 'show_on_invoice',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(array(
                        'id' => 'show_on_invoice_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')), array(
                        'id' => 'show_on_invoice_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'))),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show on order:'),
                    'name' => 'show_on_order',
                    'required' => false,
                    'class' => 't',
                    'values' => array(array(
                        'id' => 'show_on_order_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')), array(
                        'id' => 'show_on_order_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'))),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled:'),
                    'name' => 'enabled',
                    'required' => false,
                    'class' => 't',
                    'values' => array(array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')), array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'))),
                )
            ),
            'submit' => array('title' => $this->l('   Save   '), 'class' => 'btn btn-default pull-right')
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }
        return parent::renderForm();

    }
    public function renderAttributeForm()
    {
        //$this->display = 'edit';
        $this->initToolbar();
        if (!$obj = $this->loadObject(true)) {
            return;
        }
        
        $types = array();
        foreach ($obj->getTypes() as $type) {
            $types[] = array(
                'id' => 'active_' . $type,
                'value' => $type,
                'class' => 'attribute_type',
                'label' => Tools::ucfirst($type)
           );
        }
        
        $display_on = array();
        foreach ($obj->getDisplayOns() as $type) {
            $display_on[] = array(
                'id' => 'active_' . $type,
                'value' => $type,
                'label' => Tools::ucfirst($type)
           );
        }
        
        $validation = array(array(
                'id' => 'active_none',
                'value' => '',
                'label' => 'None'
           ));

        foreach ($obj->getValidations() as $method => $type) {
            $validation[] = array(
                'id' => 'active_' . $method,
                'value' => $method,
                'label' => Tools::ucfirst($type)
           );
        }
        
        $groups = Group::getGroups(Context::getContext()->language->id);
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array('title' => $this->l('Customer Attribute'), 'image' =>
                    '../img/admin/tab-categories.gif'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name:'),
                    'name' => 'name',
                    'id' => 'name',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    'lang' => true
                    ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Code:'),
                    'name' => 'code',
                    'id' => 'code',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Sort Order:'),
                    'name' => 'sort_order',
                    'id' => 'sort_order',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Display On Pages:'),
                    'name' => 'display_on',
                    'required' => false,
                    'class' => 't',
                    'values' => $display_on,
                    ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Required:'),
                    'name' => 'required',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(array(
                            'id' => 'required_on',
                            'value' => 1,
                            'label' => $this->l('Yes')), array(
                            'id' => 'required_off',
                            'value' => 0,
                            'label' => $this->l('No'))),
                    ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Type:'),
                    'name' => 'type',
                    'required' => false,
                    'class' => 't',
                    'values' => $types,
                    ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Validation:'),
                    'name' => 'validation',
                    'required' => false,
                    'class' => 't',
                    'values' => $validation,
                    ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Values:'),
                    'name' => 'values',
                    'id' => 'values',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    'lang' => true,
                    'desc' => $this->l('Semicolon as delimiter. For example: value1;value2;value3')
                    ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Max Text Length:'),
                    'name' => 'max_text_length',
                    'id' => 'max_text_length',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Max File Size (in KB):'),
                    'name' => 'file_size',
                    'id' => 'file_size',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    ),
                array(
                    'type' => 'text',
                    'label' => $this->l('File Extensions:'),
                    'name' => 'file_extensions',
                    'id' => 'file_extensions',
                    'required' => true,
                    'size' => 50,
                    'maxlength' => 50,
                    'desc' => $this->l('Semicolon as delimiter. For example: zip;doc;xls')
                    ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Customer Groups:'),
                    'name' => 'type',
                    'required' => false,
                    'class' => 't',
                    'values' => $groups,
                    ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Status:'),
                    'name' => 'id_section',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show on invoice:'),
                    'name' => 'show_on_invoice',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(array(
                        'id' => 'show_on_invoice_on',
                        'value' => 1,
                        'label' => $this->l('YES')), array(
                        'id' => 'show_on_invoice_off',
                        'value' => 0,
                        'label' => $this->l('NO'))),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show on order:'),
                    'name' => 'show_on_order',
                    'required' => false,
                    'class' => 't',
                    'values' => array(array(
                        'id' => 'show_on_order_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')), array(
                        'id' => 'show_on_order_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'))),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled:'),
                    'name' => 'enabled',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')), array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'))),
                )
            ),
            'submit' => array('title' => $this->l('   Save   '), 'class' => 'btn btn-default pull-right'));

        /*if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
                );
        }*/
        
        foreach ($groups as $group) {
            $this->fields_value['groupBox_' . $group['id_group']] = in_array($group['id_group'], $obj->getGroups());
        }

        $additional_js = '<script>
            var hidedFields = ' . Tools::jsonEncode($obj->getFieldsRelations()) . ';
            var usedLangs = ' . Tools::jsonEncode(Language::getLanguages(false)) . ';
            var shower = ' . Tools::jsonEncode($obj->getTypeFieldsHider()) . ';
        </script>';

        return parent::renderForm() . $additional_js . $this->_js;
    }

    public function displayPreviewLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_preview.tpl');
        if (!array_key_exists('Bad SQL query', self::$cache_lang)) {
            self::$cache_lang['Preview'] = $this->l('Preview', 'Helper');
        }

        $tpl->assign(array(
            'href' => $this->getPreviewUrl(new Product((int)$id)),
            'action' => self::$cache_lang['Preview'],
        ));

        return $tpl->fetch();
    }
    
    public function processSave()
    {
        if (Tools::getIsset('groupBox')) {
            if (!$obj = $this->loadObject(true)) {
                return;
            }

            $multilang_values_field = $this->_convertLangValues();
            $_POST['groups'] = implode(';', Tools::getValue('groupBox'));
            $_POST['values'] = $multilang_values_field;
        }
        $result = parent::processSave();
            if (Tools::isSubmit('submitAdd'.$this->table) && $result) {
                $sectionBack = (!preg_match('/section/', $this->table)) ? '&id_section='.Tools::getValue('id_section') : '';
                $this->redirect_after = self::$currentIndex . $sectionBack .'&token='.$this->token;
            }

        return $result;
    }

    public function processDelete()
    {
        $result = parent::processDelete();
        if (Tools::isSubmit('delete'.$this->table) && $result) {
                $sectionBack = (!preg_match('/section/', $this->table)) ? '&id_section='.Tools::getValue('id_section') : '';
                $this->redirect_after = self::$currentIndex . $sectionBack .'&token='.$this->token;
        }
        return $result;
    }

    public function processBulkDelete()
    {
        if ($this->isSection) {
            $IdList = implode(",", Tools::getValue("belvg_customerattributes_sectionBox"));
        } else {
            $IdList = implode(",", Tools::getValue("belvg_customerattributesBox"));
        }
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.$this->table.'`
            WHERE '.$this->identifier.' IN('.$IdList.')');
    }

    public function processStatus()
    {
        $result = parent::processStatus();
        if (Tools::isSubmit('status'.$this->table) && $result) {
            $sectionBack = (!preg_match('/section/', $this->table)) ? '&id_section='.Tools::getValue('id_section').'&viewbelvg_customerattributes_section' : '';
            $this->redirect_after = self::$currentIndex . $sectionBack .'&token='.$this->token;
        }
        return $result;
    }

    public function processShowOnOrderStatus()
    {
        $result = parent::processStatus();
        if (Tools::isSubmit('show_on_order_status'.$this->table) && $result) {
            $sectionBack = (!preg_match('/section/', $this->table)) ? '&configure='.$this->table.'&id_section='.Tools::getValue('id_section').'&viewbelvg_customerattributes_section' : '';
            $this->redirect_after = self::$currentIndex . $sectionBack .'&token='.$this->token;
        }
        return $result;
    }

    public function processShowOnInvoiceStatus()
    {
        $result = parent::processStatus();
        if (Tools::isSubmit('show_on_invoice_status'.$this->table) && $result) {
            $sectionBack = (!preg_match('/section/', $this->table)) ? '&id_section='.Tools::getValue('id_section').'&viewbelvg_customerattributes_section' : '';
            $this->redirect_after = self::$currentIndex . $sectionBack .'&token='.$this->token;
        }
        return $result;
    }

    /** used for dropdowm/multiselect options values
     *  converts array to json before database save
     * @return string
     */
    protected function _convertLangValues()
    {
        $converted = array();
        foreach(Language::getLanguages(false) as $lang) {
            $converted[$lang['id_lang']] = Tools::getValue('values_' . $lang['id_lang']);
            unset($_POST['values_' . $lang['id_lang']]);
        }

        return Tools::jsonEncode($converted);
    }

    public function validateRules($class_name = false)
    {
        if ( Tools::getIsset($_POST)) {
            foreach ($_POST as &$post) {
                if (!is_array($post)) {
                    $post = trim($post);
                }
            }
        }

        return parent::validateRules($class_name);
    }

    protected function updateAssoShop($id_object)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }
        $assos_data = $this->getSelectedAssoShop($this->table, $id_object);
        $exclude_ids = $assos_data;
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ .'shop') as $row) {
            if (!$this->context->employee->hasAuthOnShop($row['id_shop'])) {
                $exclude_ids[] = $row['id_shop'];
            }
        }
        Db::getInstance()->delete($this->table . '_shop', '`' . $this->identifier .
            '` = ' . (int)$id_object . ($exclude_ids ? ' AND id_shop NOT IN (' . implode(', ',$exclude_ids) . ')' : ''));

        $insert = array();
        foreach ($assos_data as $id_shop) {
            $insert[] = array(
                $this->identifier => $id_object,
                'id_shop' => (int)$id_shop,
                );
        }

        return Db::getInstance()->insert($this->table . '_shop', $insert, false, true, Db::INSERT_IGNORE);
    }

    protected function getSelectedAssoShop($table)
    {
        if (!Shop::isFeatureActive()) {
            return array();
        }

        $shops = Shop::getShops(true, null, true);
        if (count($shops) == 1 && isset($shops[0])) {
            return array($shops[0], 'shop');
        }

        $assos = array();
        if (Tools::isSubmit('checkBoxShopAsso_' . $table)) {
            foreach (Tools::getValue('checkBoxShopAsso_' . $table) as $id_shop => $value) {
                $assos[] = (int)$id_shop;
            }
        } else {
            if (Shop::getTotalShops(false) == 1) {
                // if we do not have the checkBox multishop, we can have an admin with only one shop and being in multishop
                $assos[] = (int)Shop::getContextShopID();
            }
        }

        return $assos;
    }
}
