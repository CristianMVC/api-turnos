<?php

namespace SNT\Infraestructure\Services\CommonMark;

use League\CommonMark\Environment;
use SNT\Domain\Services\Parser;
use League\CommonMark\DocParser;
use League\CommonMark\HtmlRenderer;

class CommonMarkParser implements Parser
{
    /**
     * CommonMarkParser constructor.
     * @param SNTCommonMarkParserEnvironment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->parser = new DocParser($environment);
        $this->renderer = new HtmlRenderer($environment);
    }
    
    public function render($commonMark)
    {
        $document = $this->parser->parse($commonMark);
        return $this->renderer->renderBlock($document);
    }
}