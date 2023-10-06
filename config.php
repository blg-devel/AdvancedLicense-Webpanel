<?php

/*
//========================================================================================================================\\
#  _______ ______  _    _ _______ __   _ _______ _______ ______              _____ _______ _______ __   _ _______ _______  #
#  |_____| |     \  \  /  |_____| | \  | |       |______ |     \      |        |   |       |______ | \  | |______ |______  #
#  |     | |_____/   \/   |     | |  \_| |_____  |______ |_____/      |_____ __|__ |_____  |______ |  \_| ______| |______  #
#  Coded by Leoko                                                                                                          #
\\========================================================================================================================//
*/

////////////////////////////////////////////////
//             MySQL-Data                     //
//                                     	      //
//Database-Name
define("DB_NAME", "Licenses");

//MySQL- Server-IP/Domanin
define("HOST", "localhost");

//MySQL-Account
define("USERNAME", "YOUR-USERNAME");
define("PASSWORD", "YOUR-PASSWORD");

//                                            //
////////////////////////////////////////////////

////////////////////////////////////////////////
//             Security                       //
//                                     	      //
// This keys will be used for the
// cryptographic-key-authentication-protection
// Required length: 36 [only 1 & 0]
// Private key for server license validation:
define("CKAP_KEY", "YecoF0I6M05thxLeokoHuW8iUhTdIUInjkfF");
// Public key for user-validation:
define("CKAP_KEY_CLIENT", "rHnVh9RsoYLmU6P5SrvaBT43d5z9vPiLzFLc");
// If you change this keys you will also need
// to change it in the corresponding java component
// by .setSecurityKey('Your-Key');

// If enabled this will automatically redirect to https
define("HTTPS", false);

//                                            //
////////////////////////////////////////////////

////////////////////////////////////////////////
//             Stats                          //
//                                     	      //
// Whether license-requests will be logged
// and displayed in the dashboard
define("STATS", true);
// You can disable this to avoid error-messages
// if PHP does not have enough permissions
// or to boost the performance [just a few milliseconds]

//                                            //
////////////////////////////////////////////////

////////////////////////////////////////////////
//             Admin-Account                  //
//                                     	      //
// You will need this data to login
define("ADMIN_USERNAME", "");
define("ADMIN_PASSWORD", "");

//                                            //
////////////////////////////////////////////////

////////////////////////////////////////////////
//                Indexing                    //
//                                     	      //
// Do not change this unless you
// really know what are you doing!
// Block search engines from indexing?
define("NOINDEX", true);

// Block search engines from
// following the links?
define("NOFOLLOW", false);

//                                            //
////////////////////////////////////////////////

// no ending tag to avoid accidental output
