<?php

/**
 * @file classes/Pidinst/WorkflowTab.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Workflow
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Workflow
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Base\Workflow as BaseWorkflow;
use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\PidManagerPlugin;

class Workflow extends BaseWorkflow
{
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->fieldName = Constants::pidinst;
        $this->dataModel = new DataModel();
        parent::__construct($plugin);
    }

    public function getForm(string $action, array $locales): Form
    {
        return new Form(
            $this->fieldName,
            'PUT',
            $action,
            $locales
        );
    }
}
