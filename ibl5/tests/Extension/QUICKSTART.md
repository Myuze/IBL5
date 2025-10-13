# Quick Start Guide - Extension Test Suite

## Running Tests

### Run All Extension Tests
```bash
cd ibl5
./vendor/bin/phpunit --testsuite="Extension Module Tests"
```

### Run Specific Test Class
```bash
# Validation tests only
./vendor/bin/phpunit tests/Extension/ExtensionValidationTest.php

# Evaluation tests only
./vendor/bin/phpunit tests/Extension/ExtensionOfferEvaluationTest.php

# Database tests only
./vendor/bin/phpunit tests/Extension/ExtensionDatabaseOperationsTest.php

# Integration tests only
./vendor/bin/phpunit tests/Extension/ExtensionIntegrationTest.php
```

### Run by Feature Group
```bash
# All validation tests
./vendor/bin/phpunit --group validation

# Raise validation only
./vendor/bin/phpunit --group raises

# Bird rights tests
./vendor/bin/phpunit --group bird-rights

# Database operations
./vendor/bin/phpunit --group database
```

### Run with Pretty Output
```bash
./vendor/bin/phpunit --testsuite="Extension Module Tests" --testdox --colors
```

## Test Groups Available

| Group | Description | Tests |
|-------|-------------|-------|
| `validation` | All validation tests | 23 |
| `zero-amounts` | Zero amount checks | 4 |
| `extension-usage` | Usage limit checks | 3 |
| `maximum-offer` | Max offer checks | 4 |
| `raises` | Raise validation | 6 |
| `salary-decrease` | Decrease validation | 5 |
| `offer-evaluation` | Evaluation logic | 13 |
| `modifiers` | Modifier calculations | 5 |
| `acceptance` | Accept/reject logic | 2 |
| `database` | Database operations | 11 |
| `integration` | Integration tests | 12 |

## Understanding Test Results

### ✅ Green = Passing
The test verified the expected behavior correctly.

### ❌ Red = Failing  
The test found unexpected behavior. Check:
1. Was modules/Player/extension.php modified?
2. Was a business rule changed?
3. Does the test need updating?

### ⚠️ Yellow = Skipped/Incomplete
The test is marked incomplete or skipped.

## Common Tasks

### Adding a New Test
```php
/**
 * @group validation
 * @group my-feature
 */
public function testMyNewValidationRule()
{
    // Arrange
    $offer = ['year1' => 1000, 'year2' => 1100, 'year3' => 1200, 'year4' => 0, 'year5' => 0];
    
    // Act
    $result = $this->extensionValidator->validateMyNewRule($offer);
    
    // Assert
    $this->assertTrue($result['valid']);
}
```

### Running Tests Before Commit
```bash
# Quick check (30ms)
./vendor/bin/phpunit --testsuite="Extension Module Tests"

# If all pass, commit
git add .
git commit -m "Your changes"
```

### Debugging a Failing Test
```bash
# Run with verbose output
./vendor/bin/phpunit tests/Extension/ExtensionValidationTest.php --testdox

# Run single test method
./vendor/bin/phpunit --filter testRejectsZeroAmountInYear1

# Check what queries were executed
# (Look in test code for $mockDb->getExecutedQueries())
```

## Test File Structure

```
tests/Extension/
├── ExtensionValidationTest.php          # Validation rules
├── ExtensionOfferEvaluationTest.php     # Offer evaluation
├── ExtensionDatabaseOperationsTest.php  # Database ops
├── ExtensionIntegrationTest.php         # End-to-end
├── README.md                            # Full documentation
├── CODE_REVIEW.md                       # Detailed analysis
├── FINAL_SUMMARY.md                     # Project summary
└── QUICKSTART.md                        # This file
```

## Key Business Rules Reference

### Maximum Offers by Experience
- 0-6 years: **1,063**
- 7-9 years: **1,275**
- 10+ years: **1,451**

### Raise Limits
- Without Bird Rights: **10%** per year
- With Bird Rights (3+ years): **12.5%** per year

### Contract Requirements
- Minimum length: **3 years**
- Maximum length: **5 years**
- First 3 years: **Must be non-zero**
- Years 4-5: **Can be zero**

### Extension Limits
- Per season: **1 successful extension**
- Per sim: **1 attempt** (successful or not)

## Helper Classes

Located in `classes/Extension/ExtensionTestHelpers.php`:

```php
// Validation
$validator = new ExtensionValidator($db);
$result = $validator->validateOfferAmounts($offer);

// Evaluation
$evaluator = new ExtensionOfferEvaluator($db);
$result = $evaluator->evaluateOffer($offer, $demands, $teamFactors, $playerPrefs);

// Database
$dbOps = new ExtensionDatabaseOperations($db);
$dbOps->updatePlayerContract($playerName, $offer, $currentSalary);

// Complete workflow
$processor = new ExtensionProcessor($db);
$result = $processor->processExtension($extensionData);
```

## Troubleshooting

### "Class not found" Error
```bash
# Make sure autoloader is working
composer dump-autoload
```

### "No tests executed"
```bash
# Check test suite name
./vendor/bin/phpunit --list-suites

# Should show: Extension Module Tests
```

### Tests Running Slow
```bash
# Tests should complete in ~0.033s
# If slower, check:
# 1. Is Xdebug enabled? (slows tests)
# 2. Are you hitting real database? (should be mocked)
```

## CI/CD Integration

### GitHub Actions Example
```yaml
- name: Run Extension Tests
  run: |
    cd ibl5
    ./vendor/bin/phpunit --testsuite="Extension Module Tests" --log-junit results.xml
```

### Jenkins Example
```groovy
stage('Test Extensions') {
    steps {
        sh 'cd ibl5 && ./vendor/bin/phpunit --testsuite="Extension Module Tests"'
    }
}
```

## Need More Info?

- **Usage Guide**: See `README.md`
- **Code Analysis**: See `CODE_REVIEW.md`
- **Project Overview**: See `FINAL_SUMMARY.md`
- **Original Code**: See `../../modules/Player/extension.php`
- **Main Entry Point**: See `../../modules/Player/index.php`

## Quick Reference Card

```
┌─────────────────────────────────────────────────┐
│  Extension Test Suite Quick Reference          │
├─────────────────────────────────────────────────┤
│  Total Tests:      59                          │
│  Test Classes:     4                           │
│  Execution Time:   ~0.033s                     │
│  Assertions:       151                         │
├─────────────────────────────────────────────────┤
│  Run All:                                      │
│  ./vendor/bin/phpunit --testsuite=            │
│    "Extension Module Tests"                    │
├─────────────────────────────────────────────────┤
│  Run by Group:                                 │
│  ./vendor/bin/phpunit --group validation      │
│  ./vendor/bin/phpunit --group database        │
├─────────────────────────────────────────────────┤
│  Pretty Output:                                │
│  ./vendor/bin/phpunit ... --testdox --colors  │
└─────────────────────────────────────────────────┘
```

---

**Last Updated**: October 2024  
**PHPUnit Version**: 12.4+  
**PHP Version**: 8.3+
