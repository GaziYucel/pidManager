<?php

/**
 * @file classes/Igsn/IgsnSubmissionWizard.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IgsnSubmissionWizard
 * @brief Igsn submission wizard
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

use PidManagerPlugin;

class IgsnSubmissionWizard
{
  /**@var PidManagerPlugin */
  public PidManagerPlugin $plugin;

  /** @param PidManagerPlugin $plugin */
  public function __construct(PidManagerPlugin &$plugin)
  {
    $this->plugin = &$plugin;
  }

  public function execute(string $hookName, array $args): void
  {
    $templateMgr = &$args[1];

    $request = $this->plugin->getRequest();

    $templateParameters = [
      'assetsUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/assets'
    ];
    $templateMgr->assign($templateParameters);

    $templateMgr->display(
      $this->plugin->getTemplateResource("igsn/igsnSubmissionWizard.tpl")
    );
  }
}
