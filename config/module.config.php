<?php
return array(
    'EddieJaoude\Zf2Logger' => array(

        // do not log binary responses
        // mime types reference http://www.sitepoint.com/web-foundations/mime-types-complete-list/
        'doNotLog' => array(
            'mediaTypes' => array(
                'application/octet-stream',
                'image/png',
                'image/jpeg',
                'application/pdf'
            ),
        ),
    ),
);
