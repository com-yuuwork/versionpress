<?php

/**
 * Base class for Selenium tests that use the test-config.ini configuration.
 */
abstract class SeleniumTestCase extends PHPUnit_Extensions_Selenium2TestCase {

    /**
     * Configuration read from `test-config.ini` and set to this variable from phpunit-bootstrap.php.
     *
     * @var TestConfig
     */
    public static $config;

    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->setBrowser("firefox");

        $capabilities = $this->getDesiredCapabilities();
        if (self::$config->getFirefoxExecutable()) {
            $capabilities["firefox_binary"] = self::$config->getFirefoxExecutable();
        }
        $this->setDesiredCapabilities($capabilities);

        $this->setBrowserUrl(self::$config->getSiteUrl());
    }

    /**
     *
     */
    protected function loginIfNecessary() {
        $this->url('wp-admin');
        try {
            $this->byId('user_login');
        } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            // already logged in, do nothing
            return;
        }
        $this->byId('user_login')->value(self::$config->getAdminName());
        usleep(100000); // wait for change focus
        $this->byId('user_pass')->value(self::$config->getAdminPassword());
        $this->byId('wp-submit')->click();
    }
} 