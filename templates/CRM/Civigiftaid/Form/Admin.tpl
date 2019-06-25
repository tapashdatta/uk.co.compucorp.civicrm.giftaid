{crmStyle ext=uk.co.compucorp.civicrm.giftaid file=resources/css/dist.css}
{crmScript ext=uk.co.compucorp.civicrm.giftaid file=resources/js/script.js}

<div id="gift-aid-settings" class="crm-block crm-form-block crm-export-form-block gift-aid settings">
  <h3>Gift Aid Financial Types</h3>
  <table class="form-layout-compressed">
    <tbody>
      <tr>
        <td class="label">
          <label>{$form.globally_enabled.label}</label>
        </td>
        <td>
          {$form.globally_enabled.html}
          <span class="help-text">Enable gift aid for line items of any financial type</span>
        </td>
      </tr>
      <tr id="financial-types-container">
        <td class="label">
          <label>{$form.financial_types_enabled.label}</label>
        </td>
        <td>
          {$form.financial_types_enabled.html}
        </td>
      </tr>
    </tbody>
  </table>

  {* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT)
  <table>
    <tbody>
      {foreach from=$elementNames item=elementName}
        <tr>
          <td class="label">
            <labek>{$form.$elementName.label}</label>
          </td>
          <td>
            {$form.$elementName.html}
          </td>
        </tr>
      {/foreach}
    </tbody>
  </table>

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
