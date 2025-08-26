<?php

/**
 * @file classes/Base/DataModel.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DataModel
 * @ingroup plugins_generic_pidmanager
 *
 * @brief DataModel
 */

namespace APP\plugins\generic\pidManager\classes\Base;

class DataModel
{
    /** @var string|null The DOI) for the sample. */
    public ?string $doi = null;

    /** @var string|null The label of the sample. */
    public ?string $label = null;
}
