<?php

use PHPUnit\Framework\TestCase;

/**
 * Comprehensive unit tests for Trading_TradeValidator class
 * 
 * This demonstrates modern PHP testing best practices including:
 * - Proper test structure with setUp/tearDown
 * - Mock objects and dependency injection
 * - Data providers for parametrized testing
 * - Descriptive test names and clear assertions
 * - Edge case testing and error handling
 */
class TradeValidatorTest extends TestCase
{
    private $validator;
    private $mockDb;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->validator = new Trading_TradeValidator($this->mockDb);
    }

    protected function tearDown(): void
    {
        $this->validator = null;
        $this->mockDb = null;
    }

    /**
     * @group validation
     * @group cash
     */
    public function testValidatesMinimumCashAmountsSuccessfully()
    {
        // Arrange
        $userCash = [1 => 100, 2 => 200, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $partnerCash = [1 => 150, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

        // Act
        $result = $this->validator->validateMinimumCashAmounts($userCash, $partnerCash);

        // Assert
        $this->assertTrue($result['valid'], 'Valid cash amounts should pass validation');
        $this->assertNull($result['error'], 'No error should be returned for valid amounts');
    }

    /**
     * @group validation
     * @group cash
     * @dataProvider invalidCashAmountProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidCashAmountProvider')]
    public function testRejectsCashAmountsBelowMinimum($userCash, $partnerCash, $expectedErrorText)
    {
        // Act
        $result = $this->validator->validateMinimumCashAmounts($userCash, $partnerCash);

        // Assert
        $this->assertFalse($result['valid'], 'Invalid cash amounts should fail validation');
        $this->assertStringContainsString($expectedErrorText, $result['error']);
    }

    /**
     * @group validation
     * @group salary-cap
     */
    public function testValidatesSalaryeCapsWithinLimits()
    {
        // Arrange
        $tradeData = [
            'userCurrentSeasonCapTotal' => 50000,
            'partnerCurrentSeasonCapTotal' => 55000,
            'userCapSentToPartner' => 5000,
            'partnerCapSentToUser' => 4000
        ];

        // Act
        $result = $this->validator->validateSalaryCaps($tradeData);

        // Assert
        $this->assertTrue($result['valid'], 'Valid salary caps should pass validation');
        $this->assertEmpty($result['errors'], 'No errors should be returned for valid caps');
        $this->assertEquals(49000, $result['userPostTradeCapTotal']); // 50000 - 5000 + 4000
        $this->assertEquals(56000, $result['partnerPostTradeCapTotal']); // 55000 - 4000 + 5000
    }

    /**
     * @group validation
     * @group salary-cap
     * @dataProvider salaryCapViolationProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('salaryCapViolationProvider')]
    public function testRejectsTradesExceedingSalaryCaps($tradeData, $expectedErrorCount)
    {
        // Act
        $result = $this->validator->validateSalaryCaps($tradeData);

        // Assert
        $this->assertFalse($result['valid'], 'Salary cap violations should fail validation');
        $this->assertCount($expectedErrorCount, $result['errors']);
    }

    /**
     * @group cash-considerations
     */
    public function testCalculatesCurrentSeasonCashConsiderationsCorrectly()
    {
        // Arrange
        $userCash = [1 => 100, 2 => 200, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $partnerCash = [1 => 150, 2 => 250, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

        // Act
        $result = $this->validator->getCurrentSeasonCashConsiderations($userCash, $partnerCash);

        // Assert
        $this->assertEquals(100, $result['cashSentToThem']);
        $this->assertEquals(150, $result['cashSentToMe']);
        $this->assertArrayHasKey('cashSentToThem', $result);
        $this->assertArrayHasKey('cashSentToMe', $result);
    }

    /**
     * @group player-validation
     */
    public function testDeterminesPlayerTradabilityCorrectly()
    {
        // Arrange - Valid tradeable player
        $playerId = 12345;
        $this->mockDb->setMockData([
            [1000, 5000] // ordinal, cy (salary)
        ]);

        // Act
        $result = $this->validator->canPlayerBeTraded($playerId);

        // Assert
        $this->assertTrue($result, 'Valid player should be tradeable');
    }

    /**
     * @group player-validation
     * @dataProvider nonTradeablePlayerProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('nonTradeablePlayerProvider')]
    public function testPreventsTradingIneligiblePlayers($mockData, $expectedResult, $reason)
    {
        // Arrange
        $playerId = 12345;
        $this->mockDb->setMockData($mockData);

        // Act
        $result = $this->validator->canPlayerBeTraded($playerId);

        // Assert
        $this->assertEquals($expectedResult, $result, $reason);
    }

    /**
     * Data provider for invalid cash amounts
     */
    public static function invalidCashAmountProvider()
    {
        return [
            'User cash below minimum' => [
                [1 => 50, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], // User cash
                [1 => 150, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], // Partner cash
                'minimum amount of cash that your team can send' // Expected error text
            ],
            'Partner cash below minimum' => [
                [1 => 100, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], // User cash
                [1 => 50, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], // Partner cash
                'minimum amount of cash that the other team can send' // Expected error text
            ]
        ];
    }

    /**
     * Data provider for salary cap violations
     */
    public static function salaryCapViolationProvider()
    {
        return [
            'User exceeds hard cap' => [
                [
                    'userCurrentSeasonCapTotal' => 70000,
                    'partnerCurrentSeasonCapTotal' => 50000,
                    'userCapSentToPartner' => 1000,
                    'partnerCapSentToUser' => 10000
                ],
                1 // Expected error count
            ],
            'Partner exceeds hard cap' => [
                [
                    'userCurrentSeasonCapTotal' => 50000,
                    'partnerCurrentSeasonCapTotal' => 70000,
                    'userCapSentToPartner' => 10000,
                    'partnerCapSentToUser' => 1000
                ],
                1 // Expected error count
            ]
        ];
    }

    /**
     * Data provider for non-tradeable players
     */
    public static function nonTradeablePlayerProvider()
    {
        return [
            'Waived player' => [
                [[60000, 5000]], // High ordinal (waived), has salary
                false,
                'Waived players should not be tradeable'
            ],
            'Player with no salary' => [
                [[1000, 0]], // Low ordinal, no salary
                false,
                'Players with zero salary should not be tradeable'
            ],
            'Nonexistent player' => [
                [], // No data returned
                false,
                'Nonexistent players should not be tradeable'
            ]
        ];
    }
}