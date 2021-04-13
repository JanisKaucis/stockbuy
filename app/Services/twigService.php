<?php
namespace App\Services;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class twigService
{
    public function __construct()
    {
        $this->loader = new FilesystemLoader('../Views');
        $this->twig = new Environment($this->loader,[
            'debug' => true
        ]);
        $this->twig->addExtension(new DebugExtension());
    }
}