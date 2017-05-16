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

<div class="account_creation belvg_customerattributes">
		<input type="hidden" name="belvg_customerattributes" value="1" />
		<input type="hidden" name="belvg_customer_id" value="{$belvg_customer_id|escape:'htmlall':'UTF-8'}" />

		{foreach $belvg_attr_sections as $belvg_attr_section}
			<div class="belvg_attr_section">
				<h4>{$belvg_attr_section->name|escape:'htmlall':'UTF-8'}</h4>
				{foreach from=$belvg_attr_section->attributes item=attribute}
					{assign var=input_name value='belvg_customerattributes_'|cat:$attribute->code}
					{assign var=customer_value value=$attribute->getCustomerValue($belvg_customer_id)}
					{if $smarty.server.REQUEST_METHOD eq 'POST'}
						{assign var=customer_value value=Tools::getValue($input_name)}
					{/if}
					<div class="form-group {if $attribute->required eq 1}required {/if}{if $attribute->type eq 'radio'} {elseif $attribute->type eq 'textarea'} textarea{elseif ($attribute->type eq 'multiselect' || $attribute->type eq 'dropdown')}select{else}text{/if}">

						{if $attribute->type eq 'radio'}
						<div class="clearfix">
							<label>{$attribute->name|escape:'htmlall':'UTF-8'}{if $attribute->required eq 1} <sup>*</sup>{/if}</label>
							<br>
							{else}
							<label for="{$input_name|escape:'htmlall':'UTF-8'}" class="label-title">
								{$attribute->name|escape:'htmlall':'UTF-8'}{if $attribute->required eq 1} <sup>*</sup>{/if}
							</label>
						{/if}

							{if $attribute->type eq 'text'}
							<input type="text" class="text form-control" maxlength="{$attribute->max_text_length}" id="{$input_name|escape:'htmlall':'UTF-8'}" name="{$input_name|escape:'htmlall':'UTF-8'}" value="{$customer_value|escape:'htmlall':'UTF-8'}">
							{elseif $attribute->type eq 'textarea'}
							<textarea rows="6" name="{$input_name|escape:'htmlall':'UTF-8'}" maxlength="{$attribute->max_text_length}" id="{$input_name|escape:'htmlall':'UTF-8'}" class="form-control">{$customer_value|escape:'htmlall':'UTF-8'}</textarea>
							{elseif $attribute->type eq 'date'}
							<input type="text" class="text belvg_date form-control" maxlength="{$attribute->max_text_length}" id="{$input_name|escape:'htmlall':'UTF-8'}" name="{$input_name|escape:'htmlall':'UTF-8'}" value="{$customer_value|escape:'htmlall':'UTF-8'}">
							{elseif $attribute->type eq 'radio'}
							{foreach from=$attribute->getValues() item=value key=id}
								<div class="radio-inline">
									<label class="top">
										<input type="radio" name="{$input_name|escape:'htmlall':'UTF-8'}" id="{$input_name|escape:'htmlall':'UTF-8'}_{$id|escape:'htmlall':'UTF-8'}" value="{$value|escape:'htmlall':'UTF-8'}"{if $customer_value eq $value} checked="checked"{/if} class="form-control">
										{$value|escape:'htmlall':'UTF-8'}
									</label>
								</div>
							{/foreach}
						</div>
						{elseif $attribute->type eq 'multiselect'}
						<select name="{$input_name|escape:'htmlall':'UTF-8'}[]" id="{$input_name|escape:'htmlall':'UTF-8'}" multiple="multiple" size="5" style="width: 372px;" class="form-control">
							{foreach from=$attribute->getValues() item=value key=id}
								<option {if in_array($value, $customer_value)}selected="selected" {/if}value="{$value|escape:'htmlall':'UTF-8'}">{$value|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
						{elseif $attribute->type eq 'dropdown'}
						<select name="{$input_name|escape:'htmlall':'UTF-8'}" id="{$input_name|escape:'htmlall':'UTF-8'}" style="width: 372px;" class="form-control">
							{foreach from=$attribute->getValues() item=value key=id}
								<option {if $customer_value eq $value}selected="selected" {/if}value="{$value|escape:'htmlall':'UTF-8'}">{$value|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
						{elseif $attribute->type eq 'image'}
							<input class="belvg_file" type="file" name="{$input_name|escape:'htmlall':'UTF-8'}" id="{$input_name|escape:'htmlall':'UTF-8'}" />
							<span>Max file size :{$attribute->file_size|escape:'htmlall':'UTF-8'} (KB) </span>
							{if $customer_value|escape:'htmlall':'UTF-8'}
								<br>
								<a target="_blank" href="{CustomerAttributes::getFileUrl($belvg_customer_id, $customer_value, $attribute->type)|escape:'htmlall':'UTF-8'}">
									<img class="attach img" src="{CustomerAttributes::getFileUrl($belvg_customer_id, $customer_value, $attribute->type)|escape:'htmlall':'UTF-8'}?cache={time()|escape:'htmlall':'UTF-8'}" />
								</a>
							{/if}
						{elseif $attribute->type eq 'attachment'}
					<input class="belvg_file" type="file" name="{$input_name|escape:'htmlall':'UTF-8'}" id="{$input_name|escape:'htmlall':'UTF-8'}" download />
						<p>Max file size: {$attribute->file_size|escape:'htmlall':'UTF-8'}(KB)</p>
						<p>File extensions: {$attribute->file_extensions|escape:'htmlall':'UTF-8'}</p>
						{if $customer_value}
							<br>
							<a class="attach link" href="{CustomerAttributes::getFileUrl($belvg_customer_id, $customer_value, $attribute->type)|escape:'htmlall':'UTF-8'}">{l s='Download' mod='belvg_customerattributes'}</a>
						{/if}
						{/if}
					</div>
				{/foreach}
			</div>
		{/foreach}
</div>
<script>
	{if isset($set_enctype)}
	$('#account-creation_form').attr('enctype', 'multipart/form-data');
	{/if}
	$('.belvg_date').datepicker({
		changeYear:true,
		yearRange: "1922:" + new Date().getFullYear(),
		changeMonth : true
	});
</script>
