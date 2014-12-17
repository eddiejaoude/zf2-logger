<?php
return array(
    'EddieJaoude\Zf2Logger' => array(

        // will add the $logger object before the current PHP error handler
        'registerErrorHandler'     => true, // errors logged to your writers
        'registerExceptionHandler' => true, // exceptions logged to your writers

        // do not log binary responses
        // mime types reference http://www.sitepoint.com/web-foundations/mime-types-complete-list/
        'doNotLog'                 => array(
            'mediaTypes' => array(
                'application/octet-stream',
                'image/png',
                'image/jpeg',
                'application/pdf'
            ),
        ),
    )
);
