{**
 * templates/igsn/igsnArticleDetails.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * IGSN Article View
 *}

{if $items}
    <section class="item igsn">
        <h2 class="label">
            {translate key="plugins.generic.pidManager.igsn.label"}
        </h2>
        <div class="value">
            {foreach from=$items item="item"}
                <p>{$item->doi}<br/>{$item->label}</p>
            {/foreach}
        </div>
    </section>
{/if}
