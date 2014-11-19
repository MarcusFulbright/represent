<?php

namespace Represent\Generator;

use Represent\Annotations\Link;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LinkGenerator 
{
    private $urlGenerator;

    private $language;

    public function __construct(SymfonyUrlGeneratorInterface $urlGenerator, ExpressionLanguage $language)
    {
        $this->urlGenerator = $urlGenerator;
        $this->language     = $language;
    }

    public function generate(Link $link)
    {
        $link = $this->evaluateExpressions($link);

        return $this->urlGenerator->generate($link->name, $link->parameters, $link->absolute);
    }

    private function evaluateExpressions(Link $link)
    {
        foreach ($link->parameters as $param) {
            $matches = array();
            preg_match("expr('(.+)'", $param, $matches);

            if (!empty($matches)) {
                $link->$param = $this->language->evaluate($matches[0]);
            }
        }

        return $link;
    }
}