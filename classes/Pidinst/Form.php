<?php

/**
 * @file classes/Components/Forms/Form.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Form
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Form
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Base\Form as BaseForm;
use APP\plugins\generic\pidManager\classes\Constants;

class Form extends BaseForm
{
    public function __construct(string $id, string $method, string $action, array $locales)
    {
        $this->fieldName = Constants::pidinst;
        parent::__construct($id, $method, $action, $locales);
    }
}
