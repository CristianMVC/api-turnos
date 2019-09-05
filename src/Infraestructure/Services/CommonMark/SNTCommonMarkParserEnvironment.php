<?php

namespace SNT\Infraestructure\Services\CommonMark;

use League\CommonMark\Environment;
use League\CommonMark\Extension\Extension;

class SNTCommonMarkParserEnvironment extends Environment
{
    public function __construct(
        Extension $extension,
        $config = [
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break'      => "\n",
            ],
            'safe'               => false, // deprecated option
            'html_input'         => 'allow',
            'allow_unsafe_links' => true,
            'max_nesting_level'  => INF,
        ]
    ) {
        
        parent::__construct();
        
        $this->addExtension($extension);
    
        $this->mergeConfig($config);
    }
}