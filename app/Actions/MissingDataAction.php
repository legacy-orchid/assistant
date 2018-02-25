<?php

declare(strict_types=1);

namespace App\Actions;

use App\Message;

class MissingDataAction extends Message
{

    /**
     * @var array
     */
    public $specifications = [
        'ORCHID Version'   => true,
        'Laravel Version'  => true,
        'PHP Version'      => true,
        'Database Version' => true
    ];

    /**
     * @param $issue
     * @param $comments
     * @return bool
     */
    public function check($issue, $comments): bool
    {
        $validate = false;

        foreach ($this->specifications as $key => $specification){

            if (strpos($issue['body'], $key) !== false) {
               $this->specifications[$key] =  false;
            }else{
                $validate = true;
            }
        }

        return $validate;
    }

    /**
     * @param $issue
     * @param $comments
     * @return mixed
     */
    public function action($issue, $comments)
    {
        $specifications = array_filter($this->specifications, function($var){
            return $var;
        });

        $specifications = array_keys($specifications);

        return $this->view('missing_data',[
            'missing_sections' => $specifications
        ]);
    }

}