<?php

declare(strict_types=1);

namespace App;

use Jenssegers\Blade\Blade;

/**
 * Class Message
 *
 * @package App
 */
abstract class Message
{
    /**
     * @var bool
     */
    public $stop = false;

    /**
     * @param       $view
     * @param array $variables
     * @return mixed
     */
    public function view($view, $variables = [])
    {
        $blade = new Blade(__DIR__.'/../templates', __DIR__.'/../cache');

        return $blade->make($view, $variables)->render();
    }

    /**
     * @param $issue
     * @param $comments
     * @return bool
     */
    abstract public function check($issue,$comments): bool;

    /**
     * @param $issue
     * @param $comments
     * @return mixed
     */
    abstract public function action($issue,$comments);

}