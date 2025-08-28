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
        $('#{$smarty.const.PID_MANAGER_PLUGIN_NAME}Settings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
    });
</script>

<form class="pkp_form" method="POST" id="{$smarty.const.PID_MANAGER_PLUGIN_NAME}Settings"
      action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic"
      plugin=$pluginName verb="settings" save=true}">

    {csrf}

    {fbvFormArea id="{$smarty.const.PID_MANAGER_PLUGIN_NAME}SettingsArea"}

    {fbvFormSection
    for="{APP\plugins\generic\pidManager\classes\Constants::settingEnableIgsn}"
    title="plugins.generic.pidManager.settings.title" list="true"}

    {fbvElement
    type="checkbox"
    id="{APP\plugins\generic\pidManager\classes\Constants::settingEnableIgsn}"
    checked=${APP\plugins\generic\pidManager\classes\Constants::settingEnableIgsn}
    label="plugins.generic.pidManager.settings.igsn.label"}

    {fbvElement
    type="checkbox"
    id="{APP\plugins\generic\pidManager\classes\Constants::settingEnablePidinst}"
    checked=${APP\plugins\generic\pidManager\classes\Constants::settingEnablePidinst}
    label="plugins.generic.pidManager.settings.pidinst.label"}

    {/fbvFormSection}

    {/fbvFormArea}

    {fbvFormButtons submitText="common.save"}
</form>
