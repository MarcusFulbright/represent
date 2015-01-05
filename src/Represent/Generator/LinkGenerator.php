<?php

namespace Represent\Generator;

use Represent\Annotations\Link;
use Represent\Util\PaginatedCollection;
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
     * @param      $object
     * @return string
     */
    public function generate(Link $link, $object)
    {
        $link = $this->parseParams($link, $object);

        return $this->urlGenerator->generate($this->evaluateExpression($link->uri, $object), $link->parameters, $link->absolute);
    }

    /**
     * @param Link $link
     * @param      $object
     * @return string
     */
    public function parseName(Link $link, $object)
    {
        return $this->evaluateExpression($link->name, $object);
    }

    /**
     * @param Link $link
     * @param      $object
     * @return Link
     */
    public function parseParams(Link $link, $object)
    {
        foreach ($link->parameters as $key => $value) {
            $link->parameters[$key] = $this->evaluateExpression($value, $object);
        }

        return $link;
    }

    /**
     * @param       $haystack
     * @param       $object
     * @param array $matches
     * @return string
     */
    private function evaluateExpression($haystack, $object, array $matches = array())
    {
        preg_match("/\'(.*)\'/", $haystack, $matches);

        if (!empty($matches)) {
            $output = $this->language->evaluate(
                $matches[1],
                array(
                    'object' => $object
                )
            );
        } else {
            $output = $haystack;
        }

        return $output;
    }
}
