<?php

return [
    'name'        => 'Garagist API Extension',
    'description' => 'Extend Mautic API',
    'version'     => '0.1',
    'author'      => 'David Spiola',
    'author'      => 'Jon Uhlmann',
    'routes'   => array(
        'api' => array(
            'mautic_api_sendeexamplemail' => [
                'path'       => '/emails/{id}/example',
                'controller' => 'GaragistMauticApiBundle:Api\EmailApi:exampleEmail',
                'method'     => 'POST',
            ]
        )
    ),
];
