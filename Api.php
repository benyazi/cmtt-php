<?php
namespace Benyazi\CmttPhp;

use GuzzleHttp\Client;

class Api
{
    const TJOURNAL = 'tjournal.ru';
    const DTF = 'dtf.ru';
    const VC = 'vc.ru';

    const SORTING_RECENT = "recent";
    const SORTING_POPULAR = "popular";
    const SORTING_WEEK = "week";
    const SORTING_MONTH = "month";

    protected $allowedSites = [
        self::TJOURNAL,
        self::DTF,
        self::VC,
    ];

    protected $site = self::TJOURNAL;
    protected $version = 'v1.6';
    protected $endpoint = 'https://api.{SITE}/{VERSION}/';

    private $methods = [
        'getUser' => 'user/{id}',
        'commentAdd' => 'comment/add',
        'getTimeline' => 'timeline/{category}/{sorting}',
        'getTimelineByHashtag' => 'timeline/{hastag}',
        'getEntryById' => 'entry/{id}',
        'getPopularEntries' => 'entry/{id}/popular',
        'getEntryComments' => 'entry/{id}/comments/{sorting}',
        'getCommentLikes' => 'comment/likers/{id}',
        'getUserComments' => 'user/{id}/comments',
        'getUserEntries' => 'user/{id}/entries',
    ];
    private $reqMethods = [
        'commentAdd' => 'POST',
    ];
    private $client;
    private $token;

    public function __construct($site = self::TJOURNAL, $token = null)
    {
        if(!in_array($site, $this->allowedSites)) {
            throw new \Exception('');
        }
        $this->site = $site;
        $this->token = $token;
        $this->client = new Client();
    }

    /**
     * Get current api URL
     * @return string
     */
    private function getUrl()
    {
        $url = str_replace('{VERSION}', $this->version, $this->endpoint);
        return str_replace('{SITE}', $this->site, $url);
    }

    /**
     * Get Request method (POST,GET,PUT)
     * @param string $method
     * @return string
     */
    private function getRequestMethod($method)
    {
        $reqMethod = 'GET';
        if(isset($this->reqMethods[$method])) {
            $reqMethod = $this->reqMethods[$method];
        }
        return $reqMethod;
    }

    /**
     * Parse method and data
     * @param string $url
     * @param string $method
     * @param array $data
     * @return string
     */
    private function parseData($url, $method, $data)
    {
        if(isset($this->methods[$method])) {
            $url .= $this->methods[$method];
            foreach ($data as $key => $val) {
                $url = str_replace('{' . $key . '}', $val, $url);
            }
        }
        return $url;
    }

    /**
     * Send request to API
     * @param string $method
     * @param array $data
     * @param array $query
     * @return bool|mixed
     */
    public function request($method, $data=[], $query=[])
    {
        $url = $this->getUrl();
        $reqMethod = $this->getRequestMethod($method);
        $url = $this->parseData($url, $method, $data);

        $response = $this->client->request($reqMethod, $url, $query);
        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        if($statusCode == 200) {
            return json_decode($content, true);
        }
        throw new \Exception('');
    }

    /**
     * Get article list
     * @param string $category
     * @param string $sorting - "recent" "popular" "week" "month"
     * @param null|integer $count
     * @param null|integer $offset
     * @return array
     * @throws \Exception
     */
    public function getTimeline($category, $sorting = self::SORTING_RECENT, $count = null, $offset = null)
    {
        if(!in_array($sorting, [self::SORTING_RECENT, self::SORTING_POPULAR, self::SORTING_WEEK, self::SORTING_MONTH])) {
            throw new \Exception('Sorting must be "recent" or "popular" or "week" or "month"');
        }
        $query = ['query' => []];
        if($count) {
            $query['query']['count'] = $count;
        }
        if($offset) {
            $query['query']['offset'] = $offset;
        }
        $data = $this->request('getTimeline', [
            'category' => $category,
            'sorting' => $sorting
        ], $query);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Get article list by hashtag
     * @param string $hashtag
     * @param null|integer $count
     * @param null|integer $offset
     * @return array
     * @throws \Exception
     */
    public function getTimelineByHashtag($hashtag, $count = null, $offset = null)
    {
        $query = ['query' => []];
        if($count) {
            $query['query']['count'] = $count;
        }
        if($offset) {
            $query['query']['offset'] = $offset;
        }
        $data = $this->request('getTimelineByHashtag', [
            'hashtag' => $hashtag
        ], $query);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Get comments for article by ID
     * @param integer $id
     * @param string $sorting - "recent" "popular"
     * @return array
     * @throws \Exception
     */
    public function getEntryComments($id, $sorting = self::SORTING_RECENT)
    {
        if(!in_array($sorting, [self::SORTING_RECENT, self::SORTING_POPULAR])) {
            throw new \Exception('Sorting must be "recent" or "popular"');
        }
        $data = $this->request('getEntryComments', [
            'id' => $id,
            'sorting' => $sorting,
        ], []);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Получить список лайкнувших комментарий
     * @param integer $id
     * @return array
     * @throws \Exception
     */
    public function getCommentLikes($id)
    {
        $data = $this->request('getCommentLikes', [
            'id' => $id,
        ], []);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Получить комментарии пользователя
     * @param integer $id
     * @param null|integer $count
     * @param null|integer $offset
     * @return array
     * @throws \Exception
     */
    public function getUserComments($id, $count = null, $offset = null)
    {
        $query = ['query' => []];
        if($count) {
            $query['query']['count'] = $count;
        }
        if($offset) {
            $query['query']['offset'] = $offset;
        }
        $data = $this->request('getUserComments', [
            'id' => $id
        ], $query);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Получить записи пользователя
     * @param integer $id
     * @param null|integer $count
     * @param null|integer $offset
     * @return array
     * @throws \Exception
     */
    public function getUserEntries($id, $count = null, $offset = null)
    {
        $query = ['query' => []];
        if($count) {
            $query['query']['count'] = $count;
        }
        if($offset) {
            $query['query']['offset'] = $offset;
        }
        $data = $this->request('getUserEntries', [
            'id' => $id
        ], $query);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Get article by ID
     * @param integer $id
     * @return array
     * @throws \Exception
     */
    public function getEntryById($id)
    {
        $data = $this->request('getEntryById', [
            'id' => $id
        ], []);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Get popular entities by article ID
     * @param integer $id
     * @return array
     * @throws \Exception
     */
    public function getPopularEntries($id)
    {
        $data = $this->request('getPopularEntries', [
            'id' => $id
        ], []);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Get user info by ID
     * @param $id - user ID
     * @return array
     * @throws \Exception
     */
    public function getUser($id)
    {
        $data = $this->request('getUser', ['id'=>$id]);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }

    /**
     * Send new comment
     * @param integer $id - Article ID
     * @param string $text - Comment text
     * @param integer|null $replyTo - ID comment for reply
     * @return array
     * @throws \Exception
     */
    public function sendComment($id, $text, $replyTo = null)
    {
        if(empty($this->token)) {
            throw new \Exception('');
        }
        $formParams = [
            'id' => $id,
            'text' => $text
        ];
        if($replyTo) {
            $formParams['reply_to'] = $replyTo;
        }
        $data = $this->request('commentAdd', [], [
            'headers' => [
                'X-Device-Token' => $this->token
            ],
            'form_params' => $formParams
        ]);
        if(isset($data['result'])) {
            return $data['result'];
        }
        return $data;
    }
}