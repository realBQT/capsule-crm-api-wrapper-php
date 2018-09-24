<?php 
$root   =   'https://api.capsulecrm.com/api/v2/';
return [
    'resources' =>  [
        'party'     =>  [
            'create'    =>  [],
            'read'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'parties'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  []
        ]
    ],
    'settings'  =>  [
        'site'      =>  [
            'method'    =>  'GET',
            'endpoint'  =>  $root.'site',
            'payload'   =>  []
        ]
    ]
]

?>