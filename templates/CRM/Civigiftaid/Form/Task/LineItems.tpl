<table>
    <tr>
        <th>Item</th>
        <th>Quantity</th>
        <th>Type</th>
        <th>Description</th>
        <th>Amount</th>
    </tr>
    {assign var="count" value=0}
    {foreach from=$row.line_items item=item}
        <tr {if $count % 2 !== 0 }class="odd"{/if}>
            <td>{$item.item}</td>
            <td>{$item.qty}</td>
            <td>{$row.financial_account}</td>
            <td>{$item.description}</td>
            <td>{$item.amount}</td>
        </tr>
        {assign var="count" value=$count+1}
    {/foreach}
</table>