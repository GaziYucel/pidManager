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

use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\classes\Repo;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\template\TemplateManager;

class ArticleDetails
{
    public PidManagerPlugin $plugin;
    public string $fieldName = '';
    public object $dataModel;

    public function __construct(PidManagerPlugin &$plugin, string $fieldName, object $dataModel)
    {
        $this->plugin = &$plugin;
        $this->fieldName = $fieldName;
        $this->dataModel = $dataModel;
    }

    public function execute(string $hookName, array $args): bool
    {
        /* @var TemplateManager $templateMgr */
        $templateMgr = &$args[1];

        $repo = new Repo($this->fieldName, $this->dataModel);
        $items = $repo->getPidsByPublication($templateMgr->getTemplateVars('currentPublication'));

        for ($i = 0; $i < count($items); $i++) {
            $items[$i]->doi =
                '<a href="' . Constants::doiPrefix . '/' . $items[$i]->doi . '" target="_blank">' .
                Constants::doiPrefix . '/' . $items[$i]->doi .
                '</a>';
        }

        $templateParameters = [
            'items' => $items,
            'pidName' => $this->fieldName
        ];
        $templateMgr->assign($templateParameters);
        $templateMgr->display($this->plugin->getTemplateResource(
            $this->fieldName . '/articleDetails.tpl'));

        return false;
    }
}
