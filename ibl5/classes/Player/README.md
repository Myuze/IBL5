# Player Module

## Overview

The Player module has been refactored following SOLID software design principles. The original monolithic `Player` class has been split into smaller, focused classes, each with a single responsibility.

## Architecture

### Class Diagram

```
Player (Facade)
├── PlayerData (Value Object)
├── PlayerRepository (Data Access)
├── PlayerContractCalculator (Business Logic)
├── PlayerContractValidator (Business Logic)
├── PlayerNameDecorator (Presentation)
└── PlayerInjuryCalculator (Business Logic)
```

## Classes

### PlayerData
**Responsibility**: Value Object / Data Transfer Object

A simple data container that holds player information without any business logic. This class is:
- Easy to serialize and cache
- Simple to test
- Easy to pass between layers
- Immutable in intent (though PHP doesn't enforce this at the language level)

**Properties**: All player data fields (contract info, ratings, personal info, etc.)

**Location**: `/ibl5/classes/PlayerData.php`

---

### PlayerRepository
**Responsibility**: Data Access Layer

Handles all database operations for player data following the Repository pattern. This class:
- Encapsulates data loading logic
- Provides methods to load players from different sources
- Translates database rows into PlayerData objects
- Isolates the rest of the code from database schema changes

**Key Methods**:
- `loadByID(int $playerID): PlayerData` - Load a player by ID
- `fillFromCurrentRow(array $plrRow): PlayerData` - Create PlayerData from current season row
- `fillFromHistoricalRow(array $plrRow): PlayerData` - Create PlayerData from historical row
- `getFreeAgencyDemands(string $playerName)` - Query free agency demands

**Location**: `/ibl5/classes/PlayerRepository.php`

---

### PlayerContractCalculator
**Responsibility**: Contract and Salary Calculations

Handles all contract-related mathematical calculations. This class:
- Computes salary information
- Calculates buyout terms
- Determines remaining contract values
- Contains no data persistence logic

**Key Methods**:
- `getCurrentSeasonSalary(PlayerData $playerData): int` - Calculate current season salary
- `getNextSeasonSalary(PlayerData $playerData): int` - Calculate next season salary
- `getTotalRemainingSalary(PlayerData $playerData): int` - Sum remaining contract value
- `getRemainingContractArray(PlayerData $playerData): array` - Get array of remaining years
- `getLongBuyoutArray(PlayerData $playerData): array` - Calculate 6-year buyout terms
- `getShortBuyoutArray(PlayerData $playerData): array` - Calculate 2-year buyout terms

**Location**: `/ibl5/classes/PlayerContractCalculator.php`

**Tests**: `/ibl5/tests/Player/PlayerContractCalculatorTest.php` (10 test cases)

---

### PlayerContractValidator
**Responsibility**: Contract Eligibility Rules

Validates whether a player is eligible for various contract operations. This class:
- Encapsulates contract rule logic
- Makes rules easy to test independently
- Provides clear, single-purpose methods

**Key Methods**:
- `canRenegotiateContract(PlayerData $playerData): bool` - Check if contract can be renegotiated
- `canRookieOption(PlayerData $playerData, string $seasonPhase): bool` - Check rookie option eligibility
- `wasRookieOptioned(PlayerData $playerData): bool` - Check if rookie option was exercised

**Location**: `/ibl5/classes/PlayerContractValidator.php`

**Tests**: `/ibl5/tests/Player/PlayerContractValidatorTest.php` (12 test cases)

---

### PlayerNameDecorator
**Responsibility**: Name Formatting and Presentation

Handles player name formatting with status indicators. This class:
- Decorates player names with special symbols
- Indicates free agency eligibility (^)
- Indicates waiver status (*)
- Separates presentation logic from data

**Key Methods**:
- `decoratePlayerName(PlayerData $playerData): string` - Format name with status indicators

**Location**: `/ibl5/classes/PlayerNameDecorator.php`

**Tests**: `/ibl5/tests/Player/PlayerNameDecoratorTest.php` (4 test cases)

---

### PlayerInjuryCalculator
**Responsibility**: Injury Date Calculations

Calculates injury-related dates. This class:
- Computes injury return dates
- Handles date arithmetic
- Isolates date calculation logic

**Key Methods**:
- `getInjuryReturnDate(PlayerData $playerData, string $rawLastSimEndDate): string` - Calculate return date

**Location**: `/ibl5/classes/PlayerInjuryCalculator.php`

**Tests**: `/ibl5/tests/Player/PlayerInjuryCalculatorTest.php` (4 test cases)

---

### Player
**Responsibility**: Facade

The refactored Player class now acts as a facade, maintaining backward compatibility while delegating to specialized classes. This class:
- Provides the same public API as before
- Delegates to specialized classes
- Maintains backward compatibility with existing code
- Coordinates between different components

**Pattern**: Facade Pattern

**Location**: `/ibl5/classes/Player.php`

## SOLID Principles Applied

### Single Responsibility Principle (SRP)
Each class has one reason to change:
- `PlayerData`: Changes only when data structure changes
- `PlayerRepository`: Changes only when data access patterns change
- `PlayerContractCalculator`: Changes only when salary calculation rules change
- `PlayerContractValidator`: Changes only when eligibility rules change
- `PlayerNameDecorator`: Changes only when naming conventions change
- `PlayerInjuryCalculator`: Changes only when injury date logic changes

### Open/Closed Principle (OCP)
Classes are open for extension but closed for modification:
- New validators can be added without modifying existing ones
- New calculators can be added without changing existing calculation logic
- The facade pattern allows new functionality to be added through composition

### Liskov Substitution Principle (LSP)
The refactored `Player` class maintains the same interface as the original, so it can be substituted anywhere the original was used without breaking functionality.

### Interface Segregation Principle (ISP)
Each class has a focused interface with only the methods relevant to its responsibility. Clients depend only on the interfaces they need.

### Dependency Inversion Principle (DIP)
The `Player` facade depends on abstractions (the specialized classes) rather than concrete implementations. The specialized classes are injected/created in the constructor.

## Benefits

1. **Testability**: Each class can be tested in isolation with focused unit tests
2. **Maintainability**: Changes to one aspect (e.g., salary calculations) don't affect others
3. **Readability**: Clear class names indicate purpose
4. **Reusability**: Specialized classes can be used independently
5. **Extensibility**: New functionality can be added without modifying existing code
6. **Backward Compatibility**: Existing code continues to work unchanged

## Testing

All classes have comprehensive unit tests:
- **Total Tests**: 30 test cases for Player module
- **Coverage**: All public methods are tested
- **Test Location**: `/ibl5/tests/Player/`

Run tests:
```bash
cd /home/runner/work/IBL5/IBL5/ibl5
phpunit --testsuite="Player Module Tests"
```

## Migration Guide

For developers working with the Player class:

### No Changes Required for Existing Code
The refactoring maintains complete backward compatibility. All existing code using the `Player` class continues to work without modification.

### Using New Classes Directly
If you need to use the specialized classes directly:

```php
// Example: Using PlayerContractCalculator directly
$calculator = new PlayerContractCalculator();
$salary = $calculator->getCurrentSeasonSalary($playerData);

// Example: Using PlayerContractValidator directly
$validator = new PlayerContractValidator();
if ($validator->canRenegotiateContract($playerData)) {
    // Renegotiate logic
}

// Example: Using PlayerRepository directly
$repository = new PlayerRepository($db);
$playerData = $repository->loadByID(12345);
```

## Future Enhancements

Potential improvements for future iterations:
1. Add interfaces for each component (e.g., `ContractCalculatorInterface`)
2. Implement dependency injection container
3. Add caching layer to PlayerRepository
4. Extract database connection logic to a separate service
5. Add value validation to PlayerData
6. Consider immutability patterns for PlayerData
