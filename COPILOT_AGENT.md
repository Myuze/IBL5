# Copilot Coding Agent Instructions for IBL5

## Overview
This repository uses the Copilot coding agent to automate code changes, improvements, and maintenance. Please follow these best practices to ensure smooth collaboration and effective automation.

## Engineering Philosophy

### Operate as an Architect-Level Engineer
- Approach every change with architectural thinking and long-term maintainability in mind
- Consider the broader system impact of all changes
- Write code that is clear, readable, maintainable, and extensible
- Prioritize code quality over speed of delivery
- Think about future developers who will work with this code

### Pull Request Standards
- **Clarity**: PRs must be easy to understand at a glance
- **Readability**: Code changes should follow consistent patterns and naming conventions
- **Maintainability**: Avoid clever code; prefer explicit, self-documenting approaches
- **Extensibility**: Design changes to accommodate future requirements
- **Completeness**: All tests must pass without warnings or failures before a PR is considered complete

## Codebase Architecture

### Technology Stack & Migration Goals
- **Current Foundation**: Built on PHP-Nuke legacy framework
- **Migration Target**: Gradually migrate to Laravel and/or TypeScript/Svelte/Vite stack
- **Database Current**: MySQL
- **Database Future**: Support PostgreSQL and implement ORM layer
- When refactoring, consider compatibility with future Laravel migration
- Write new code with modern PHP practices (namespaces, type hints, etc.)

### Key Directory Structure
```
ibl5/
├── classes/          # All class files (PSR-4 autoloaded)
├── modules/          # Feature modules (PHP-Nuke style)
├── tests/            # PHPUnit test suite (v12.4+)
└── mainfile.php      # Bootstrap file with class autoloader
```

### Class Autoloading
- **Location**: The class autoloader is defined in `mainfile.php`
- All classes should be placed in `ibl5/classes/` directory
- Follow PSR-4 autoloading conventions when creating new classes
- Use proper namespacing for new classes to facilitate future Laravel migration

### Testing Requirements
- **Framework**: PHPUnit 12.4+ compatibility required
- **Test Location**: All tests in `ibl5/tests/` directory
- **PR Completion Criteria**: No warnings or failures allowed
- Always run the full test suite before marking a PR as complete
- Add tests for new functionality
- Update tests when refactoring existing code

### Database Considerations
- Current queries use MySQL-specific syntax
- When writing new code, avoid MySQL-only features where possible
- Consider PostgreSQL compatibility for future migration
- Think about how queries could be converted to ORM patterns (Eloquent)
- Document any database schema assumptions or dependencies

## Best Practices

### 1. Use Clear, Actionable Pull Request Titles
- Start PR titles with a verb (e.g., "Add", "Fix", "Refactor", "Update")
- Be concise but descriptive (e.g., "Fix player stats calculation bug")
- Reference issue numbers when applicable

### 2. Write Descriptive Pull Request Descriptions
- Clearly explain the purpose and context of the change
- List any related issues or tickets
- Include testing or validation steps if relevant
- Document any breaking changes or migration steps
- Highlight architectural decisions made

### 3. Keep Pull Requests Focused
- Each PR should address a single logical change or feature
- Avoid mixing unrelated changes in one PR
- Break large refactors into smaller, reviewable chunks
- Consider the reviewer's experience when sizing PRs

### 4. Use Conventional Commits (Optional)
- If possible, use [Conventional Commits](https://www.conventionalcommits.org/) for commit messages
- Types: feat, fix, refactor, test, docs, chore, style

### 5. Review and Approve PRs Promptly
- Review Copilot-generated PRs for correctness and style
- Leave feedback or request changes as needed
- Verify all tests pass
- Approve and merge when ready

### 6. Code Quality Checklist
- [ ] Code follows existing patterns and conventions
- [ ] All tests pass without warnings or failures
- [ ] New functionality includes appropriate tests
- [ ] Code is self-documenting with clear variable/function names
- [ ] Complex logic includes explanatory comments
- [ ] No debugging code or commented-out blocks left behind
- [ ] Database queries consider PostgreSQL compatibility
- [ ] Changes support eventual Laravel migration where applicable

## Copilot Coding Agent Configuration

- The Copilot agent will:
  - Open PRs for code changes
  - Provide detailed PR descriptions and context
  - Respond to feedback and update PRs as needed
  - Run tests and ensure they pass before completing PRs
  - Consider architectural implications of all changes
- The agent will **not** merge PRs automatically; human review is required

## Additional Resources
- [Copilot Coding Agent Best Practices](https://gh.io/copilot-coding-agent-tips)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

_This file was generated to onboard the Copilot coding agent to this repository. Edit as needed to reflect your team's workflow._
