<?php

declare(strict_types=1);

namespace App\Actions;

use App\App;
use App\Message;

class DocsAction extends Message
{
    /**
     * @var
     */
    public $result;

    /**
     * @param $issue
     * @param $comments
     * @return bool
     */
    public function check($issue, $comments): bool
    {
        $app = new App();

        $title = $issue['title'];
        $title = $this->stopWords($title, file(__DIR__.'/../stop_words'));

        $owner = env('GITHUB_OWNER');
        $repo = env('GITHUB_REPO');

        $response = $app->client->request('GET',
            '/search/issues', [
                'query' => [
                    'q'             => $title."+repo:{$owner}/{$repo}",
                ],
            ]);
        $this->result = json_decode($response->getBody()->getContents(), true);

        if ($this->result['total_count'] > 1) {
            return true;
        }

        return false;
    }

    /**
     * @param $issue
     * @param $comments
     * @return mixed
     */
    public function action($issue, $comments)
    {
        $items = array_chunk($this->result['items'], 6);
        unset($items[0][0]);

        return $this->view('related_issue', [
            'issues' => $items[0],
        ]);
    }

    /**
     * @param $text
     * @param $stopwords
     * @return string
     */
    private function stopWords($text, $stopwords)
    {
        // Remove line breaks and spaces from stopwords
        $stopwords = array_map(function ($x) {
            return trim(strtolower($x));
        }, $stopwords);
        // Replace all non-word chars with comma
        $pattern = '/[0-9\W]/';
        $text = preg_replace($pattern, ',', $text);
        // Create an array from $text
        $text_array = explode(',', $text);
        // remove whitespace and lowercase words in $text
        $text_array = array_map(function ($x) {
            return trim(strtolower($x));
        }, $text_array);
        foreach ($text_array as $term) {
            if (! in_array($term, $stopwords)) {
                $keywords[] = $term;
            }
        }

        $keywords = array_filter($keywords ?? []);

        return implode(' ', $keywords);
    }
}
