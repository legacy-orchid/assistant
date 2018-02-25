<?php

declare(strict_types=1);

namespace App\Actions;

use App\Message;

class ByeAction extends Message
{
    /**
     * @param $issue
     * @param $comments
     * @return bool
     */
    public function check($issue, $comments): bool
    {
        return true;
    }

    /**
     * @param $issue
     * @param $comments
     * @return mixed
     */
    public function action($issue, $comments)
    {
        return $this->view('footer');
    }

}