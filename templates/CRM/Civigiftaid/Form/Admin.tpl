{crmStyle ext=uk.co.compucorp.civicrm.giftaid file=resources/css/dist.css}
{crmScript ext=uk.co.compucorp.civicrm.giftaid file=resources/js/script.js}

<div id="gift-aid-settings" class="crm-block crm-form-block crm-export-form-block gift-aid settings">
    <h3>Gift Aid Financial Types</h3>

    <div class="crm-section">
        <div class="label">{$form.globally_enabled.label}</div>
        <div class="content">
            {$form.globally_enabled.html}
            <span class="help-text">Enable gift aid for line items of any financial type</span>
        </div>
        <div class="clear"></div>
    </div>

    <div id="financial-types-container" class="crm-section">
        <div class="label">{$form.financial_types_enabled.label}</div>
        <div class="content">{$form.financial_types_enabled.html}</div>
        <div class="clear"></div>
    </div>

    {* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT)

    {foreach from=$elementNames item=elementName}
        <div class="crm-section">
            <div class="label">{$form.$elementName.label}</div>
            <div class="content">{$form.$elementName.html}</div>
            <div class="clear"></div>
        </div>
    {/foreach}

    {* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

      <div>
        <span>{$form.favorite_color.label}</span>
        <span>{$form.favorite_color.html}</span>
      </div>

    {* FOOTER *}

    <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
</div>

{literal}
    <script type="text/javascript">
        cj('#financial_types_enabled').crmSelect2();
    </script>
{/literal}
