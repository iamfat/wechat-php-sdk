<?php

namespace Wechat {

    class HTTP
    {
        private $_header = [];
        private $_post = [];
        private $_get = [];

        public function header($name, $value)
        {
            $this->_header[$name] = $value;

            return $this;
        }

        public function get($url, $query, $timeout = 5)
        {
            $qpos = strpos($url, '?');
            $url .= ($qpos === false) ? '?' : '&';
            $url .= is_string($query) ? $query : http_build_query($query);

            return $this->request($url, $timeout);
        }

        public function post($url, $query, $timeout = 5)
        {
            $this->_post = $query;

            return $this->request($url, $timeout);
        }

        public function clean()
        {
            $this->_header = [];
            $this->_post = [];
        }

        public function & request($url, $timeout = 5)
        {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_AUTOREFERER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FRESH_CONNECT => true,
                CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?: 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
                CURLOPT_REFERER => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
            ));

            if ($this->_cookie_file) {
                curl_setopt_array($ch, array(
                    CURLOPT_COOKIEFILE => $this->_cookie_file,
                    CURLOPT_COOKIEJAR => $this->_cookie_file,
                ));
            }

            if ($this->_proxy) {
                curl_setopt_array($ch, array(
                    CURLOPT_HTTPPROXYTUNNEL => true,
                    CURLOPT_PROXY => $this->_proxy,
                    CURLOPT_PROXYTYPE => $this->_proxy_type,
                ));
            }

            if ($this->_header) {
                $curl_header = array();
                foreach ($this->_header as $k => $v) {
                    $curl_header[] = $k.': '.$v;
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);
            }

            if ($this->_post) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($this->_post) ? http_build_query($this->_post) : $this->_post);
            }

            $data = curl_exec($ch);

            $this->clean();

            $errno = curl_errno($ch);
            if ($errno || !$data) {
                $err = curl_error($ch);
                _LOG("CURL ERROR($errno $err): $url ", 'error');
                curl_close($ch);

                return;
            }

            $info = curl_getinfo($ch);

            curl_close($ch);

            return new HTTP_Response($data, $info['http_code']);
        }
    }

    class HTTP_Response
    {
        public $header = [];
        public $status = null;
        public $body = null;

        public function __construct($data, $status)
        {
            list($header, $body) = explode("\n\n", str_replace("\r", "", $data), 2);

            $this->body = trim($body);

            $header = explode("\n", $header);
            $status = array_shift($header);
            $this->status = $status;

            foreach ($header as $h) {
                list($k, $v) = explode(': ', $h, 2);
                if ($k) {
                    $this->header[$k] = $v;
                }
            }
        }

        public function __toString()
        {
            return $this->body;
        }
    }

}
