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

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DashboardAction
{

    private $twig;

    public function __construct(\Twig_Environment $twig){
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $template = $this->twig->loadTemplate('IdkLegoBundle:Layout:dashboard.html.twig');
        return new Response($template->render([]));
    }

}