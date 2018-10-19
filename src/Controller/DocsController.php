<?php

namespace App\Controller;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;

class DocsController extends AbstractController
{
    public function index(MarkdownParserInterface $parser, ContainerBagInterface $params)
    {
        $readme_path = $params->get('kernel.project_dir').DIRECTORY_SEPARATOR.'README.md';
        $text = file_get_contents($readme_path);
        return new Response($parser->transformMarkdown($text));
    }

}

