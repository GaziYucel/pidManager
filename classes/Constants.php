<?php

/**
 * @file plugins/generic/pidManager/classes/Constants.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Constants
 *
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Constants
 */

namespace APP\plugins\generic\pidManager\classes;

class Constants
{
    /** @var string Whether IGSN feature is enabled. */
    public const string settingEnableIgsn = 'PidManager_Igsn';

    /** @var string Whether PIDINST feature is enabled. */
    public const string settingEnablePidinst = 'PidManager_Pidinst';

    /** @var string Correct prefix for DOI, e.g. https://doi.org */
    public const string doiPrefix = 'https://doi.org';

    /** @var string Key for IGSN saved in publications */
    public const string igsn = 'igsn';

    /** @var string Key for PIDINST saved in publications */
    public const string pidinst = 'pidinst';
}
