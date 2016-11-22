<?php

namespace ApiBundle\Controller;

use ApiBundle\Form\Type\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use WorkLoggerBundle\Entity\Project;

class ProjectController extends BaseController
{
    /**
     * @ApiDoc(
     *  description="Return all projects"
     * )
     * @return Response
     */
    public function indexAction()
    {
        $projectRepository = $this->getRepository(Project::class);
        $projects = $projectRepository->findAll();

        return $this->success($projects, 'Project', Response::HTTP_OK, array('PROJECT_LIST'));
    }

    /**
     * @ApiDoc(
     *  description="Return single project"
     * )
     * @param Project $project
     * @return Response
     * @ParamConverter("project", class="WorkLoggerBundle\Entity\Project", options={"id" = "id"})
     */
    public function showAction(Project $project)
    {
        return $this->success($project, 'Project', Response::HTTP_OK, array('PROJECT_DETAILS', 'RECORD_LIST'));
    }

    /**
     * @ApiDoc(
     *  description="Create new project",
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="Project name"},
     *      {"name"="description", "dataType"="string", "required"=true, "description"="Project description"}
     *  })
     * )
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(ProjectType::class);
        $submittedData = json_decode($request->getContent(), true);
        $form->submit($submittedData);

        if (!$form->isValid()) {
            return $this->error($this->getFormErrorsAsArray($form));
        }

        $project = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();

        return $this->success($project, 'Project', Response::HTTP_CREATED, array('PROJECT_DETAILS'));
    }

    /**
     * @ApiDoc(
     *  description="Update project",
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="Project name"},
     *      {"name"="description", "dataType"="string", "required"=true, "description"="Project description"}
     *  })
     * )
     * @param Request $request
     * @param Project $project
     * @return Response
     * @ParamConverter("project", class="WorkLoggerBundle\Entity\Project", options={"id" = "id"})
     */
    public function updateAction(Request $request, Project $project)
    {
        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($formData);

        if (!$form->isValid()) {
            return $this->error($this->getFormErrorsAsArray($form));
        }
        $project = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();

        return $this->success($project, 'Project', Response::HTTP_OK, array('PROJECT_DETAILS'));
    }

    /**
     * @ApiDoc(
     *  description="Delete project"
     * )
     * @param Project $project
     * @return Response
     * @ParamConverter("project", class="WorkLoggerBundle\Entity\Project", options={"id" = "id"})
     */
    public function deleteAction(Project $project)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();

        return $this->success(array('status' => 'Removed', 'message' => 'Project properly removed'), 'Project');
    }
}
