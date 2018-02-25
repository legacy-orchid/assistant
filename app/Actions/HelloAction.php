<?php

declare(strict_types=1);

namespace App\Actions;

use App\Message;

class HelloAction extends Message
{
    /**
     * @param $issue
     * @param $comments
     * @return bool
     */
    public function check($issue, $comments): bool
    {
        if (count($comments) === 0) {
            return true;
        }

        $botname = env('GITHUB_LOGIN');

        foreach ($comments as $comment) {
            if ($comment['user']['login'] == $botname) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $issue
     * @param $comments
     * @return mixed
     */
    public function action($issue, $comments)
    {
        return $this->view('header', [
            'submitter' => $issue['user']['login'],
        ]);
    }
}
