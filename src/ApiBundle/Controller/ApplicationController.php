<?php

namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class ApplicationController
 */
class ApplicationController extends BaseController
{

    /**
     * @ApiDoc(
     *  description="Return application status",
     * )
     * @return Response
     */
    public function statusAction()
    {
        return JsonResponse::create([
            'status' => 'OK'
        ]);
    }
}
