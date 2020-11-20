<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'mvclogin';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
	
	const SECRET_KEY = "PNtjF7b6UHD5zOIulJLtytbiX8obTdOd";
	
	const MAILGUN_API_KEY = "1ea6e87bb95c623ae3910333647c89bb-87c34c41-1745db6b";
	
	const MAILGUN_DOMAIN = "sandbox2970fd31f74344f6ac3143ece7fa0f0f.mailgun.org";
}


