
    <ul class="breadcrumbs">
        {foreach item=node from=$path name=breadcrumbs_loop}
            <li>
                {if !$smarty.foreach.breadcrumbs_loop.last}
                    <a href="{$node->link}">{$node->text}</a> &gt;
                {else}
                    <span>{$node->text}</span>
                {/if}
            </li>
        {/foreach}
    </ul>
