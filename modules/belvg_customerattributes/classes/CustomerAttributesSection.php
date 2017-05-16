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

class CustomerAttributesSection extends ObjectModel
{
    /** @var  int section id */
    public $id_section;

    /** @var name */
    public $name;

    /** @var  string
     *  placement on registration page relatively to main data //      top or bottom */
    public $placement;

    /** @var  int display order on registration page */
    public $display_order;

    /** @var  int display on invoice */
    public $show_on_invoice;

    /** @var  int display on  admin order page */
    public $show_on_order;

    /** @var  bool is enabled */
    public $enabled;

    protected static $placement_types = array(
        'top',
        'bottom'
    );

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' =>  'belvg_customerattributes_section',
        'primary' => 'id_section',
        'multilang' => true,
        //'multilang_shop' => true,
        'fields' => array(
            'id_section' => array('type' => self::TYPE_INT),
            'placement' => array('type' => self::TYPE_STRING),
            'display_order' => array('type' => self::TYPE_INT),
            'show_on_invoice' => array('type' => self::TYPE_INT),
            'show_on_order' => array('type' => self::TYPE_INT),

        'enabled' => array('type' => self::TYPE_INT),
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => true
            ),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        )
    );

    public function __construct($id = null, $id_lang = null)
    {
        return parent::__construct($id, $id_lang);

    }

    /**
     * overrides parent class method to switch show on order/invoice
     * and enabled statuses
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
     * @param $groups  - guest|customer
     * @param string $page  -
     * @param null $id_lang
     * @param null $placement
     * @param null $adminDisplay -  for admin views
     * @return Collection
     */
    public static function getAllSections($groups, $page = 'both', $id_lang = null, $placement = null, $adminDisplay = null)
    {
        $_sections = new Collection(__CLASS__, $id_lang);

        if ($adminDisplay) {
            switch ($adminDisplay) {
                case 'order' : $_sections->where('show_on_order', '=', 1);
                    break;
                case 'invoice' : $_sections->where('show_on_invoice', '=', 1);
                    break;
            }
        }
        $placement = ($placement) ? $placement : array('top', 'bottom');
        $_sections->where('placement', '=', $placement);
        $_sections->where('enabled', '=', 1);
        $_sections->orderBy('display_order');
        $_sections->getAll();
        foreach ($_sections as $_section) {
            $_section->attributes = CustomerAttributes::getAllAttributes($groups, $page = 'both', $id_lang = null, $_section->id_section, $adminDisplay);
        }

        return $_sections;
    }
}

