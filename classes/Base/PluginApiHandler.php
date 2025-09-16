<?php

/**
 * @file classes/Base/PluginApiHandler.php
 *
 * @copyright (c) 2024+ TIB Hannover
 * @copyright (c) 2024+ Gazi Yücel
 * @license Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PluginApiHandler
 * @ingroup plugins_generic_pidmanager
 *
 * @brief PluginApiHandler
 */


namespace APP\plugins\generic\pidManager\classes\Base;

use APP\facades\Repo;
use APP\plugins\generic\pidManager\classes\PluginRepo;
use APP\plugins\generic\pidManager\PidManagerPlugin;
use APP\submission\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Http\Response;
use PKP\core\PKPBaseController;
use PKP\handler\APIHandler;
use PKP\plugins\Hook;
use PKP\security\Role;

abstract class PluginApiHandler
{
    public PidManagerPlugin $plugin;
    public string $fieldName = '';
    public object $dataModel;

    public array $allowedRoles = [
        Role::ROLE_ID_SITE_ADMIN,
        Role::ROLE_ID_MANAGER,
        Role::ROLE_ID_SUB_EDITOR,
        Role::ROLE_ID_ASSISTANT,
        Role::ROLE_ID_AUTHOR
    ];

    public function __construct(PidManagerPlugin $plugin)
    {
        $this->plugin = &$plugin;
    }

    /**
     * This allows to add a route on the fly without defining an api controller.
     * Hook: APIHandler::endpoints::submissions
     * e.g. api/v1/submissions/pidManager/1/igsn
     */
    public function addRoute(string $hookName, PKPBaseController $apiController, APIHandler $apiHandler): bool
    {
        $apiHandler->addRoute(
            'POST',
            "pidManager/{publication_id}/$this->fieldName",
            function (IlluminateRequest $illuminateRequest): JsonResponse {
                return $this->edit($illuminateRequest);
            },
            'pidManager.edit.' . $this->fieldName,
            $this->allowedRoles
        );

        $apiHandler->addRoute(
            'POST',
            "pidManager/{publication_id}/$this->fieldName/parseCsv",
            function (IlluminateRequest $illuminateRequest): JsonResponse {
                return $this->parseCsv($illuminateRequest);
            },
            'pidManager.parseCsv.' . $this->fieldName,
            $this->allowedRoles
        );

        return Hook::CONTINUE;
    }

    /**
     * Edit pids of a publication.
     */
    private function edit(IlluminateRequest $illuminateRequest): JsonResponse
    {
        $publication = Repo::publication()->get((int)$illuminateRequest->route('publication_id'));
        $params = $illuminateRequest->input();

        if (!$publication) {
            return response()->json([
                'error' => __('api.404.resourceNotFound'),
            ], Response::HTTP_NOT_FOUND);
        }

        if (!is_array($params)) {
            return response()->json([
                'error' => __('common.error'),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($publication->getData('status') === Submission::STATUS_PUBLISHED) {
            return response()->json([
                'error' => __('common.error'),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $publication->setData($this->fieldName, json_encode($params));
        Repo::publication()->edit($publication, []);

        $publication = Repo::publication()->get((int)$publication->getId());

        return response()->json(
            $publication->_data,
            Response::HTTP_OK
        );
    }

    /**
     * Parse pids from a csv string add to existing pids.
     */
    private function parseCsv(IlluminateRequest $illuminateRequest): JsonResponse
    {
        $publication = Repo::publication()->get((int)$illuminateRequest->route('publication_id'));
        $params = $illuminateRequest->input();

        if (!$publication) {
            return response()->json([
                'error' => __('api.404.resourceNotFound'),
            ], Response::HTTP_NOT_FOUND);
        }

        if (!is_array($params)) {
            return response()->json([
                'error' => __('common.error'),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($publication->getData('status') === Submission::STATUS_PUBLISHED) {
            return response()->json([
                'error' => __('common.error'),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $csvString = (string)$illuminateRequest->input('csvString');
        $csvLines = explode("\n", $csvString);
        $currentPids = PluginRepo::getPidsByPublication($publication, $this->fieldName, $this->dataModel);

        $rejected = '';
        $properties = get_class_vars(get_class(new $this->dataModel()));
        $modelPropCount = count($properties);

        foreach ($csvLines as $csvLine) {
            $csvLine = trim($csvLine);
            if(empty($csvLine)) {
                continue;
            }
            $csvLineParsed = str_getcsv($csvLine);
            if (count($csvLineParsed) === $modelPropCount) {
                $index = 0;
                $item = new $this->dataModel();
                foreach ($properties as $key => $value) {
                    $item->$key = trim($csvLineParsed[$index]);
                    $index++;
                }
                if (str_contains(strtolower(json_encode($currentPids)), strtolower(json_encode($item)))) {
                    $rejected .= $csvLine . PHP_EOL;
                } else {
                    $currentPids[] = $item;
                }
            } else {
                $rejected .= $csvLine . PHP_EOL;
            }
        }

        $publication->setData($this->fieldName, json_encode($currentPids));
        Repo::publication()->edit($publication, []);

        return response()->json([
            'data' => $currentPids,
            'rejected' => $rejected
        ], Response::HTTP_OK);
    }
}
