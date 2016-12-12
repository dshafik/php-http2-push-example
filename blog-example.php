<?php
class H2PushCache {
  static $cache = [];
  static $pushHandles = [];

  static function addPushHandle($headers, $handle)
  {
    foreach ($headers as $header) {
      if (strpos($header, ':path:') === 0) {
        $path = substr($header, 6);
        $url = curl_getinfo($handle)['url'];
        $url = str_replace(
          parse_url($url, PHP_URL_PATH),
          $path,
          $url
        );
        static::$pushHandles[$url] = $handle;
      }
    }
  }

  static function add($handle)
  {
    $found = false;
    foreach (static::$pushHandles as $url => $h) {
      if ($handle == $h) {
        $found = $url;
      }
    }

    if (!$found) {
      $found = curl_getinfo($handle)['url'];
    }

    static::$cache[$found] = curl_multi_getcontent($handle);
  }

  static function exists($url)
  {
    if (isset(static::$cache[$url])) {
      return true;
    }

    return false;
  }

  static function get($url)
  {
    return static::$cache[$url];
  }
}

function get_request($url)
{
  if (H2PushCache::exists($url)) {
    return H2PushCache::get($url);
  }

  $transfers = 1;
  $cb = function ($parent, $pushed, $headers) use (&$transfers) {
    $transfers++; // increment to keep track of the number of concurrent requests

    H2PushCache::addPushHandle($headers, $pushed);

    return CURL_PUSH_OK;
  };

  $mh = curl_multi_init();

  curl_multi_setopt($mh,
    CURLMOPT_PIPELINING, CURLPIPE_MULTIPLEX
  );
  curl_multi_setopt($mh,
    CURLMOPT_PUSHFUNCTION, $cb
  );

  $ch = curl_init();
  curl_setopt($ch,
    CURLOPT_URL, $url
  );
  curl_setopt($ch,
    CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2
  );
  curl_setopt($ch,
    CURLOPT_RETURNTRANSFER, 1
  );

  curl_multi_add_handle($mh, $ch);

  $active = null;
  do {
    $status = curl_multi_exec($mh, $active);

    do {
      $info = curl_multi_info_read($mh);
      if (false !== $info
          && $info['msg'] == CURLMSG_DONE)
      {
        $handle = $info['handle'];
        if ($handle !== null) {
          $transfers--; // decrement remaining requests
          H2PushCache::add($handle);
          curl_multi_remove_handle($mh, $handle);
          curl_close($handle);
        }
      }
    } while ($info);
  } while ($transfers);

  curl_multi_close($mh);

  return H2PushCache::get($url);
}

$url = 'https://example/post/1';
$response = get_request($url);
$post = json_decode($response);
$response = get_request($post->comments);
$comments = json_decode($reponse);
$response = get_request($post->author);
$author = json_decode($response);
