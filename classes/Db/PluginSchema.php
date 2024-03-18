<?php
/**
 * @file classes/Db/PluginSchema.php
 *
 * @copyright (c) 2021+ TIB Hannover
 * @copyright (c) 2021+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PluginSchema
 * @brief Plugin Schema
 */
namespace APP\plugins\generic\pidManager\classes\Db;

use APP\plugins\generic\pidManager\PidManagerPlugin;

class PluginSchema
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

        foreach(PidManagerPlugin::PID_MANAGER_PIDs_PUBLICATION as $pid){
            $schema->properties->{$pid} = (object)[
                'type' => 'string',
                'multilingual' => false,
                'apiSummary' => true,
                'validation' => ['nullable']
            ];
        }

        return false;
    }

    /**
     * This method adds properties to the schema of a journal / context.
     *
     * @param string $hookName
     * @param array $args
     * @return bool
     */
    public function addToSchemaContext(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        foreach(PidManagerPlugin::PID_MANAGER_PIDs_JOURNAL as $pid){
            $schema->properties->{$pid} = (object)[
                'type' => 'string',
                'multilingual' => false,
                'apiSummary' => true,
                'validation' => ['nullable']
            ];
        }

        return false;
    }
}
