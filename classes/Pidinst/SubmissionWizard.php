<?php

/**
 * @file classes/Pidinst/SubmissionWizard.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionWizard
 * @ingroup plugins_generic_pidmanager
 *
 * @brief SubmissionWizard
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Base\SubmissionWizard as BaseSubmissionWizard;
use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\PidManagerPlugin;

class SubmissionWizard extends BaseSubmissionWizard
{
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->fieldName = Constants::pidinst;
        parent::__construct($plugin);
    }
}
