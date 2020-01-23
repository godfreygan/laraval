<?php

while ($line = fgets(STDIN)) {
    $log  = json_decode($line, true);
    $flag = false;

    if ($log['tags']['type'] == 'RPC' && $log['tags']['side'] == 'client') {
        $flag = true;
    } elseif ($log['tags']['type'] == 'DISPATCH') {
        $flag = true;
    }

    if (!$flag) {
        continue;
    }

    $data = array(
        'method'   => $log['name'],
        'params'   => json_decode($log['tags']['request_params'], true),
        'type'     => $log['tags']['type'],
        'domain'   => $log['tags']['service_name'],
        'duration' => $log['duration'],
        'trace_id' => $log['trace_id']
    );

    if (empty($data['params']) && $log['tags']['request_params']) {
        $data['params'] = $log['tags']['request_params'];
    }

    if (array_key_exists('errstr', $log['tags'])) {
        $data['errstr'] = $log['tags']['errstr'];
    }

    if (array_key_exists('errno', $log['tags'])) {
        $data['errno'] = $log['tags']['errno'];
    }

    echo json_encode($data) . "\n";
}
