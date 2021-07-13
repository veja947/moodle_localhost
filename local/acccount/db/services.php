<?php
$services = array(
    'localacccountservice' => array(                                                // the name of the web service
        'functions' => array ('local_acccount_test'), // web service functions of this service
        'requiredcapability' => '',                // if set, the web service user need this capability to access
        // any function of this service. For example: 'some/capability:specified'
        'restrictedusers' => 0,                                             // if enabled, the Moodle administrator must link some user to this service
        // into the administration
        'enabled' => 1,                                                       // if enabled, the service can be reachable on a default installation
        'shortname' =>  '',       // optional – but needed if restrictedusers is set so as to allow logins.
        'downloadfiles' => 0,    // allow file downloads.
        'uploadfiles'  => 0      // allow file uploads.
    )
);

$functions = array(
    'local_acccount_test' => array(         //web service function name
        'classname'   => 'local_acccount_external',  //class containing the external function OR namespaced class in classes/external/XXXX.php
        'methodname'  => 'test',          //external function name
        'classpath'   => 'local/acccount/externallib.php',  //file containing the class/external function - not required if using namespaced auto-loading classes.
        // defaults to the service's externalib.php
        'description' => 'just test.',    //human readable description of the web service function
        'type'        => 'write',                  //database rights of the web service function (read, write)
        'ajax' => true,        // is the service available to 'internal' ajax calls.
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE), // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
        'capabilities' => '', // comma separated list of capabilities used by the function.
    ),
);