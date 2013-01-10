<?php

return array(
    'servers' => array(
        '173.192.165.130:1234' => array('www.issh.in', '6K97nz')
    ),
    'database' => array(
            'connection' => getenv('CLEARDB_DATABASE_URL'),
            'initialization' => array(
                'SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary',
                'SET sql_mode=``'
            )
    )
);