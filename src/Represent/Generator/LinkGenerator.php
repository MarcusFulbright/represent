<?php

namespace Represent\Generator;

use Represent\Annotations\Link;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LinkGenerator 
{
    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Symfony\Component\ExpressionLanguage\ExpressionLanguage
     */
    private $language;

    /**
     * @param SymfonyUrlGeneratorInterface $urlGenerator
     * @param ExpressionLanguage           $language
     */
    public function __construct(SymfonyUrlGeneratorInterface $urlGenerator, ExpressionLanguage $language)
    {
        $this->urlGenerator = $urlGenerator;
        $this->language     = $language;
    }

    /**
     * @param Link $link
     * @return string
     */
    public function generate(Link $link)
    {
        $link = $this->parseParams($link);

        return $this->urlGenerator->generate($link->name, $link->parameters, $link->absolute);
    }

    /**
     * @param Link $link
     * @return string
     */
    public function parseName(Link $link)
    {
        return $this->evaluateExpression($link->name);
    }

    /**
     * @param Link $link
     * @return Link
     */
    public function parseParams(Link $link)
    {
        foreach ($link->parameters as $key => $value) {
            $link->parameters[$key] = $this->evaluateExpression($value);
        }

        return $link;
    }

    /**
     * @param       $haystack
     * @param array $matches
     * @return string
     */
    private function evaluateExpression($haystack, array $matches = array())
    {
        preg_match("/\'(.*)\'/", $haystack, $matches);

        if (!empty($matches)) {
            $output = $this->language->evaluate($matches[0]);
        } else {
            $output = $haystack;
        }

        return $output;
    }
}