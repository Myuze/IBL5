# UpdateAllTheThings Test Suite

## Overview

This directory contains a comprehensive PHPUnit test suite for `ibl5/updateAllTheThings.php` and its related classes. These tests verify large-scale post-sim update functionality, ensuring that schedule updates, standings calculations, power rankings, and HTML generation all work correctly together.

## Test Structure

The test suite is organized into 5 test files covering all aspects of the update workflow:

### 1. ScheduleUpdaterTest.php (13 tests)
Tests schedule update functionality:
- **Date Extraction** (7 tests): Tests date parsing for different season phases (Preseason, HEAT, Regular Season), Post-to-June conversion, and date formatting
- **Box ID Extraction** (3 tests): Tests extraction of box score IDs from HTML links
- **Database Operations** (1 test): Verifies schedule table truncation
- **Team Resolution** (1 test): Verifies team ID resolution via Shared functions
- **Constructor** (1 test): Verifies proper initialization

### 2. StandingsUpdaterTest.php (21 tests)
Tests standings update functionality:
- **Record Parsing** (9 tests): Tests extraction of wins and losses from various record formats (single digit, double digit, mixed, zeros, perfect records)
- **Grouping Assignment** (7 tests): Tests proper grouping for conferences (Eastern, Western) and divisions (Atlantic, Central, Midwest, Pacific)
- **Magic Number Calculation** (2 tests): Tests magic number calculation and update logic
- **Database Operations** (1 test): Verifies standings table truncation
- **Constructor** (1 test): Verifies proper initialization
- **Validation** (1 test): Ensures grouping arrays have correct structure

### 3. PowerRankingsUpdaterTest.php (17 tests)
Tests power rankings update functionality:
- **Month Determination** (3 tests): Tests month selection for different phases (Preseason, HEAT, Regular Season)
- **Query Building** (1 test): Verifies games query construction
- **Stats Calculation** (10 tests): Tests calculation of wins, losses, home/away records, streaks, last 10 games, and tie handling
- **Ranking Calculation** (1 test): Tests ranking score computation
- **Database Operations** (1 test): Verifies depth chart reset
- **Season Records** (1 test): Tests HEAT season records update
- **Constructor** (1 test): Verifies proper initialization

### 4. StandingsHTMLGeneratorTest.php (19 tests)
Tests HTML generation functionality:
- **Grouping Assignment** (5 tests): Tests proper grouping for conferences and divisions
- **Header Generation** (5 tests): Tests HTML header generation with required columns, region names, sortable tables
- **Clinched Status** (3 tests): Tests display of X, Y, Z indicators for playoff, division, and conference clinching
- **Team Rows** (5 tests): Tests team row generation including last 10 games, streaks, and team links
- **Constructor** (1 test): Verifies proper initialization

### 5. UpdateAllTheThingsIntegrationTest.php (19 tests)
Integration tests for complete workflow:
- **Component Initialization** (5 tests): Tests that all updater components can be instantiated
- **Extension Reset** (1 test): Tests contract extension attempt reset
- **Database Operations** (2 tests): Tests query tracking and mock data handling
- **Season Phases** (3 tests): Tests components work with all season phases
- **Shared Functions** (2 tests): Tests team ID resolution
- **Season Data** (2 tests): Tests season data accessibility and constants
- **League Constants** (1 test): Tests League class constants
- **UI Class** (1 test): Tests UI debug output
- **Complete Workflow** (2 tests): Tests end-to-end workflow execution

## Running the Tests

### Run the complete UpdateAllTheThings test suite:
```bash
cd ibl5
./vendor/bin/phpunit --testsuite "UpdateAllTheThings Tests"
```

### Run tests with code coverage:
```bash
./vendor/bin/phpunit --testsuite "UpdateAllTheThings Tests" --coverage-html coverage/
```

### Run specific test file:
```bash
./vendor/bin/phpunit tests/UpdateAllTheThings/ScheduleUpdaterTest.php
```

### Run tests by group:
```bash
# Run all schedule updater tests
./vendor/bin/phpunit --group schedule-updater

# Run all date extraction tests
./vendor/bin/phpunit --group date-extraction

# Run all integration tests
./vendor/bin/phpunit --group integration
```

## Test Groups

Tests are organized with the following groups:
- `schedule-updater`: Schedule update functionality
- `standings-updater`: Standings update functionality  
- `power-rankings`: Power rankings update functionality
- `standings-html`: HTML generation functionality
- `integration`: Integration tests
- `date-extraction`, `box-id`, `record-parsing`, `grouping`, `magic-numbers`, `stats-calculation`, `clinched-status`, `html-generation`, etc.: Specific feature groups

## Mock Objects

The test suite uses mock objects defined in `tests/bootstrap.php`:

### MockDatabase
- Simulates SQL queries without database connection
- Tracks executed queries for verification
- Returns configurable mock data
- Supports both result sets and boolean returns

### Mock Classes
- **Shared**: Provides team ID resolution and extension reset functionality
- **Season**: Provides season phase, dates, and constants
- **UI**: Suppresses debug output during tests
- **League**: Provides conference/division names and constants

## Key Business Rules Tested

### Schedule Updates
1. Date parsing handles Preseason, HEAT, and Regular Season phases
2. Post-season dates are converted to June
3. Box IDs are extracted from HTML links
4. Schedule table is truncated before updates
5. Team IDs are resolved via Shared functions

### Standings Updates
1. Win-loss records are parsed from various formats
2. Regions are properly assigned to conferences or divisions
3. Magic numbers are calculated for playoff clinching
4. Games back is calculated correctly
5. Division and conference clinching is detected

### Power Rankings Updates
1. Month is determined based on season phase
2. Team stats track wins, losses, home/away records
3. Winning and losing streaks are tracked
4. Last 10 games are tracked separately
5. Ranking scores are calculated based on wins and opponent strength
6. Season and HEAT records are updated
7. Depth chart status is reset after sim

### HTML Generation
1. Conference and division standings are generated
2. Clinched status indicators (X, Y, Z) are displayed
3. Last 10 games and current streaks are shown
4. Team links are properly formatted
5. Standings are sortable

### Integration
1. All components can be initialized together
2. Components work with all season phases
3. Shared functions provide team resolution
4. Extension attempts are reset post-sim
5. Database operations are tracked

## Test Coverage

The test suite provides comprehensive coverage:
- **89 tests** total
- **290 assertions**
- Tests all public and key private methods
- Tests normal cases, edge cases, and error conditions
- Tests integration between components

## Expected Warnings

The test suite produces some warnings from tests that attempt to call `update()` methods which try to load HTML files. These are expected because:
1. The HTML files don't exist in the test environment
2. These tests verify that database operations (TRUNCATE) are attempted before file loading fails
3. Full update testing requires actual HTML files and is beyond the scope of unit testing

These warnings do not indicate test failures - they confirm that the tests are properly isolated from file system dependencies.

## Dependencies

- PHP 8.3+
- PHPUnit 12.4+
- Mock classes defined in `tests/bootstrap.php`

## Contributing

When adding new tests:
1. Follow the existing test structure and naming conventions
2. Use descriptive test method names starting with `test`
3. Add appropriate `@group` annotations for test organization
4. Document complex test scenarios with comments
5. Ensure tests are isolated and don't depend on execution order
6. Use mock objects instead of real database connections

## Notes

- Tests use Reflection to access private methods for thorough testing
- Output buffering (`ob_start`/`ob_end_clean`) is used to suppress echo statements
- The test suite is designed to work without actual HTML files or database connections
- Mock database tracks all executed queries for verification
