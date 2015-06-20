<?php

/**
 * Class vineAuth
 *
 * @author Selçuk Çrlik
 * @blog http://selcuk.in
 * @mail selcuk@msn.com
 * @date 18.6.2015
 */

class vineAuth
{
    private $_useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/600.6.3 (KHTML, like Gecko) Version/8.0.6 Safari/600.6.3";

    private $_proxy = array();

    private $_keys = array();

    /*
     *  Vine hesabına giriş fonksiyon.
     *
     *  @param $username(or email)
     *  @param $password
     *  @return string
     */
    public function login($username, $password)
    {
        $randomProxy = ( count($this->_proxy) > 0 ) ? $this->_proxy[rand(0, (count($this->_proxy) - 1))] : NULL;

        $header = array();
        $header[] = 'Accept: application/json, text/javascript, */*; q=0.01';
        $header[] = 'x-vine-client: vinewww/2.0';
        $header[] = 'Referer: https://vine.co/forgot-password';
        $header[] = 'X-Requested-With: XMLHttpRequest';
        $header[] = "User-Agent: {$this->_useragent}";

        $selco = curl_init();
        $options = array(
            CURLOPT_URL => "https://vine.co/api/clientflags",
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HEADER => FALSE,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_USERAGENT => $this->_useragent,
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE
        );

        if ( !empty($randomProxy) )
        {
            $options[CURLOPT_PROXY] = $randomProxy;
            $options[CURLOPT_PROXYTYPE] = 'HTTP';
        }

        curl_setopt_array($selco, $options);
        $page = curl_exec($selco);
        $page = json_decode($page);

        if ( $page->success == TRUE )
        {
            $header = array();
            $header[] = 'vine-guest-id: ' . $page->data->guestIdStr;
            $header[] = 'x-vine-client: vinewww/2.0';
            $header[] = 'Origin: https://vine.co';
            $header[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
            $header[] = 'Accept: application/json, text/javascript, */*; q=0.01';
            $header[] = 'Referer: https://vine.co/forgot-password';
            $header[] = 'X-Requested-With: XMLHttpRequest';
            $header[] = "User-Agent: {$this->_useragent}";
            $options = array(
                CURLOPT_URL => "https://vine.co/api/users/authenticate",
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => http_build_query(array('username' => $username, 'password' => $password))
            );

            if ( !empty($randomProxy) )
            {
                $options[CURLOPT_PROXY] = $randomProxy;
                $options[CURLOPT_PROXYTYPE] = 'HTTP';
            }
            curl_setopt_array($selco, $options);
            $login = curl_exec($selco);
            $login = json_decode($login);

            curl_close($selco);

            if ( $page->success == TRUE )
            {
                $result = $this->me($login->data->key);
                if ( $result->success == FALSE )
                {
                    throw new Exception('Error: login() - Giriş hatalı. Hata Detayı: ' . $result->error);
                }

            } else {

                throw new Exception('Error: login() - Giriş hatalı. Hata Detayı: ' . $login->error);
            }

        } else {

            throw new Exception('Error: login() - Login session oluşturma hatalı.');
        }

        $json = json_encode(
            array(
                'key' => $login->data->key,
                'me' => $result
            )
        );
        return json_decode($json);
    }

    /*
     *  Vine sessiona ait kullanıcının bilgilerine ulaşıldığı fonksiyon.
     *
     *  @param string $key
     *  @return string
     */
    public function me($key)
    {
        $header = array();
        $header[] = 'Accept: application/json, text/javascript, */*; q=0.01';
        $header[] = 'x-vine-client: vinewww/2.0';
        $header[] = 'Referer: https://vine.co/';
        $header[] = 'X-Requested-With: XMLHttpRequest';
        $header[] = 'vine-session-id: ' . $key;
        $header[] = "User-Agent: {$this->_useragent}";

        $selco = curl_init();
        $options = array(
            CURLOPT_URL => "https://vine.co/api/users/me",
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HEADER => FALSE,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_USERAGENT => $this->_useragent,
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE
        );

        if ( !empty($randomProxy) )
        {
            $options[CURLOPT_PROXY] = $randomProxy;
            $options[CURLOPT_PROXYTYPE] = 'HTTP';
        }

        curl_setopt_array($selco, $options);
        $page = curl_exec($selco);
        $page = json_decode($page);

        curl_close($selco);

        return $page;
    }

    /*
     *  Takipçi çekimi yapan sınıf fonksiyonu.
     *
     *  @param integer $ID
     *  @param boolean $delete
     *  @return json
     */
    public function follow($ID, $delete = NULL)
    {
        if ( !empty($this->_keys) )
        {
            foreach ( $this->_keys as $key )
            {

                $randomProxy = ( count($this->_proxy) > 0 ) ? $this->_proxy[rand(0, (count($this->_proxy) - 1))] : NULL;

                $header = array();
                $header[] = 'x-vine-client: vinewww/2.0';
                $header[] = 'Origin: https://vine.co';
                $header[] = "User-Agent: {$this->_useragent}";
                $header[] = 'Accept: application/json, text/javascript, */*; q=0.01';
                $header[] = 'Referer: https://vine.co/u/' . $ID;
                $header[] = 'X-Requested-With: XMLHttpRequest';
                $header[] = 'vine-session-id: ' . $key;

                $options = array(
                    CURLOPT_HTTPHEADER => $header,
                    CURLOPT_POST => TRUE,
                    CURLOPT_POSTFIELDS => array(),
                    CURLOPT_SSL_VERIFYPEER => FALSE,
                    CURLOPT_SSL_VERIFYHOST => FALSE
                );

                if ( !empty($randomProxy) )
                {
                    $options[CURLOPT_PROXY] = $randomProxy;
                    $options[CURLOPT_PROXYTYPE] = 'HTTP';
                }

                if ( $delete == TRUE )
                {
                    $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                }

                $params[] = array(
                    array(
                        'url' => "https://vine.co/api/users/{$ID}/followers",
                        'options' => $options
                    )
                );

                $response = $this->_multiCURL($params);

                $success = 0;
                foreach ( $response as $result )
                {
                    if ( is_string($result) )
                    {
                        $result = json_decode($result);
                        if ( is_object($result) )
                        {
                            if ($result->success == TRUE) $success++;
                        }
                    }
                }
                return $success;

            }
        } else {

            throw new Exception('Error: follow() - Takipçi yaptırabilmeniz için {keys} tanımlamalısınız.');
        }
    }

    /*
     *  Beğeni göndermek için kullanılan sınıf fonksiyonu.
     *
     *  @param integer $ID
     *  @param boolean $delete
     *  @return string
     */
    public function like($ID, $delete = NULL)
    {
        if ( !empty($this->_keys) )
        {
            $data = $this->getVine($ID);
            $data = json_decode($data);
            $vineID = $data->data->records[0]->postId;
            if ( $vineID )
            {
                foreach ( $this->_keys as $key )
                {
                    $randomProxy = ( count($this->_proxy) > 0 ) ? $this->_proxy[rand(0, (count($this->_proxy) - 1))] : NULL;

                    $header = array();
                    $header[] = 'x-vine-client: vinewww/2.0';
                    $header[] = 'Origin: https://vine.co';
                    $header[] = "User-Agent: {$this->_useragent}";
                    $header[] = 'Accept: application/json, text/javascript, */*; q=0.01';
                    $header[] = 'Referer: ' . $ID;
                    $header[] = 'X-Requested-With: XMLHttpRequest';
                    $header[] = 'vine-session-id: ' . $key;

                    $options = array(
                        CURLOPT_HTTPHEADER => $header,
                        CURLOPT_POST => TRUE,
                        CURLOPT_POSTFIELDS => array(),
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                        CURLOPT_SSL_VERIFYHOST => FALSE
                    );

                    if ( !empty($randomProxy) )
                    {
                        $options[CURLOPT_PROXY] = $randomProxy;
                        $options[CURLOPT_PROXYTYPE] = 'HTTP';
                    }

                    if ( $delete == TRUE )
                    {
                        $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                    }

                    $params[] = array(
                        array(
                            'url' => "https://vine.co/api/posts/{$vineID}/likes",
                            'options' => $options
                        )
                    );

                    $response = $this->_multiCURL($params);

                    $success = 0;
                    foreach ( $response as $result )
                    {
                        if ( is_string($result) )
                        {
                            $result = json_decode($result);
                            if ( is_object($result) )
                            {
                                if ($result->success == TRUE) $success++;
                            }
                        }
                    }
                    return $success;

                }

            } else {

                throw new Exception('Error: like() - Geçersiz bir vine bağlantısı yazdınız.');
            }
        } else {

            throw new Exception('Error: like() - Beğeni yaptırabilmeniz için {keys} tanımlamalısınız.');
        }
    }

    /*
     *  Revine yapmak için kullanılan sınıf fonksiyonu.
     *
     *  @param integer $ID
     *  @return string
     */
    public function revine($ID)
    {
        if ( !empty($this->_keys) )
        {
            $data = $this->getVine($ID);
            $data = json_decode($data);
            $vineID = $data->data->records[0]->postId;
            if ( $vineID )
            {
                foreach ( $this->_keys as $key )
                {
                    $randomProxy = ( count($this->_proxy) > 0 ) ? $this->_proxy[rand(0, (count($this->_proxy) - 1))] : NULL;

                    $header = array();
                    $header[] = 'x-vine-client: vinewww/2.0';
                    $header[] = 'Origin: https://vine.co';
                    $header[] = "User-Agent: {$this->_useragent}";
                    $header[] = 'Accept: application/json, text/javascript, */*; q=0.01';
                    $header[] = 'Referer: ' . $ID;
                    $header[] = 'X-Requested-With: XMLHttpRequest';
                    $header[] = 'vine-session-id: ' . $key;

                    $options = array(
                        CURLOPT_HTTPHEADER => $header,
                        CURLOPT_POST => TRUE,
                        CURLOPT_POSTFIELDS => array(),
                        CURLOPT_SSL_VERIFYPEER => FALSE,
                        CURLOPT_SSL_VERIFYHOST => FALSE
                    );

                    if ( !empty($randomProxy) )
                    {
                        $options[CURLOPT_PROXY] = $randomProxy;
                        $options[CURLOPT_PROXYTYPE] = 'HTTP';
                    }

                    $params[] = array(
                        array(
                            'url' => "https://vine.co/api/posts/{$vineID}/repost",
                            'options' => $options
                        )
                    );

                    $response = $this->_multiCURL($params);

                    $success = 0;
                    foreach ( $response as $result )
                    {
                        if ( is_string($result) )
                        {
                            $result = json_decode($result);
                            if ( is_object($result) )
                            {
                                if ($result->success == TRUE) $success++;
                            }
                        }
                    }
                    return $success;

                }

            } else {

                throw new Exception('Error: revine() - Geçersiz bir vine bağlantısı yazdınız.');
            }
        } else {

            throw new Exception('Error: revine() - Revine yaptırabilmeniz için {keys} tanımlamalısınız.');
        }
    }

    /*
     *  Vine kullanıcı aramak için kullanılan fonksiyon
     *
     *  @param string $q
     *  @param integer $count
     *  @return json
     */
    public function search($q, $count = 1)
    {
        return $this->_cURL("https://vine.co/api/users/search/{$q}?size={$count}");
    }

    /*
     *  Vine video detaylarına ulaşma fonksiyon.
     *
     *  @param string $URL
     *  @return json
     */
    public function getVine($URL)
    {
        $URL = parse_url($URL);
        $ID = str_replace(array('/v/'), NULL, $URL['path']);
        return $this->_cURL("https://vine.co/api/timelines/posts/s/{$ID}");
    }

    /*
     *  Çekimler için kullanılacak vine session keys tanımlama fonksiyonu.
     *
     *  @param string $keys
     *  @return null
     */
    public function setKeys($keys)
    {
        $this->_keys = $keys;
    }

    /*
     *  Çekimler için kullanılacak proxy tanımlama fonksiyonu
     *
     *  @param array $proxy
     *  @return null
     */
    public function setProxy($proxy)
    {
        $this->_proxy = $proxy;
    }

    /*
     *  Tüm çekimler için kullanılacak olan multiCurl fonksiyonu.
     *
     *  @param array $params
     *  @return mixed
     */
    protected function _multiCURL($params)
    {

        $result = array();
        $multiCurl = array();
        $selco = curl_multi_init();
        $query = 0;
        for ($i = 0; $i <= (count($params) - 1); $i++)
        {
            foreach ( $params[$i] as $param )
            {
                if ( is_array($param) )
                {
                    $multiCurl[$query] = curl_init();

                    $options = array(
                        CURLOPT_URL => $param['url'],
                        CURLOPT_HEADER => FALSE,
                        CURLOPT_USERAGENT => $this->_useragent,
                        CURLOPT_FOLLOWLOCATION => FALSE,
                        CURLOPT_RETURNTRANSFER => TRUE
                    );

                    if ( isset($param['options']) && is_array($param['options']) )
                    {
                        foreach($param['options'] as $option => $value) {
                            $options[$option] = $value;
                        }
                    }
                    curl_setopt_array($multiCurl[$query], $options);
                    curl_multi_add_handle($selco, $multiCurl[$query]);

                    $query++;
                }
            }
        }

        $running = NULL;
        do {

            curl_multi_exec($selco, $running);

        } while($running > 0);

        foreach($multiCurl as $key => $value) {
            $result[$key] = curl_multi_getcontent($value);
            curl_multi_remove_handle($selco, $value);
        }
        curl_multi_close($selco);

        return str_replace(array("\n","\t","\r"), NULL, $result);
    }

    /*
     *  Tekil işlemler için kullanılan cURL fonksiyonu.
     *
     *  @param string $URL
     *  @param array $params
     *  @return mixed
     */
    protected function _cURL($URL, $params = NULL)
    {

        $c = curl_init();
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($c, CURLOPT_TIMEOUT, 30);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($c, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($c, CURLOPT_URL, $URL);
        if ( $params )
        {
            curl_setopt($c, CURLOPT_POST, TRUE);
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        $contents = curl_exec($c);

        $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
        curl_close($c);

        if ($contents) return $contents;
        else return FALSE;

    }
}
