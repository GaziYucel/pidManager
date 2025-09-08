{**
 * templates/pidinst/articleDetails.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Article Details View
 *}
<link rel="stylesheet" href="{$assetsUrl}/css/frontend.css" type="text/css"/>

{if $items}
    <section class="item {$pidName}">
        <h2 class="label">
            {translate key="plugins.generic.pidManager.{$pidName}.label"}
        </h2>
        <div class="value" id="{$pidName}-item-list">
            {foreach from=$items item="item" name="itemLoop"}
                <p class="{$pidName}-item-entry"{if $smarty.foreach.itemLoop.index >= 5} style="display:none;"{/if}>
                    {if $item->creators}{$item->creators}. {/if}
                    {if $item->publicationYear}({$item->publicationYear}). {/if}
                    {if $item->label}<i>{$item->label}.</i> {/if}
                    {if $item->publisher}{$item->publisher}. {/if}
                    {if $item->doi}{$item->doi}{/if}
                </p>
            {/foreach}
        </div>
        {if count($items) > 5}
            <a id="{$pidName}-toggleItems" class="pkpButton" onclick="{$pidName}ToggleItems()">
                {translate key="plugins.generic.pidManager.articleDetails.buttonShowAll.showAll"}
            </a>
        {/if}
    </section>
{/if}

<script>
function {$pidName}ToggleItems() {
    const items = document.querySelectorAll('#{$pidName}-item-list .{$pidName}-item-entry');
    const btn = document.getElementById('{$pidName}-toggleItems');
    const showingAll = btn.dataset.showingAll === "true";

    if (!showingAll) {
        // Show all items
        items.forEach(item => item.style.display = "");
        btn.textContent = "{translate key="plugins.generic.pidManager.articleDetails.buttonShowAll.minimise"}";
        btn.dataset.showingAll = "true";
    } else {
        // Show only first 5
        items.forEach((item, idx) => {
            item.style.display = idx < 5 ? "" : "none";
        });
        btn.textContent = "{translate key="plugins.generic.pidManager.articleDetails.buttonShowAll.showAll"}";
        btn.dataset.showingAll = "false";
    }
}
</script>