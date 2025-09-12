<?php

/**
 * @file classes/PluginRepo.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PluginRepo
 * @ingroup plugins_generic_pidmanager
 *
 * @brief PluginRepo
 */

namespace APP\plugins\generic\pidManager\classes;

use APP\publication\Publication;

class PluginRepo
{
    /**
     * This method retrieves the pids for a publication and returns an array of pid DataModels.
     * If no pids are found, the method returns an empty array.
     */
    public static function getPidsByPublication(Publication $publication, string $fieldName, object $dataModel): array
    {
        $itemsIn = json_decode($publication->getData($fieldName), true);

        if (empty($itemsIn) || json_last_error() !== JSON_ERROR_NONE) return [];

        $itemsOut = [];
        $properties = get_class_vars(get_class(new $dataModel()));

        foreach ($itemsIn as $item) {
            $dataModel = new $dataModel();
            foreach ($properties as $key => $value) {
                if (!empty($item[$key])) {
                    $dataModel->{$key} = $item[$key];
                }
            }
            $itemsOut[] = $dataModel;
        }

        return $itemsOut;
    }
}
