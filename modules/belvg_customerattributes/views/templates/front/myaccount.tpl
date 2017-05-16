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
{capture name=path}
		<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}" rel="nofollow" title="{l s='My Account' mod='belvg_customerattributes'}">{l s='My Account' mod='belvg_customerattributes'}</a>
		<span class="navigation-pipe">&gt;</span>
		{l s='Additional Information' mod='belvg_customerattributes'}
{/capture}
	
{if isset(Context::getContext()->cookie->belvg_errors)}
	{if !($belvg_errors|is_array)}
		<p class="alert alert-success">
			{l s='Your additional information has been successfully updated.' mod='belvg_customerattributes'}
		</p>

	{else}
		<p class="alert alert-danger">
			{foreach $belvg_errors as $error}
				{$error}<br>
			{/foreach}
		</p>

		<script type="text/javascript">
			{literal}
					$(document).ready(function(){
						$(document).on('click', document, function(e){
							var element = $(e.data.activeElement);
							console.log(element.attr('class'));
							if (element.hasClass('form-control') || element.hasClass('belvg_file')) {
								return true;
							} else {
								e.preventDefault();
								alert('{/literal}{l s='You must fill required fields' mod='belvg_customerattributes'}{literal}');
							}
						});
					});
			{/literal}
		</script>
	{/if}
	{Context::getContext()->cookie->unsetFamily('belvg_errors')|escape:'htmlall':'UTF-8'}
{/if}


<div class="box">
	<form action="{$link->getModuleLink('belvg_customerattributes', 'myaccount')}" method="post" id="account-creation_form" enctype="multipart/form-data">
		{include file="../hook/account.tpl"}
		<br><br>
		<p style="float:right; color:red;"><span><strong></strong>{l s='Required field' mod='belvg_customerattributes'}</span><sup>*</sup></p>
		<p class="cart_navigation required submit">
			<button type="submit" name="submitAccount" id="submitAccount" class="btn btn-default button button-small form-control">
				<span>
					Save
					<i class="icon-chevron-right right"></i>
				</span>
			</button>
		</p>
	</form>
</div>