<?php

declare(strict_types=1);

namespace wax\dev;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{

    static ?Main $instance=null;

    public static function getInstance () : Main
    {
        return self ::$instance;
    }

    protected function onLoad () : void
    {
        self ::$instance=$this;
    }

    public function onEnable () : void
    {
        $this -> getServer () -> getPluginManager () -> registerEvents ( new hammer() , $this );
        $this -> getLogger () -> info ( "plugin active" );
    }

    protected function onDisable(): void {
        self::$instance = null;
    }
}