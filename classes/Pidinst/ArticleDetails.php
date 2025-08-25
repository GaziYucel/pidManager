<?php

/**
 * @file classes/Pidinst/ArticleDetails.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ArticleDetails
 * @brief Article page view
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Constants;
use PidManagerPlugin;
use TemplateManager;

class ArticleDetails
{
    /** @var PidManagerPlugin */
    public PidManagerPlugin $plugin;

    /** @param PidManagerPlugin $plugin */
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    /**
     * Hook callback: register output filter to replace raw with structured citations.
     *
     * @param string $hookName
     * @param array $args
     * @return bool
     */
    public function execute(string $hookName, array $args): bool
    {
        /* @var TemplateManager $templateMgr */
        $templateMgr = &$args[1];

        $repo = new Repo();
        $items = $repo->getByPublication($templateMgr->getTemplateVars('currentPublication'));

        for ($i = 0; $i < count($items); $i++) {
            $items[$i]->doi =
                '<a href="' . Constants::doiPrefix . '/' . $items[$i]->doi . '" target="_blank">' .
                Constants::doiPrefix . '/' . $items[$i]->doi .
                '</a>';
        }

        $templateParameters = ['items' => $items];
        $templateMgr->assign($templateParameters);
        $templateMgr->display($this->plugin->getTemplateResource("pidinst/articleDetails.tpl"));

        return false;
    }
}
