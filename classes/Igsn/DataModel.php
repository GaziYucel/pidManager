<?php

/**
 * @file classes/Igsn/DataModel.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DataModel
 * @brief Igsn data model.
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

class DataModel
{
    /** @var string|null The DOI) for the sample. */
    public ?string $doi = null;

    /** @var string|null The label of the sample. */
    public ?string $label = null;
}
