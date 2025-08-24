<?php
/**
 * @file classes/Settings/Actions.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Actions
 * @brief Actions on the settings page
 */

namespace APP\plugins\generic\pidManager\classes\Settings;

use PidManagerPlugin;
use LinkAction;
use AjaxAction;

class Actions
{
  /** @var PidManagerPlugin */
  public PidManagerPlugin $plugin;

  /** @param PidManagerPlugin $plugin */
  public function __construct(PidManagerPlugin &$plugin)
  {
    $this->plugin = &$plugin;
  }

  /** @copydoc Plugin::getActions() */
  public function execute($request, $actionArgs, $parentActions): array
  {
    if (!$this->plugin->getEnabled()) return $parentActions;

    $router = $request->getRouter();

    $linkAction[] = new LinkAction(
      'initialise',
      new AjaxAction(
        $router->url(
          $request, null, null, 'manage', null,
          [
            'verb' => 'initialise',
            'plugin' => $this->plugin->getName(),
            'category' => 'generic'
          ]
        )
      ),
      __('plugins.generic.pidManager.settings.initialise.button'),
      null);

    array_unshift($parentActions, ...$linkAction);

    return $parentActions;
  }
}
