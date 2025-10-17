{**
 * templates/base/articleDetails.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Article Details View
 *}

{assign var="itemCount" value=$items|@count}

{if $items}
    <section id="{$pidName}" class="item {$pidName}">
        <h2 class="label">
            {translate key="plugins.generic.pidManager.{$pidName}.label"}
        </h2>
        <a onclick="{$pidName}DownloadCsv()" class="obj_galley_link pkpButton">CSV</a>
        <p class="description align-justify description-color">
            {translate key="plugins.generic.pidManager.{$pidName}.generalDescription"}
            {translate key="plugins.generic.pidManager.{$pidName}.articleDetails.details"}
        </p>
        <div class="value" id="{$pidName}-item-list">
            {foreach from=$items item="item" name="itemLoop"}
                <p class="{$pidName}-item-entry"{if $smarty.foreach.itemLoop.index >= 5} style="display:none;"{/if}">
                    {if $item->creators}{$item->creators}. {/if}
                    {if $item->publicationYear}({$item->publicationYear}). {/if}
                    {if $item->label}<i>{$item->label}.</i> {/if}
                    {if $item->publisher}{$item->publisher}. {/if}
                    {if $item->doi}<a href="{$doiPrefix}/{$item->doi}" target="_blank">{$doiPrefix}/{$item->doi}</a>{/if}
                </p>
            {/foreach}
        </div>
        {if count($items) > 5}
            <a id="{$pidName}-toggleItems" class="pkpButton" onclick="{$pidName}ToggleItems()" data-item-count="{$itemCount}">
                {translate key="plugins.generic.pidManager.articleDetails.buttonShowAll.showAll"}
                ({$itemCount})
            </a>
        {/if}
    </section>
{/if}

<script>
    function {$pidName}ToggleItems() {
        const items = document.querySelectorAll('#{$pidName}-item-list .{$pidName}-item-entry');
        const button = document.getElementById('{$pidName}-toggleItems');
        const isMinimised = button.dataset.isMinimised === "true";
        const itemCount = button.dataset.itemCount;

        if (isMinimised) {
            // Show only first 5 items
            items.forEach((item, index) => {
                item.style.display = index < 5 ? "" : "none";
            });
            button.textContent = "{translate key="plugins.generic.pidManager.articleDetails.buttonShowAll.showAll"} (" + itemCount + ")";
            button.dataset.isMinimised = "false";
        } else {
            // Show all items
            items.forEach(item => item.style.display = "");
            button.textContent = "{translate key="plugins.generic.pidManager.articleDetails.buttonShowAll.minimise"}";
            button.dataset.isMinimised = "true";
        }
    }
    function {$pidName}DownloadCsv() {
        const EOL = (typeof process !== 'undefined' && process.platform === 'win32') ? '\r\n' : '\n';
        const items = {$itemsJson};

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += '"creators","publicationYear","label","publisher","doi"' + EOL;
        items.forEach((item) => {
            csvContent +=
                '"' + item['creators'].replaceAll('"', '\"') + '",' +
                '"' + item['publicationYear'].replaceAll('"', '\"') + '",' +
                '"' + item['label'].replaceAll('"', '\"') + '",' +
                '"' + item['publisher'].replaceAll('"', '\"') + '",' +
                '"' + '{$doiPrefix}/' + item['doi'].replaceAll('"', '\"') + '"' + EOL;
        });

        // Encode the URI
        const encodedUri = encodeURI(csvContent);

        // Create a link element
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', '{$pidName}-data.csv');

        // Append the link to the body
        document.body.appendChild(link);

        // Trigger the download
        link.click();

        // Cleanup: remove the link element
        document.body.removeChild(link);
    }
</script>
