{**
 * templates/pidinst/articleDetails.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Article Details View
 *}

{if $items}
    <section class="item {$pidName}">
        <h2 class="label">
            {translate key="plugins.generic.pidManager.{$pidName}.label"}
        </h2>
        <div class="value">
            {foreach from=$items item="item"}
                <p>
                    {if $item->creators}{$item->creators}. {/if}
                    {if $item->publicationYear}({$item->publicationYear}). {/if}
                    {if $item->label}<i>{$item->label}.</i> {/if}
                    {if $item->publisher}{$item->publisher}. {/if}
                    {if $item->doi}{$item->doi}{/if}
                </p>
            {/foreach}
        </div>
    </section>
{/if}
