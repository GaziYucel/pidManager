<?php

/**
 * @file classes/Igsn/IgsnArticleDetails.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnArticleDetails
 * @brief Article page view
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use APP\plugins\generic\pidManager\classes\Constants;
use PidManagerPlugin;
use TemplateManager;

class IgsnArticleDetails
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

        $igsnRepo = new IgsnRepo();
        $igsns = $igsnRepo->getIgsns($templateMgr->getTemplateVars('currentPublication'));

        for ($i = 0; $i < count($igsns); $i++) {
            $igsns[$i]->doi = '<a href="' . Constants::doiPrefix . '/' . $igsns[$i]->doi . '" target="_blank">' . $igsns[$i]->doi . '</a>';
        }

        $templateParameters = ['igsns' => $igsns];
        $templateMgr->assign($templateParameters);
        $templateMgr->display($this->plugin->getTemplateResource("igsn/igsnArticleDetails.tpl"));

        return false;
    }
}
