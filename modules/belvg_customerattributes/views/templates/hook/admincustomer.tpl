{*
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
*}
<div class="col-lg-12 panel">
	<div class="additional-info-container">
		<div class="panel-heading">
			<img src="../modules/belvg_customerattributes/logo.png" style="width: 20px;margin-right: 10px;"/>
			{l s='ADDITIONAL INFO' mod='belvg_customerattributes'}
		</div>
		<form action="#" method="post" id="account-creation_form" enctype="multipart/form-data">
			{if isset($postState)}
				<p class="alert {$postState|escape:'htmlall':'UTF-8'}">
					{if $postState eq 'alert-success'}
						{l s='Your additional information has been successfully updated.' mod='belvg_customerattributes'}
					{else}
						{$errors|escape:'htmlall':'UTF-8'}
					{/if}
				</p>
			{/if}
			{include file="./account.tpl"}
			<p class="cart_navigation required submit">
				<input type="submit" name="submitAccount" id="submitAccount" value="{l s='Save' mod='belvg_customerattributes'}" class="btn btn-default">
				<span><sup>*</sup>{l s='Required field' mod='belvg_customerattributes'}</span>
			</p>
		</form>
	</div>
</div>
