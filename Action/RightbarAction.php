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

use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\Tag\RightBarChain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RightbarAction
{

    private $chain;
    private $twig;
    
    public function __construct(RightBarChain $chain, \Twig_Environment $twig)
    {
        $this->chain = $chain;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $rightbar= $this->chain->get($request->get('rightbar'));
        $template = $this->twig->loadTemplate($rightbar->getTemplate());
        return new Response($template->render($rightbar->getParameters()));
    }

}
