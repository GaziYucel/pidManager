<?php

/**
 * @file classes/Base/ArticleDetails.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ArticleDetails
 * @ingroup plugins_generic_pidmanager
 *
 * @brief ArticleDetails
 */

namespace APP\plugins\generic\pidManager\classes\Base;

use APP\plugins\generic\pidManager\classes\PluginRepo;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\template\TemplateManager;

abstract class ArticleDetails
{
    public PidManagerPlugin $plugin;
    public string $fieldName = '';
    public object $dataModel;

    public function __construct(PidManagerPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function execute(string $hookName, array $args): bool
    {
        /* @var TemplateManager $templateMgr */
        $templateMgr = &$args[1];

        $items = PluginRepo::getPidsByPublication(
            $templateMgr->getTemplateVars('currentPublication'),
            $this->fieldName,
            $this->dataModel
        );

        $templateParameters = [
            'dataModel' => json_encode(get_class_vars(get_class($this->dataModel))),
            'items' => $items,
            'itemsJson' => json_encode($items)
        ];
        $templateMgr->assign($templateParameters);
        $templateMgr->display($this->plugin->getTemplateResource(
            $this->fieldName . '/articleDetails.tpl'));

        return false;
    }
}
