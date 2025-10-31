<?php

use PHPUnit\Framework\TestCase;
use Waivers\WaiversValidator;

class WaiversValidatorTest extends TestCase
{
    private $validator;
    
    protected function setUp(): void
    {
        $this->validator = new WaiversValidator();
    }
    
    public function testValidateDropSucceedsWithNormalRosterAndSalary()
    {
        $result = $this->validator->validateDrop(
            10, // roster slots
            6000 // total salary (under cap)
        );
        
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }
    
    public function testValidateDropFailsWithFullRosterOverCap()
    {
        $result = $this->validator->validateDrop(
            13, // more than 2 roster slots (12+ players)
            7500 // over hard cap
        );
        
        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString("12 players", $errors[0]);
        $this->assertStringContainsString("over the hard cap", $errors[0]);
    }
    
    public function testValidateDropSucceedsWithFullRosterUnderCap()
    {
        $result = $this->validator->validateDrop(
            13, // more than 2 roster slots
            6000 // under hard cap
        );
        
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }
    
    public function testValidateAddFailsWithNullPlayerID()
    {
        $result = $this->validator->validateAdd(
            null,  // no player selected
            5,     // healthy roster slots
            6000,  // total salary
            100    // player salary
        );
        
        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString("didn't select a valid player", $errors[0]);
    }
    
    public function testValidateAddFailsWithZeroPlayerID()
    {
        $result = $this->validator->validateAdd(
            0,     // invalid player ID
            5,     // healthy roster slots
            6000,  // total salary
            100    // player salary
        );
        
        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString("didn't select a valid player", $errors[0]);
    }
    
    public function testValidateAddFailsWithFullRoster()
    {
        $result = $this->validator->validateAdd(
            123,   // valid player ID
            0,     // no healthy roster slots available
            6000,  // total salary
            100    // player salary
        );
        
        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString("full roster", $errors[0]);
    }
    
    public function testValidateAddFailsWith12PlusHealthyPlayersOverCap()
    {
        $result = $this->validator->validateAdd(
            123,   // valid player ID
            3,     // 12 healthy players (4-1)
            6800,  // current salary
            300    // player salary (would put over cap)
        );
        
        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString("12 or more healthy players", $errors[0]);
        $this->assertStringContainsString("over the hard cap", $errors[0]);
    }
    
    public function testValidateAddSucceedsWith12HealthyPlayersUnderCap()
    {
        $result = $this->validator->validateAdd(
            123,   // valid player ID
            3,     // 12 healthy players
            6000,  // current salary
            100    // player salary (stays under cap)
        );
        
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }
    
    public function testValidateAddFailsOverCapWithNonVetMin()
    {
        $result = $this->validator->validateAdd(
            123,   // valid player ID
            5,     // under 12 healthy players
            6980,  // current salary
            400    // player salary above vet min (103), would put over cap
        );
        
        $this->assertFalse($result);
        $errors = $this->validator->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString("over the hard cap", $errors[0]);
        $this->assertStringContainsString("veteran minimum", $errors[0]);
    }
    
    public function testValidateAddSucceedsOverCapWithVetMin()
    {
        $result = $this->validator->validateAdd(
            123,   // valid player ID
            5,     // under 12 healthy players
            7100,  // over hard cap
            103    // vet min salary
        );
        
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }
    
    public function testValidateAddSucceedsWithNormalConditions()
    {
        $result = $this->validator->validateAdd(
            123,   // valid player ID
            5,     // healthy roster slots available
            6000,  // total salary
            500    // player salary
        );
        
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }
    
    public function testClearErrorsRemovesAllErrors()
    {
        // First create an error
        $this->validator->validateAdd(null, 5, 6000, 100);
        $this->assertNotEmpty($this->validator->getErrors());
        
        // Clear errors
        $this->validator->clearErrors();
        $this->assertEmpty($this->validator->getErrors());
    }
    
    public function testValidateAddEdgeCaseAtExactCap()
    {
        $result = $this->validator->validateAdd(
            123,   // valid player ID
            5,     // healthy roster slots
            6900,  // current salary
            100    // player salary brings to exactly 7000
        );
        
        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }
}
