<?php

/**
 * @file classes/Pidinst/Schema.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Schema
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Schema
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Base\Schema as BaseSchema;
use APP\plugins\generic\pidManager\classes\Constants;

class Schema extends BaseSchema
{
    public function __construct()
    {
        $this->fieldName = Constants::pidinst;
    }
}
