<?php

/**
 * @file classes/Pidinst/ArticleDetails.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ArticleDetails
 * @ingroup plugins_generic_pidmanager
 *
 * @brief ArticleDetails
 */

namespace APP\plugins\generic\pidManager\classes\Pidinst;

use APP\plugins\generic\pidManager\classes\Base\ArticleDetails as BaseArticleDetails;
use APP\plugins\generic\pidManager\classes\Constants;
use APP\plugins\generic\pidManager\PidManagerPlugin;

class ArticleDetails extends BaseArticleDetails
{
    public function __construct(PidManagerPlugin &$plugin)
    {
        $this->fieldName = Constants::pidinst;
        $this->dataModel = new DataModel();
        parent::__construct($plugin);
    }
}
