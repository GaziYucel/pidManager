<?php
/**
 * @file classes/Igsn/IgsnArticleView.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnArticleView
 * @brief Article page view
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use APP\plugins\generic\pidManager\classes\Helpers\PID\Doi;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\template\TemplateManager;

class IgsnArticleView
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

        $igsnDao = new IgsnDao();
        $igsnS = $igsnDao->getIgsns($templateMgr->getTemplateVars('currentPublication'));

        for ($i = 0; $i < count($igsnS); $i++) {
            $id = Doi::removePrefix($igsnS[$i]->id);
            $prefix = Doi::prefix;
            $igsnS[$i]->id = "<a href='$prefix/$id' target='_blank'>$id</a>";
        }

        $templateParameters = ['igsnS' => $igsnS];
        $templateMgr->assign($templateParameters);
        $templateMgr->display($this->plugin->getTemplateResource("igsnArticleView.tpl"));

        return false;
    }
}
