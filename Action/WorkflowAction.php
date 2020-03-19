<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Exception\LogicException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class WorkflowAction extends AbstractAction
{
    private $workflows;

    public function __construct(ConfiguratorBuilder $configuratorBuilder, Registry $workflows) {
        parent::__construct($configuratorBuilder);
        $this->workflows = $workflows;
    }

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $em = $this->mem->getEntityManager();
        $entity = $em->getRepository($configurator->getRepositoryName())->find($request->get('id'));
        $transition = $request->query->get('transition');
        if(!$entity){
            throw new NotFoundHttpException('not found');
        }
        $workflow = $this->workflows->get($entity);

        try {
            $workflow->apply($entity, $transition);
            $em->flush();
        } catch (LogicException $exception) {
            return new JsonResponse(['status' => 'nok', 'error' => $exception->getMessage()]);
        }
        return new JsonResponse(['status' => 'ok', 'message' => $this->trans('lgo.action.workflow.success')]);
    }
}
