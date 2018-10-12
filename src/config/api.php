<?php 
$root   =   'https://api.capsulecrm.com/api/v2/';
return [
    'resources' =>  [
        'people'    =>  [
            'plural'    =>  'parties',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'POST',
                'endpoint'  =>  $root.'parties/filters/results'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'parties/{0}?embed=tags,fields,organisation'
            ]
        ],
        'organisation'    =>  [
            'plural'    =>  'parties',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'POST',
                'endpoint'  =>  $root.'parties/filters/results'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'parties/{0}?embed=tags,fields,organisation'
            ]
        ],
        'party'     =>  [
            'plural'    =>  'parties',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'parties/'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'parties/{0}?embed=tags,fields,organisation'
            ]
        ],
        'opportunity'   =>  [
            'plural'    =>  'opportunities',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'POST',
                'endpoint'  =>  $root.'opportunities/filters/results'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'opportunities/{0}?embed=tags,fields,party,milestone'
            ]
        ],
        'kase'      =>  [
            'plural'    =>  'kases',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'POST',
                'endpoint'  =>  $root.'kases/filters/results'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'kases/{0}?embed=tags,fields,party'
            ]
        ],
        'task'      =>  [
            'plural'    =>  'tasks',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'tasks/'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'tasks/{0}?embed=party,opportunity,kase,owner,nextTask'
            ]
        ],
        'entries'      =>  [
            'plural'    =>  'entries',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'{0}/{1}/entries'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'tasks/{0}?embed=party,opportunity,kase,owner,nextTask'
            ]
        ],
        'settings'  =>  [
            'perPage'   =>  100
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