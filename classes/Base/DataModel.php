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

abstract class DataModel
{
    /** @var string The DOI) for the sample. */
    public string $doi = '';

    /** @var string The label of the sample. */
    public string $label = '';

    /** @var string Creator(s) of the sample. */
    public string $creators = '';

    /** @var string Publisher of the sample. */
    public string $publisher = '';

    /** @var string Publication year of the sample. */
    public string $publicationYear = '';
}
