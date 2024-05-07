<?php
/**
 * @file classes/DataModels/PluginSchema.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PluginSchema
 * @brief Plugin Schema
 */

namespace APP\plugins\generic\pidManager\classes\Igsn;

class IgsnSchema
{
    /**
     * This method adds properties to the schema of a publication.
     *
     * @param string $hookName
     * @param array $args
     * @return bool
     */
    public function addToSchemaPublication(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        return false;
    }
}
