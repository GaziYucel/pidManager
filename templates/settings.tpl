{**
 * templates/settings.tpl
 *
 * Copyright (c) 2024+ TIB Hannover
 * Copyright (c) 2024+ Gazi Yücel
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Settings form for the pidManagerSettings plugin.
 *}
<script>
    $(function () {
        $('#{$pluginName}Settings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
    });
</script>

<form class="pkp_form" method="POST" id="{$pluginName}Settings"
      action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic"
      plugin=$pluginName verb="settings" save=true}">

    {csrf}

    {fbvFormArea id="{$pluginName}SettingsArea"}

    {fbvFormSection
    for="{APP\plugins\generic\pidManager\classes\Constants::settingEnableIgsn}"
    title="plugins.generic.pidManager.settings.title" list="true"}
        <p class="description align-justify" style="color: rgba(0,0,0,0.54)">
            {translate key="plugins.generic.pidManager.settings.description"}
            {fbvElement
            type="checkbox"
            id="{APP\plugins\generic\pidManager\classes\Constants::settingEnableIgsn}"
            checked=${APP\plugins\generic\pidManager\classes\Constants::settingEnableIgsn}
            label="plugins.generic.pidManager.igsn.settings.label"}

            {fbvElement
            type="checkbox"
            id="{APP\plugins\generic\pidManager\classes\Constants::settingEnablePidinst}"
            checked=${APP\plugins\generic\pidManager\classes\Constants::settingEnablePidinst}
            label="plugins.generic.pidManager.pidinst.settings.label"}
        </p>
    {/fbvFormSection}

    {/fbvFormArea}

    {fbvFormButtons submitText="common.save"}
</form>
