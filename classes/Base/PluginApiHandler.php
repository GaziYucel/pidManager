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
use APP\plugins\generic\pidManager\classes\Constants;
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

    public function __construct(PidManagerPlugin $plugin)
    {
        $this->plugin = &$plugin;
    }

    /**
     * This allows to add a route on the fly without defining an api controller.
     */
    public function addRoute(string $hookName, PKPBaseController $apiController, APIHandler $apiHandler): bool
    {
        $apiHandler->addRoute(
            'POST',
            str_replace('{fieldName}', $this->fieldName, Constants::apiRoute),
            function (IlluminateRequest $illuminateRequest): JsonResponse {
                return $this->execute($illuminateRequest);
            },
            'pidManager.edit.' . $this->fieldName,
            [
                Role::ROLE_ID_SITE_ADMIN,
                Role::ROLE_ID_MANAGER,
                Role::ROLE_ID_SUB_EDITOR,
                Role::ROLE_ID_ASSISTANT,
                Role::ROLE_ID_AUTHOR,
            ]
        );

        return Hook::CONTINUE;
    }

    private function execute(IlluminateRequest $illuminateRequest): JsonResponse
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

        if($publication->getData('status') === Submission::STATUS_PUBLISHED){
            return response()->json([
                'error' => __('common.error'),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $publication->setData($this->fieldName, json_encode($params));
        Repo::publication()->edit($publication, []);

        $publication = Repo::publication()->get((int)$publication->getId());

        return response()->json(
            $publication->_data,
            Response::HTTP_OK);
    }
}