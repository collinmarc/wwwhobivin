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

(function($){
	$(".multishop_toolbar").hide();

	$('input[type="radio"][name="type"]').click(function(){
		for (var field in hidedFields) {
			if (hidedFields[field] == 'values') {
				for (var lang in usedLangs ) {
					var elem = $('input[name="' + hidedFields[field] + '_' + usedLangs[lang].id_lang +'"]');
					elem.parents('.form-group').hide();
				}
			} else {
				var elem = $('input[name="' + hidedFields[field] + '"]');
				elem.parents('.form-group:first').hide();//.prev().hide();
			}
		}

		var type = $(this).val();
		if (typeof shower[type] == 'object') {
			for (var field in shower[type]) {
				if (shower[type][field] == 'values') {
					var elem = $('input[name="' + shower[type][field] + '_' + usedLangs[0].id_lang + '"]');
					elem.parents('.form-group').show();//.prev().show();
				}
				var elem = $('input[name="'+shower[type][field]+'"]');
				elem.parents('.form-group:first').show();//.prev().show();
			}
		}
	});

	$('input[type="radio"][name="type"]:checked').click();
})(jQuery);