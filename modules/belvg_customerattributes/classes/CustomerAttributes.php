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

class CustomerAttributes extends ObjectModel
{
    const FILES_DIR = 'files';

    public $id_attribute;
    public $id_section;
    public $name;
    public $code;
    public $type = 'text';
    public $enabled = 1;
    public $sort_order = 0;
    public $display_on = 'both';
    public $required = 0;
    public $validation;
    public $values;
    public $show_on_invoice;
    public $show_on_order;
    public $max_text_length;
    public $file_size;
    public $file_extensions;
    public $date_add;
    public $date_upd;
    public $groups;

    protected static $_types = array(
        'text',
        'textarea',
        'date',
        'radio',
        'multiselect',
        'dropdown',
        'image',
        'attachment'
    );
    
    protected static $_fields_relations = array(
        'values',
        'file_size',
        'file_extensions',
        'validation',
        'max_text_length'
    );
    
    protected static $_type_fields_hider = array(
        'text' => array('validation', 'max_text_length'),
        'textarea' => array('validation', 'max_text_length'),
        'radio' => array('values'),
        'multiselect' => array('values'),
        'dropdown' => array('values'),
        'image' => array('file_size'),
        'attachment' => array('file_size', 'file_extensions'),
    );
    
    protected static $_display_ons = array(
        'create',
        'myaccount',
        'both'
    );
    
    protected static $_validations = array(
        'isPostCode' => 'Alphanumeric',
        'isFloat' => 'Numeric',
        'isCountryName' => 'Alpha',
        'isUrl' => 'Url',
        'isEmail' => 'Email',
    );

    protected static $_allowedImagesExt = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );

    public static $definition = array(
        'table' => 'belvg_customerattributes',
        'primary' => 'id_attribute',
        'multilang' => true,
        'fields' => array(
            'code' => array('type' => self::TYPE_STRING, 'validate' => 'isTableOrIdentifier', 'required' => true),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isTableOrIdentifier', 'required' => true),
            'enabled' => array('type' => self::TYPE_INT),
            'id_section' => array('type' => self::TYPE_INT, self::HAS_ONE),
            'sort_order' => array('type' => self::TYPE_INT),
            'display_on' => array('type' => self::TYPE_STRING, 'required' => true),
            'required' => array('type' => self::TYPE_INT),
            'validation' => array('type' => self::TYPE_STRING),
            'values' => array('type' => self::TYPE_HTML),
            'groups' => array('type' => self::TYPE_HTML),
            'max_text_length' => array('type' => self::TYPE_INT),
            'file_size' => array('type' => self::TYPE_INT),
            'show_on_invoice' => array('type' => self::TYPE_INT),
            'show_on_order' => array('type' => self::TYPE_INT),
            'file_extensions' => array('type' => self::TYPE_HTML),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => true)
            ),
        'associations' => array(
            'attributes_section' =>  array('type' => self::HAS_ONE)
            )
        );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang, null);
        $this->values = $this->setMultilangOptionValues();
    }

    /**
     *  converts dropdown/miltiselect values
     *  stored in database
     * to show in admin- example 111;222;333
     */
    public function setMultilangOptionValues()
    {
        return (array)Tools::jsonDecode($this->values, true);
    }

    public static function getModule()
    {
        return Module::getInstanceByName('belvg_customerattributes');
    }

    public static function getTypes()
    {
        return self::$_types;
    }
    
    public static function getDisplayOns()
    {
        return self::$_display_ons;
    }
    
    public static function getValidations()
    {
        return self::$_validations;
    }
    
    public static function getFieldsRelations()
    {
        return self::$_fields_relations;
    }
    
    public static function getTypeFieldsHider()
    {
        return self::$_type_fields_hider;
    }

    public static function getAllowedImagesExt()
    {
        return self::$_allowedImagesExt;
    }

    /**
     * parent method override
     * used to switch show_on_order/show_on_invoice/enabled
     * @return bool
     */
    public function toggleStatus()
    {
        $allValues = (method_exists("Tools", "getAllValues")) ? Tools::getAllValues() : belvg_customerattributes::getAllValues();
        foreach ($allValues as $param => $value) {
            if (preg_match('/show_on_order/', $param)) {
                $this->show_on_order = ($this->show_on_order) ? 0 : 1;
            } else if (preg_match('/^status/', $param)) {
                $this->enabled = ($this->enabled) ? 0 : 1;
            } else if (preg_match('/show_on_invoice/', $param)) {
                $this->show_on_invoice = ($this->show_on_invoice) ? 0 : 1;
            }
        }

        return $this->save();
    }

    /**
     * get user groups
     * visitor|cusromer|guest|new
     * @return array
     */
    public function getGroups()
    {
        return explode(';', $this->groups);
    }

    /**
     *  get all attributes
     * @param $groups
     * @param string $page
     * @param null $id_lang
     * @param $id_section
     * @param null $adminDisplay
     * @return mixed
     */
    public static function getAllAttributes($groups, $page = 'both', $id_lang = null, $id_section, $adminDisplay = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!is_array($groups)) {
            $groups = array($id_lang);
        }
        $_attributes = new Collection(__CLASS__, $id_lang);
        if (!in_array($page, self::$_display_ons)) {
            $page = 'both';
        }
        if ($adminDisplay) {
            switch ($adminDisplay) {
                case 'order' : $_attributes->where('show_on_order', '=', 1);
                    break;
                case 'invoice' : $_attributes->where('show_on_invoice', '=', 1);
                    break;
            }
        }
        $_attributes->where('enabled', '=', 1);
        $_attributes->where('id_section', '=', (int)$id_section);
        if ($page != 'both') {
            $_attributes->where('display_on', '=', array($page, 'both'));
        }

        if (!self::getModule()->isWriteable()) {
            $_attributes->where('type', 'notin', array('image', 'attachment'));
        }
        $_attributes->orderBy('sort_order');
        foreach ($_attributes as $offset => $item) {
            if (!count(array_intersect($groups, $item->getGroups()))) {
                $_attributes->offsetUnset($offset);
            }
        }

        return $_attributes->getAll();
    }

    /** converts dropdown or multiselect options from json to array
     * to show on front
     * @return array
     */
    public function getValues()
    {
        $val = Tools::jsonDecode($this->values, true);

        return explode(';', $val[Context::getContext()->language->id]);
    }

    /**
     * used to get users image url
     * @param $id_customer
     * @param $value
     * @param $type
     * @return string
     */
    public static function getFileUrl($id_customer, $value, $type)
    {
        return self::getModule()->getPath() . self::FILES_DIR . '/' . $id_customer . '/' . $type . '/' . $value;
    }

    /**
     *  to get users attachment path
     * @param $id_customer
     * @param $value
     * @param $type
     * @return string
     */
    public static function getFilePath($id_customer, $value, $type)
    {
        return self::getModule()->getLocalPath() . self::FILES_DIR . '/' . $id_customer . '/' . $type . '/' . $value;
    }

    /** gets customer attribute value
     * @param $id_customer
     * @return array|string
     */
    public function getCustomerValue($id_customer)
    {
        $value = (string)Db::getInstance()->getValue('
            SELECT `value`
            FROM `' . belvg_customerattributes::getDbPrefix() . 'customerattributes_customer`
            WHERE `id_customer` = ' . (int)$id_customer . '
            AND `id_attribute` = ' . (int)$this->id . '
        ');

        if ($this->type == 'multiselect') {
            $value = explode(';', $value);
        }

        return $value;
    }

    /** set customer value
     *
     * @param $id_customer
     * @param $value
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function setCustomerValue($id_customer, $value)
    {
        if ($this->type == 'multiselect') {
            $value = implode(';', $value);
        }

        $data = array(
            'id_attribute' => $this->id,
            'id_customer' => (int)$id_customer,
            'value' => $value
        );

        return Db::getInstance()->autoExecute(belvg_customerattributes::getDbPrefix() . 'customerattributes_customer', $data, 'REPLACE');
    }

    /** parent method override
     * @param bool|false $null_values
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($null_values = false)
    {
        // @hook actionObject*UpdateBefore
        Hook::exec('actionObjectUpdateBefore', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'UpdateBefore', array('object' => $this));

        $this->clearCache();

        // Automatically fill dates
        if (array_key_exists('date_upd', $this)) {
            $this->date_upd = date('Y-m-d H:i:s');
            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_upd'] = true;
            }
        }

        // Automatically fill dates
        if (array_key_exists('date_add', $this) && $this->date_add == null) {
            $this->date_add = date('Y-m-d H:i:s');
            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_add'] = true;
            }
        }

        $id_shop_list = Shop::getContextListShopID();
        if (count($this->id_shop_list) > 0) {
            $id_shop_list = $this->id_shop_list;
        }

        if (Shop::checkIdShopDefault($this->def['table']) && !$this->id_shop_default) {
            $this->id_shop_default = (in_array(Configuration::get('PS_SHOP_DEFAULT'), $id_shop_list) == true) ? Configuration::get('PS_SHOP_DEFAULT') : min($id_shop_list);
        }
        // Database update
        if (!$result = Db::getInstance()->update($this->def['table'], $this->getFields(), '`'.pSQL($this->def['primary']).'` = '.(int)$this->id, 0, $null_values)) {
            return false;
        }

        // Database insertion for multishop fields related to the object
        if (Shop::isTableAssociated($this->def['table'])) {
            $fields = $this->getFieldsShop();
            $fields[$this->def['primary']] = (int)$this->id;
            if (is_array($this->update_fields)) {
                $update_fields = $this->update_fields;
                $this->update_fields = null;
                $all_fields = $this->getFieldsShop();
                $all_fields[$this->def['primary']] = (int)$this->id;
                $this->update_fields = $update_fields;
            } else {
                $all_fields = $fields;
            }

            foreach ($id_shop_list as $id_shop) {
                $fields['id_shop'] = (int)$id_shop;
                $all_fields['id_shop'] = (int)$id_shop;
                $where = $this->def['primary'].' = '.(int)$this->id.' AND id_shop = '.(int)$id_shop;

                // A little explanation of what we do here : we want to create multishop entry when update is called, but
                // only if we are in a shop context (if we are in all context, we just want to update entries that alread exists)
                $shop_exists = Db::getInstance()->getValue('SELECT '.$this->def['primary'].' FROM '._DB_PREFIX_.$this->def['table'].'_shop WHERE '.$where);
                if ($shop_exists) {
                    $result &= Db::getInstance()->update($this->def['table'].'_shop', $fields, $where, 0, $null_values);
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $result &= Db::getInstance()->insert($this->def['table'].'_shop', $all_fields, $null_values);
                }
            }
        }

        // Database update for multilingual fields related to the object
        if (isset($this->def['multilang']) && $this->def['multilang']) {
            $fields = $this->getFieldsLang();
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    foreach (array_keys($field) as $key) {
                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PrestaShopException('key '.$key.' is not a valid table or identifier');
                        }
                    }

                    // If this table is linked to multishop system, update / insert for all shops from context
                    if ($this->isLangMultishop()) {
                        $id_shop_list = Shop::getContextListShopID();
                        if (count($this->id_shop_list) > 0) {
                            $id_shop_list = $this->id_shop_list;
                        }
                        foreach ($id_shop_list as $id_shop) {
                            $field['id_shop'] = (int)$id_shop;
                            $where = pSQL($this->def['primary']).' = '.(int)$this->id
                                .' AND id_lang = '.(int)$field['id_lang']
                                .' AND id_shop = '.(int)$id_shop;

                            if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->def['table']).'_lang WHERE '.$where)) {
                                $result &= Db::getInstance()->update($this->def['table'].'_lang', $field, $where);
                            } else {
                                $result &= Db::getInstance()->insert($this->def['table'].'_lang', $field);
                            }
                        }
                    }
                    // If this table is not linked to multishop system ...
                    else {
                        $where = pSQL($this->def['primary']).' = '.(int)$this->id
                        .' AND id_lang = '.(int)$field['id_lang'];
                        if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->def['table']).'_lang WHERE '.$where)) {
                            $result &= Db::getInstance()->update($this->def['table'].'_lang', $field, $where);
                        } else {
                            $result &= Db::getInstance()->insert($this->def['table'].'_lang', $field, $null_values);
                        }
                    }
                }
            }
        }

        // @hook actionObject*UpdateAfter
        Hook::exec('actionObjectUpdateAfter', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'UpdateAfter', array('object' => $this));

        return $result;
    }
}

