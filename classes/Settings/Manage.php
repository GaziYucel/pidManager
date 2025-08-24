<?php
/**
 * @file classes/Settings/Manage.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Manage
 * @brief Manage settings page
 */

namespace APP\plugins\generic\pidManager\classes\Settings;

use Application;
use NotificationManager;
use APP\plugins\generic\pidManager\classes\Igsn\IgsnSchemaMigration;
use PidManagerPlugin;
use JSONMessage;

class Manage
{
  /** @var PidManagerPlugin */
  public PidManagerPlugin $plugin;

  /** @param PidManagerPlugin $plugin */
  public function __construct(PidManagerPlugin &$plugin)
  {
    $this->plugin = &$plugin;
  }

  /** @copydoc Plugin::manage() */
  public function execute($args, $request): JSONMessage
  {
    $json = new JSONMessage(false);

    switch ($request->getUserVar('verb')) {
      case 'initialise':
        $igsnSchemaMigration = new IgsnSchemaMigration();
        $igsnSchemaMigration->up();

        $notificationManager = new NotificationManager();
        $notificationManager->createTrivialNotification(
          Application::get()->getRequest()->getUser()->getId(),
          NOTIFICATION_TYPE_SUCCESS,
          array('contents' => __('plugins.generic.pidManager.settings.initialise.notification')));

        $json->setStatus(true);
        $json->setEvent('dataChanged');
    }

    return $json;
  }
}
