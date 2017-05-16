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

<div class="additional-info-container">
    <div class="panel">
        <div class="panel-heading">
            <img src="../modules/belvg_customerattributes/logo.png" style="width: 20px;margin-right: 10px;"/>
            {l s='ADDITIONAL INFO' mod='belvg_customerattributes'}
        </div>
            {foreach $belvg_attr_sections as $belvg_attr_section}
                <div class="admin-order-section-block">
                    <div class='admin-order-section'>
                        <h4>{$belvg_attr_section->name|escape:'htmlall':'UTF-8'}</h4>

                        {foreach from=$belvg_attr_section->attributes item=attribute}
                            {assign var=input_name value='belvg_customerattributes_'|cat:$attribute->code}
                            {assign var=customer_value value=$attribute->getCustomerValue($belvg_customer_id)}

                            {if $attribute->type eq 'text'}
                                <p>
                                    {$attribute->name|escape:'htmlall':'UTF-8'}:<strong>{$customer_value|escape:'htmlall':'UTF-8'}</strong>;
                                </p>
                            {elseif $attribute->type eq 'textarea'}
                                <p>
                                    {$attribute->name|escape:'htmlall':'UTF-8'}:<strong>{$customer_value|escape:'htmlall':'UTF-8'}</strong>;
                                </p>
                            {elseif $attribute->type eq 'date'}
                                <p>
                                    {$attribute->name|escape:'htmlall':'UTF-8'}:<strong>{$customer_value|escape:'htmlall':'UTF-8'}</strong>;
                                </p>
                            {elseif $attribute->type eq 'radio'}
                                <p>
                                    {$attribute->name|escape:'htmlall':'UTF-8'}:
                                    <strong>
                                        {foreach from=$attribute->getValues() item=value key=id}
                                            {$value|escape:'htmlall':'UTF-8'}
                                        {/foreach}
                                    </strong>;
                                </p>

                            {elseif $attribute->type eq 'multiselect'}
                                <p>
                                    {$attribute->name|escape:'htmlall':'UTF-8'}:
                                    <strong>
                                        {foreach $customer_value as $value}
                                            {$value|escape:'htmlall':'UTF-8'};
                                        {/foreach}
                                    </strong>
                                </p>

                            {elseif $attribute->type eq 'dropdown'}
                                <p>
                                    {$attribute->name|escape:'htmlall':'UTF-8'}:
                                    <strong>
                                        {foreach $customer_value as $value}
                                            {$value|escape:'htmlall':'UTF-8'};
                                        {/foreach}
                                    </strong>
                                </p>
                            {elseif $attribute->type eq 'image'}
                                <p>
                                    {$attribute->name|escape:'htmlall':'UTF-8'}:
                                        <img style="max-width:200px;" class="attach img" src="..{CustomerAttributes::getFileUrl($belvg_customer_id, $customer_value, $attribute->type)|escape:'htmlall':'UTF-8'}" />
                                </p>
                            {elseif $attribute->type eq 'attachment'}
                                {$attribute->name|escape:'htmlall':'UTF-8'}:
                                {if $customer_value}
                                        <a  class="attach link" href="{CustomerAttributes::getFileUrl($belvg_customer_id, $customer_value, $attribute->type)|escape:'htmlall':'UTF-8'}">{l s='Download' mod='belvg_customerattributes'}</a>
                                    {/if}
                            {/if}
                        {/foreach}
                    </div>
                </div>
            {/foreach}
    </div>
</div>
