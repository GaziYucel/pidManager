<?php

/**
 * @file classes/Repo.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Repo
 * @ingroup plugins_generic_pidmanager
 *
 * @brief Repo
 */

namespace APP\plugins\generic\pidManager\classes;

use Publication;

class Repo
{
    public string $fieldName = '';
    public object $dataModel;

    public function __construct(string $fieldName, object $dataModel)
    {
        $this->fieldName = $fieldName;
        $this->dataModel = $dataModel;
    }

    /**
     * This method retrieves the pids for a publication and returns an array of pid DataModels.
     * If no pids are found, the method returns an empty array.
     */
    public function getPidsByPublication(Publication|null $publication): array
    {
        if (empty($publication)) return [];

        $itemsIn = json_decode($publication->getData($this->fieldName), true);

        if (empty($itemsIn) || json_last_error() !== JSON_ERROR_NONE) return [];

        $itemsOut = [];
        $properties = get_class_vars(get_class(new $this->dataModel()));

        foreach ($itemsIn as $item) {
            $dataModel = new $this->dataModel();
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
