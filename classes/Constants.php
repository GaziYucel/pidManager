<?php

/**
 * @file classes/Constants.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Constants
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Constants
 */

namespace APP\plugins\generic\pidManager\classes;

class Constants
{
    /** @var string Whether IGSN feature is enabled. */
    public const settingEnableIgsn = 'PidManager_Igsn';

    /** @var string Whether PIDINST feature is enabled. */
    public const settingEnablePidinst = 'PidManager_Pidinst';

    /** @var string Correct prefix for DOI, e.g. https://doi.org */
    public const doiPrefix = 'https://doi.org';

    /** @var string Key for IGSN saved in publications */
    public const igsn = 'igsn';

    /** @var string Key for PIDINST saved in publications */
    public const pidinst = 'pidinst';

    /** @var string Api endpoint used by this plugin (hook: APIHandler::endpoints::submissions), e.g. api/v1/submissions/pidManager/1/igsn */
    public const apiRoute = 'pidManager/{publication_id}/{fieldName}';
}
