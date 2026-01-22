# Contributing to Flux Filemanager

Thank you for considering contributing to Flux Filemanager! This document outlines the process for contributing to this project.

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on what is best for the community

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce** the issue
- **Expected behavior** vs actual behavior
- **Environment details** (PHP version, Laravel version, etc.)
- **Code samples** if applicable

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Clear title and description**
- **Use case** - why would this be useful?
- **Possible implementation** if you have ideas

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Follow the coding standards** (PSR-12 for PHP, ESLint for JavaScript)
3. **Write tests** for new features
4. **Update documentation** if needed
5. **Ensure tests pass** by running `composer test`
6. **Write clear commit messages**

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR-USERNAME/livewire-flux-editor-filemanager.git

# Install dependencies
composer install
npm install

# Run tests
composer test

# Run tests with coverage
composer test-coverage
```

## Coding Standards

### PHP

- Follow **PSR-12** coding standard
- Use **type hints** for parameters and return types
- Write **PHPDoc blocks** for classes and methods
- Keep methods **focused and small**

### JavaScript

- Use **ES6+** syntax
- Write **JSDoc comments** for functions
- Use **descriptive variable names**
- Handle **errors gracefully**

### Blade

- Use **proper indentation** (4 spaces)
- Keep templates **clean and readable**
- Use **components** for reusable elements

## Testing

- Write tests for **new features**
- Ensure **existing tests pass**
- Aim for **high code coverage**
- Test both **happy paths and edge cases**

### Running Tests

```bash
# Run all tests
composer test

# Run specific test
composer test-filter EditorComponentTest

# Generate coverage report
composer test-coverage
```

## Documentation

- Update **README.md** for user-facing changes
- Update **WORKFLOW.md** for technical details
- Update **CHANGELOG.md** following [Keep a Changelog](https://keepachangelog.com/)
- Add **code comments** for complex logic

## Git Commit Messages

- Use the **present tense** ("Add feature" not "Added feature")
- Use the **imperative mood** ("Move cursor to..." not "Moves cursor to...")
- **Limit the first line** to 72 characters
- **Reference issues and pull requests** when applicable

### Examples

```
Add image align functionality

- Add left, center, right align buttons
- Update CSS for alignment classes
- Add tests for align functionality

Fixes #123
```

## Release Process

1. Update `CHANGELOG.md`
2. Update version in `composer.json` and `package.json`
3. Create a git tag
4. Push to GitHub
5. Create a GitHub release

## Questions?

Feel free to open an issue with your question or reach out to the maintainers.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
