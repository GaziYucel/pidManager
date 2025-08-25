<?php

/**
 * @file classes/Constants.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Constants
 * @brief Constants
 */

namespace APP\plugins\generic\pidManager\classes;

class Constants
{
    /** @var string Correct prefix for DOI, e.g. https://doi.org */
    public const doiPrefix = 'https://doi.org';

    /** @var string Key for igsn saved in publications */
    public const igsn = 'igsn';
}
