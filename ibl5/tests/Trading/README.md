# Trading Module Test Suite

This directory contains comprehensive unit tests for the Trading module's refactored classes, demonstrating modern PHP testing best practices.

## Fixed PHPUnit Compatibility Issues

**Issues Resolved**: 
1. PHPUnit 12+ no longer recognizes `@test` annotations by default → Updated test methods to use proper `test*` naming convention
2. PHPUnit 10+ requires data provider methods to be static → Made all data provider methods `static`
3. PHPUnit 12+ prefers attributes over annotations for data providers → Added `#[DataProvider]` attributes alongside `@dataProvider` annotations for maximum compatibility

## Testing Approach Comparison

### Old Approach (Player_Tests.php style)
The previous testing approach was basic and limited:
- Simple data array definitions
- No actual test execution
- No assertions or validation
- No test structure or organization
- Difficult to maintain and extend

```php
// Example of old approach
$playerOptionableFirstRoundRookieInFreeAgency = [
    'draftround' => 1,
    'exp' => 2,
    'cy4' => 0
];
// No actual testing - just data definitions
```

### New Approach (Modern PHPUnit)
The new testing approach follows modern best practices:

#### Key Features:
- **Proper test structure** with `setUp()` and `tearDown()` methods
- **Descriptive test names** that explain what's being tested (using `test*` naming convention)
- **Mock objects** for isolated unit testing
- **Data providers** for parametrized testing
- **Test groups** for organized test execution
- **Comprehensive assertions** with clear failure messages
- **Edge case testing** and error handling validation
- **PHPUnit version compatibility** (works with PHPUnit 8-12+)

#### Example Structure:
```php
class TradeValidatorTest extends TestCase
{
    private $validator;
    private $mockDb;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->validator = new Trading_TradeValidator($this->mockDb);
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
     * @dataProvider invalidCashAmountProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidCashAmountProvider')]
    public function testRejectsCashAmountsBelowMinimum($userCash, $partnerCash, $expectedErrorText)
    {
        // Test implementation
    }
    
    /**
     * Data provider methods must be static in PHPUnit 10+
     * Use both @dataProvider annotation and #[DataProvider] attribute for PHPUnit 8-12+ compatibility
     */
    public static function invalidCashAmountProvider()
    {
        return [
            'User cash below minimum' => [[...], [...], 'error message'],
            'Partner cash below minimum' => [[...], [...], 'error message']
        ];
    }
}
```

## Test Classes

### TradeValidatorTest.php
Tests for the `Trading_TradeValidator` class:
- Cash amount validation
- Salary cap validation 
- Player tradability checks
- Season-specific cash considerations

### CashTransactionHandlerModernTest.php
Tests for the `Trading_CashTransactionHandler` class:
- Unique PID generation
- Contract year calculations
- Cash transaction creation
- Database operations

## Running Tests

### Run all Trading tests:
```bash
phpunit tests/Trading/
```

### Run with detailed output:
```bash
phpunit --testdox tests/Trading/
```

### Run tests by group:
```bash
phpunit --group validation tests/Trading/
phpunit --group cash tests/Trading/
```

### Expected Output:
```
PHPUnit 12.3.15 by Sebastian Bergmann and contributors.

Cash Transaction Handler Modern
 ✔ Generates unique pid when requested pid is available
 ✔ Calculates contract total years correctly with front_loaded_contract
 ✔ Detects cash presence in trade accurately
 ✔ Creates cash transaction with proper story text
 [... more tests ...]

Trade Validator Modern
 ✔ Validates minimum cash amounts successfully
 ✔ Rejects cash amounts below minimum
 ✔ Validates salary caps within limits
 [... more tests ...]

Time: 75 ms, Memory: 15.14 MB
OK (24 tests, 50 assertions)
```

## Compatibility

- **PHPUnit Versions**: Compatible with PHPUnit 8.5+ through 12.3+
- **PHP Versions**: Works with PHP 7.4+ and PHP 8.x
- **Test Method Naming**: Uses `test*` method names for maximum compatibility
- **No Schema Dependencies**: Simple XML configuration works across versions

## Benefits of Modern Testing

1. **Reliability**: Tests actually execute and validate behavior
2. **Maintainability**: Clear structure makes tests easy to update
3. **Documentation**: Tests serve as executable documentation
4. **Confidence**: Comprehensive coverage ensures code quality
5. **Regression Prevention**: Automated tests catch breaking changes
6. **Debugging**: Detailed assertions help identify issues quickly
7. **Version Compatibility**: Works across multiple PHPUnit versions

## Mock Objects

The tests use sophisticated mock objects to:
- Isolate units under test from dependencies
- Control test data and scenarios
- Simulate database operations without actual database calls
- Test error conditions and edge cases

This approach ensures tests are:
- **Fast**: No database I/O operations
- **Reliable**: Consistent test data
- **Isolated**: Each test is independent
- **Comprehensive**: Can test all code paths including error scenarios