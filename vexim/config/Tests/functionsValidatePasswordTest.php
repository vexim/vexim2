<?php

class FunctionValidatePasswordTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        require_once(__DIR__ . '/../functions.php');
    }

    public function testSendEmptyPasswords()
    {
        $this->assertFalse(validate_password("", ""));
    }

    public function testSendSamePasswords()
    {
        $this->assertTrue(validate_password("password", "password"));
    }

    public function testSendDifferentPasswords()
    {
        $this->assertFalse(validate_password("password", "pass"));
    }

    public function testSendNullValues()
    {
        $this->assertFalse(validate_password(null, null));
    }

    public function testSendBoolValues()
    {
        $this->assertFalse(validate_password(true, true));
        $this->assertFalse(validate_password(false, false));
    }

    public function testSendIntegerValues()
    {
        $this->assertFalse(validate_password(10, 10));
    }

    public function testSendZeroValues()
    {
        $this->assertFalse(validate_password(0, 0));
    }
}

