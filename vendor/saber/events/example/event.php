<?php

use Saber\Events\Event;

require "../vendor/autoload.php";

function gen()
{
    $n = 1;
    while (1) {
        yield new Event('e1', 'e1data', $n++);
        sleep(1);
        yield Event::fromArray([
            'type' => 'e2',
            'data' => 'e2data',
            'id' => $n++
        ]);
        sleep(1);
        $event = [
            'type' => 'e3',
            'data' => 'e3data',
            'id' => $n++
        ];
        yield Event::fromString(json_encode($event));
        sleep(1);
    }
}

$eventGen = gen();

while ($eventGen->valid()) {
    /** @var \Saber\Events\Event $event */
    $event = $eventGen->current();
    echo $event, PHP_EOL;
    echo $event->getId(), ' - ', $event->getType(), ' - ', $event->getData(),PHP_EOL;
    $eventGen->next();
}
