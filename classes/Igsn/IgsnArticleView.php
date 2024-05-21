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

use APP\plugins\generic\pidManager\classes\PID\Doi;
use APP\plugins\generic\pidManager\classes\PID\Handle;
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

        $url = "<a href='{prefix}/{id}' target='_blank'>{id}</a>";

        for ($i = 0; $i < count($igsnS); $i++) {
            $id = $igsnS[$i]->id;
            if (!empty(Doi::extractFromString($id))) {
                $id = Doi::removePrefix($id);
                $id = str_replace(['{prefix}', '{id}'], [Doi::prefix, $id], $url);
            } else if (!empty(Handle::extractFromString($id))) {
                $id = Handle::removePrefix($id);
                $id = str_replace(['{prefix}', '{id}'], [Handle::prefix, $id], $url);
            }
            $igsnS[$i]->id = $id;
        }

        $templateParameters = ['igsnS' => $igsnS];
        $templateMgr->assign($templateParameters);
        $templateMgr->display($this->plugin->getTemplateResource("igsnArticleView.tpl"));

        return false;
    }
}
