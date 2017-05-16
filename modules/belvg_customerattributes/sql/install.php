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

if (!class_exists('belvg_customerattributes')) {
    return;
}

$prefix = belvg_customerattributes::getDbPrefix();
return array(
    'CREATE TABLE IF NOT EXISTS `' . $prefix . 'customerattributes_section` (
        `id_section` int(10) unsigned NOT NULL auto_increment,
        `placement` varchar(20) NOT NULL,
        `display_order` INT(10) NULL DEFAULT "0",
        `show_on_invoice` INT(10) NULL DEFAULT "0",
        `show_on_order` INT(10) NULL DEFAULT "0",
        `enabled` int(10) default "0",
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_section`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
    '
    CREATE TABLE IF NOT EXISTS `' . $prefix . 'customerattributes_section_lang` (
        `id_section` int(10) unsigned NOT NULL auto_increment,
        `id_lang` INT(10) UNSIGNED NOT NULL,
        `name` VARCHAR(152) NULL DEFAULT NULL,
        PRIMARY KEY (`id_section`, `id_lang`),
        INDEX `customerattributes_section_lang` (`id_section`),
        FOREIGN KEY (`id_section`)
        REFERENCES `' . $prefix . 'customerattributes_section` (`id_section`)
        ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
    '
    CREATE TABLE IF NOT EXISTS `' . $prefix . 'customerattributes` (
      `id_attribute` int(10) unsigned NOT NULL auto_increment,
      `id_section` int(10) unsigned NOT NULL,
      `code` varchar(255) NOT NULL,
      `type` enum("' . implode('", "', CustomerAttributes::getTypes()) . '") NOT NULL,
      `enabled` int(10) NOT NULL default "0",
      `sort_order` int(10) NOT NULL default "0",
      `display_on` enum("' . implode('", "', CustomerAttributes::getDisplayOns()) . '") NOT NULL,
      `show_on_invoice` int(1) default "0",
      `show_on_order` int(1) default "0",
      `required` int(1) NOT NULL default "0",
      `validation` varchar(255) NOT NULL,
      `values` text NOT NULL,
      `groups` text NOT NULL,
      `max_text_length` int(10) NOT NULL,
      `file_size` int(10) NOT NULL,
      `file_extensions` text NOT NULL,
      `date_add` datetime NOT NULL,
      `date_upd` datetime NOT NULL,
      PRIMARY KEY  (`id_attribute`),
      INDEX ` customerattributes` (`id_section`),
      FOREIGN KEY (`id_section`)
      REFERENCES `' . $prefix . 'customerattributes_section` (`id_section`)
      ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
    '
    CREATE TABLE IF NOT EXISTS `' . $prefix . 'customerattributes_lang` (
      `id_attribute` int(10) unsigned NOT NULL,
      `id_lang` int(10) unsigned NOT NULL,
      `name` varchar(255) NOT NULL,
      PRIMARY KEY (`id_attribute`, `id_lang`),
      INDEX `belvg_customerattributes_lang` (`id_attribute`),
      FOREIGN KEY (`id_attribute`)
      REFERENCES `' . $prefix . 'customerattributes` (`id_attribute`)
      ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
    '
    CREATE TABLE IF NOT EXISTS `' . $prefix . 'customerattributes_shop` (
      `id_section` int(10) unsigned NOT NULL,
      `id_shop` int(10) unsigned NOT NULL,
      PRIMARY KEY (`id_section`, `id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
    '
    CREATE TABLE IF NOT EXISTS `' . $prefix . 'customerattributes_customer` (
      `id_attribute` int(10) unsigned NOT NULL,
      `id_customer` int(10) unsigned NOT NULL,
      `value` text NOT NULL,
      PRIMARY KEY (`id_attribute`, `id_customer`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
