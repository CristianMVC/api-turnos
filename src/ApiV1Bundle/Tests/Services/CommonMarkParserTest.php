<?php

namespace ApiV1Bundle\Tests\Services;


use League\CommonMark\Extension\CommonMarkCoreExtension;
use SNT\Infraestructure\Services\CommonMark\CommonMarkParser;
use SNT\Infraestructure\Services\CommonMark\SNTCommonMarkParserEnvironment;

class CommonMarkParserTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var
     */
    private $service;
    
    public function setUp()
    {
        $extension = new CommonMarkCoreExtension();
        $environment = new SNTCommonMarkParserEnvironment($extension);
        $this->service = new CommonMarkParser($environment);
    }
    
    public function testRenderStrongMarkdownAsHtml()
    {
        $text = $this->service->render('Texto **strong**');
    
        $this->assertEquals('<p>Texto <strong>strong</strong></p>', rtrim($text));
    }
    
    public function testRenderEmphasizedMarkdownAsHtml()
    {
        $text = $this->service->render('Texto *Emphasized*');
        
        $this->assertEquals('<p>Texto <em>Emphasized</em></p>', rtrim($text));
    }
    
    public function testRenderLinkMarkdownAsHtml()
    {
        $text = $this->service->render('[Link](https://www.google.com)');
        
        $this->assertEquals('<p><a href="https://www.google.com">Link</a></p>', rtrim($text));
    }
    
    public function testRenderOrderedListsMarkdownAsHtml()
    {
        $text = $this->service->render('1. Item');
        
        $expected = '<ol>' . PHP_EOL . '<li>Item</li>' . PHP_EOL . '</ol>';
        
        $this->assertEquals($expected, rtrim($text));
    }
    
    public function testRenderUnorderedListsMarkdownAsHtml()
    {
        $text = $this->service->render('* Item');
        
        $expected = '<ul>' . PHP_EOL . '<li>Item</li>' . PHP_EOL . '</ul>';
        
        $this->assertEquals($expected, rtrim($text));
    }
    
}