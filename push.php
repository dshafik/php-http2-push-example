<?php
$callback = function() {
	$args = func_get_args();
	var_dump($args);
	var_dump(CURL_PUSH_OK);
	return CURL_PUSH_OK;
};

$mh = curl_multi_init();

curl_multi_setopt($mh, CURLMOPT_PUSHFUNCTION, $callback);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_SERVER['argv'][1]);
curl_setopt($ch, CURLOPT_HTTP_VERSION, 3);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 

curl_multi_add_handle($mh, $ch);

$active = null;
//execute the handles
do {
    $mrc = curl_multi_exec($mh, $active);
} while ($mrc == CURLM_CALL_MULTI_PERFORM);

while ($active && $mrc == CURLM_OK) {
    if (curl_multi_select($mh) != -1) {
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
}

curl_multi_close($mh);
