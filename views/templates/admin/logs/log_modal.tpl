{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}
<div
    class="btn btn-default button js-log-button"
    data-toggle="modal"
    data-log-id="{$log_id|escape:'htmlall':'UTF-8'}"
    data-information-type="{$log_information_type|escape:'htmlall':'UTF-8'}"
    data-target="log-modal-{$log_id|escape:'htmlall':'UTF-8'}-{$log_information_type|escape:'htmlall':'UTF-8'}"
>
    {l s='View' mod='dpdbaltics'}
</div>

<div id="log-modal-{$log_id|escape:'htmlall':'UTF-8'}-{$log_information_type|escape:'htmlall':'UTF-8'}" class="modal">
    <div class="log-modal-overlay"></div>

    <div class="log-modal-window">
        <div class="log-modal-title">
            <h4 class="log-modal-title-text">
                {if $log_information_type === 'request'}
                    {$log_id|escape:'htmlall':'UTF-8'}: {l s='Request data' mod='dpdbaltics'}
                {elseif $log_information_type === 'response'}
                    {$log_id|escape:'htmlall':'UTF-8'}: {l s='Response data' mod='dpdbaltics'}
                {/if}
            </h4>
            <button type="button" class="log-modal-close js-log-modal-close" aria-label="Close">&times;</button>
        </div>

        <div class="log-modal-content">
            <div class="log-modal-content-spinner hidden"></div>
            <pre class="log-modal-content-data hidden"></pre>
        </div>
    </div>
</div>
