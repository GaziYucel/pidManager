{**
 * templates/igsn/articleDetails.tpl
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
                <p>{$item->doi}<br/>{$item->label}</p>
            {/foreach}
        </div>
    </section>
{/if}
