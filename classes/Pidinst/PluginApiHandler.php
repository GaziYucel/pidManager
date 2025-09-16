<?php

/**
 * @file classes/Pidinst/PluginApiHandler.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PluginApiHandler
 * @ingroup plugins_generic_pidmanager
 *
 * @brief PluginApiHandler
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Base\PluginApiHandler as BasePluginApiHandler;
use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\PidManagerPlugin;

class PluginApiHandler extends BasePluginApiHandler
{
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->fieldName = Constants::pidinst;
        $this->dataModel = new DataModel();
        parent::__construct($plugin);
    }
}