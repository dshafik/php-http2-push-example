<?php
$transfers = 1;

$callback = function() use (&$transfers) {
	return CURL_PUSH_DENY;
	$transfers++;
};

$mh = curl_multi_init();

curl_multi_setopt($mh, CURLMOPT_PIPELINING, CURLPIPE_MULTIPLEX);
curl_multi_setopt($mh, CURLMOPT_PUSHFUNCTION, $callback);

$ch = curl_init();
curl_setopt($ch, CURLOPT_VERBOSE, 1);
//curl_setopt($ch, CURLOPT_URL, $_SERVER['argv'][1]);
curl_setopt($ch, CURLOPT_URL, 'https://localhost:8443/index.html');
curl_setopt($ch, CURLOPT_HTTP_VERSION, 3);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_multi_add_handle($mh, $ch);

$active = null;
do {
    $status = curl_multi_exec($mh, $active);

    do {
        $info = curl_multi_info_read($mh);
        if (false !== $info && $info['msg'] == CURLMSG_DONE) {
            $handle = $info['handle'];
            if ($handle !== null) {
                $transfers--;
		$out = curl_multi_getcontent($info['handle']);
		var_dump($out);
                curl_multi_remove_handle($mh, $handle);
                curl_close($handle);
            }
        }
    } while ($info);
} while ($transfers);

curl_multi_close($mh);
