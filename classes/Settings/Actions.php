<?php

/**
 * @file classes/Settings/Actions.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Actions
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Actions on the settings page
 */

namespace APP\plugins\generic\pidManager\classes\Settings;

use APP\plugins\generic\pidManager\PidManagerPlugin;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;

class Actions
{
    public PidManagerPlugin $plugin;

    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->plugin = &$plugin;
    }

    public function execute($request, $actionArgs, $parentActions): array
    {
        if (!$this->plugin->getEnabled()) {
            return $parentActions;
        }

        $router = $request->getRouter();

        $linkAction[] = new LinkAction(
            'settings',
            new AjaxModal(
                $router->url(
                    $request,
                    null,
                    null,
                    'manage',
                    null,
                    [
                        'verb' => 'settings',
                        'plugin' => $this->plugin->getName(),
                        'category' => 'generic'
                    ]
                ),
                $this->plugin->getDisplayName()
            ),
            __('manager.plugins.settings'),
            null
        );

        array_unshift($parentActions, ...$linkAction);

        return $parentActions;
    }
}
