{if $igsnS}
    <section class="item igsn">
        <h2 class="label">
            {translate key="plugins.generic.pidManager.igsn.label"}
        </h2>
        <div class="value">
            {foreach from=$igsnS item="igsn"}
                <p>{$igsn->id}<br />{$igsn->label}</p>
            {/foreach}
        </div>
    </section>
{/if}
