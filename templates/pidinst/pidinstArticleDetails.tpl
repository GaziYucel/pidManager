{**
 * templates/pidinst/pidinstArticleDetails.tpl
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * IGSN Article View
 *}

{if $pidinsts}
    <section class="item pidinst">
        <h2 class="label">
            {translate key="plugins.generic.pidManager.pidinst.label"}
        </h2>
        <div class="value">
            {foreach from=$pidinsts item="pidinst"}
                <p>{$pidinst->doi}<br/>{$pidinst->label}</p>
            {/foreach}
        </div>
    </section>
{/if}
