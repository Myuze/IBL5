# Depth Chart Entry Module - Refactoring Documentation

## Overview
The Depth Chart Entry module has been refactored to follow best practices for testability, maintainability, and separation of concerns.

## Recent Updates (2025)
**Offensive Sets and Position Restrictions Removed**
- The site no longer uses offensive sets (previously allowed 3 different offensive set configurations per team)
- Position restrictions have been removed - all players can now play at all positions regardless of their natural position
- Simplified user interface without offensive set selector
- Reduced complexity in validation and form rendering logic

## Architecture

### Namespace: `DepthChart\`

The module is now organized into the following classes:

#### 1. **DepthChartController**
- **Purpose**: Main entry point coordinating the depth chart entry workflow
- **Responsibilities**:
  - Handles user authentication and team retrieval
  - Orchestrates data retrieval and view rendering
  - Manages the form display flow

#### 2. **DepthChartRepository**
- **Purpose**: Handles all database operations
- **Responsibilities**:
  - Retrieves team player data
  - Updates player depth chart settings
  - Updates team history timestamps

#### 3. **DepthChartProcessor**
- **Purpose**: Processes and transforms depth chart data
- **Responsibilities**:
  - Processes form submission data
  - Counts active players and position depth
  - Detects invalid configurations (e.g., multiple starting positions)
  - Generates CSV export format

#### 4. **DepthChartValidator**
- **Purpose**: Validates depth chart submissions
- **Responsibilities**:
  - Validates active player counts
  - Validates position depth requirements
  - Checks for players starting at multiple positions
  - Adjusts validation rules based on season phase (Regular Season vs Playoffs)
  - Generates formatted error messages

#### 5. **DepthChartView**
- **Purpose**: Renders all HTML output for the module
- **Responsibilities**:
  - Renders form elements (dropdowns, inputs)
  - Renders player rows (all players can play at all positions)
  - Renders submission results

#### 6. **DepthChartSubmissionHandler**
- **Purpose**: Handles form submissions
- **Responsibilities**:
  - Orchestrates submission processing
  - Coordinates validation, database updates, and file operations
  - Sends email notifications
  - Displays submission results

## Benefits of Refactoring

### 1. **Testability**
- Each class has a single, well-defined responsibility
- Classes can be unit tested in isolation
- Mock objects can be injected for database operations
- 20 unit tests cover core validation and processing logic

### 2. **Maintainability**
- Clear separation of concerns makes code easier to understand
- Each class has focused responsibilities
- Changes to validation logic don't affect view rendering
- Database changes are isolated to the Repository class

### 3. **Readability**
- Descriptive class and method names
- Comprehensive PHPDoc documentation
- Logical organization of related functionality
- index.php reduced from 621 lines to 95 lines

### 4. **Extensibility**
- New validation rules can be added to DepthChartValidator
- View rendering can be customized without affecting business logic
- Database schema changes only require updates to Repository
- Easy to add new features without modifying existing code

## File Structure

```
ibl5/
├── modules/
│   └── Depth_Chart_Entry/
│       └── index.php (thin controller - 95 lines)
├── classes/
│   └── DepthChart/
│       ├── DepthChartController.php
│       ├── DepthChartProcessor.php
│       ├── DepthChartRepository.php
│       ├── DepthChartSubmissionHandler.php
│       ├── DepthChartValidator.php
│       └── DepthChartView.php
└── tests/
    └── DepthChart/
        ├── DepthChartProcessorTest.php
        └── DepthChartValidatorTest.php
```

## Testing

Run the Depth Chart tests:
```bash
vendor/bin/phpunit --testsuite="DepthChart Module Tests"
```

Run all tests:
```bash
vendor/bin/phpunit
```

### Test Coverage
- **20 tests** covering validation and processing logic
- Tests for regular season and playoff validation rules
- Tests for CSV generation
- Tests for detecting invalid configurations
- All tests passing (100% success rate)

## Usage

The refactored module maintains a simplified interface:

```php
// Entry point
modules.php?name=Depth_Chart_Entry

// Submission
POST to modules.php?name=Depth_Chart_Entry&op=submit
```

**Note**: Offensive set selection (useset parameter) has been removed as the site no longer uses offensive sets.

## Migration Notes

- **Simplified interface** - Offensive set selection removed
- Core depth chart functionality preserved
- Database schema unchanged (offense_sets table no longer queried)
- Form submissions work with simplified structure
- Email notifications continue to function without offensive set references
- Position restrictions removed - all players can now play at all positions

## Security Features

The refactored module includes comprehensive security improvements:

### SQL Injection Prevention
- **mysqli_real_escape_string**: All user input passed to SQL queries is properly escaped using `mysqli_real_escape_string()`
- **Type Casting**: Numeric values are cast to integers before use in queries
- **Parameterized Values**: All database queries use escaped and validated parameters

### Input Validation & Sanitization
- **Player Names**: HTML tags stripped, whitespace trimmed
- **Depth Values**: Validated to be between 0-5
- **Active Status**: Validated to be 0 or 1
- **Minutes**: Validated to be between 0-40
- **Focus Values**: Validated to be between 0-3
- **Setting Values**: Validated to be between -2 and 2
- **Team/Set Names**: HTML tags stripped, validated for non-empty

### Path Traversal Prevention
- **Filename Sanitization**: Team names sanitized to prevent directory traversal attacks
- **Path Validation**: File paths verified to be within expected directory
- **Character Filtering**: Only alphanumeric, spaces, underscores, and hyphens allowed in filenames

### Email Security
- **Header Injection Prevention**: Email subjects properly sanitized using `filter_var()`
- **Proper Headers**: RFC-compliant email headers used to prevent header injection

## Future Improvements

### Security
- Implement CSRF token validation for form submissions
- Add rate limiting for submission attempts
- Consider using prepared statements if database layer is upgraded

### Features
- Add drag-and-drop interface for depth chart management
- Implement depth chart history/versioning
- Add validation preview before submission
- Create REST API for mobile app integration

## Design Patterns Used

1. **Repository Pattern** - Abstracts database access
2. **MVC Pattern** - Separates Model, View, and Controller concerns
3. **Single Responsibility Principle** - Each class has one reason to change
4. **Dependency Injection** - Dependencies passed via constructor
5. **Factory Pattern** - Controller creates dependent objects

## Changes from Original Implementation

### Removed Features
- **Offensive Sets**: Previously allowed 3 different offensive set configurations (useset=1, useset=2, useset=3)
- **Position Restrictions**: Previously restricted which positions could play at which slots based on position value ranges

### Maintained Features
- Core depth chart submission and validation
- Same form field names for player settings
- Same validation rules for active players and position depth
- Same database updates and file operations
- Email notifications (without offensive set references)
