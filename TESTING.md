# PHP Unit Testing Guide

This document provides comprehensive information about the PHP unit testing setup for this Laravel project.

## Overview

This project includes a complete test suite with:
- **Unit Tests**: Testing individual models and classes
- **Feature Tests**: Testing API endpoints and application features
- **Model Factories**: For generating test data
- **Test Helpers**: Utility functions for testing
- **Coverage Reports**: HTML and text coverage reports

## Test Structure

```
tests/
├── Feature/
│   ├── AuthTest.php              # Authentication endpoints
│   ├── ClientControllerTest.php  # Client CRUD operations
│   ├── EmployeeControllerTest.php # Employee CRUD operations
│   ├── PlacementControllerTest.php # Placement CRUD operations
│   └── ExampleTest.php           # Laravel default test
├── Unit/
│   ├── UserTest.php              # User model tests
│   ├── ClientTest.php            # Client model tests
│   ├── EmployeeTest.php          # Employee model tests
│   ├── PlacementTest.php         # Placement model tests
│   └── ExampleTest.php           # Laravel default test
├── TestCase.php                  # Base test class
├── TestHelpers.php               # Helper functions
└── CreatesApplication.php        # Laravel test bootstrap
```

## Model Factories

The following factories are available for generating test data:

- `UserFactory` - Creates users with proper JWT authentication support
- `ClientFactory` - Creates clients with all required fields
- `EmployeeFactory` - Creates employees with unique NIK/NIP
- `PlacementFactory` - Creates placements with proper relationships
- `PicExternalFactory` - Creates external PICs

## Running Tests

### Using the Test Runner Script

A convenient test runner script is provided:

```bash
# Run all tests
./run-tests.sh -a

# Run only unit tests
./run-tests.sh -u

# Run only feature tests
./run-tests.sh -f

# Run tests with coverage report
./run-tests.sh -c

# Run specific test file
./run-tests.sh -t UserTest

# Run tests verbosely
./run-tests.sh -v

# Combine options
./run-tests.sh -f -c -v  # Feature tests with coverage, verbose
```

### Using PHPUnit Directly

```bash
# Run all tests
./vendor/bin/phpunit

# Run unit tests only
./vendor/bin/phpunit --testsuite Unit

# Run feature tests only
./vendor/bin/phpunit --testsuite Feature

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage-html

# Run specific test
./vendor/bin/phpunit --filter UserTest

# Run specific method
./vendor/bin/phpunit --filter test_user_can_login
```

### Using Laravel Artisan

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter UserTest
```

## Test Configuration

### PHPUnit Configuration

The `phpunit.xml` file is configured with:
- SQLite in-memory database for fast testing
- Coverage reporting (HTML and text)
- Proper environment variables for testing
- JWT authentication settings

### Environment Variables

Test-specific environment variables are set in `phpunit.xml`:
- `APP_ENV=testing`
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=:memory:`
- `CACHE_DRIVER=array`
- `QUEUE_CONNECTION=sync`
- `JWT_SECRET=test-jwt-secret-key-for-testing`

## Test Types

### Unit Tests

Unit tests focus on testing individual models and their:
- **Relationships**: Testing Eloquent relationships
- **Attributes**: Testing fillable, hidden, and appended attributes
- **Methods**: Testing custom model methods
- **Validation**: Testing model validation rules
- **Soft Deletes**: Testing soft delete functionality

Example unit test structure:
```php
/** @test */
public function it_can_create_a_user()
{
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);

    $this->assertInstanceOf(User::class, $user);
    $this->assertEquals('John Doe', $user->name);
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
}
```

### Feature Tests

Feature tests focus on testing API endpoints and include:
- **Authentication**: Login, logout, registration, token refresh
- **CRUD Operations**: Create, Read, Update, Delete for all resources
- **Authorization**: Testing protected routes
- **Validation**: Testing request validation
- **Pagination**: Testing paginated responses
- **Search**: Testing search functionality

Example feature test structure:
```php
/** @test */
public function authenticated_user_can_create_client()
{
    $clientData = [
        'name' => 'Test Company',
        'email' => 'test@company.com'
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/clients', $clientData);

    $response->assertStatus(201)
            ->assertJson(['success' => true]);
}
```

## Test Helpers

The `TestHelpers` trait provides utility methods:

```php
// Create authenticated user
$auth = $this->createAuthenticatedUser();
$user = $auth['user'];
$token = $auth['token'];

// Make authenticated requests
$response = $this->authenticatedGet('/api/clients', $token);
$response = $this->authenticatedPost('/api/clients', $data, $token);

// Assert responses
$this->assertSuccessfulResponse($response);
$this->assertCreatedResponse($response, 'Client created successfully');
$this->assertValidationErrors($response, ['name', 'email']);
```

## Coverage Reports

### HTML Coverage Report

Run tests with coverage to generate an HTML report:
```bash
./run-tests.sh -c
```

The HTML report will be available at `./coverage-html/index.html`

### Text Coverage Report

A text coverage summary is also generated in `coverage.txt`

## Best Practices

### Writing Tests

1. **Use descriptive test names**: `test_user_can_login_with_valid_credentials`
2. **Follow AAA pattern**: Arrange, Act, Assert
3. **Test one thing per test**: Each test should verify one specific behavior
4. **Use factories**: Always use model factories instead of manual creation
5. **Clean database**: Use `RefreshDatabase` trait to ensure clean state

### Test Organization

1. **Group related tests**: Keep authentication tests together
2. **Use consistent naming**: Follow the `test_` prefix convention
3. **Document complex tests**: Add comments for complex test logic
4. **Test edge cases**: Include tests for error conditions and edge cases

### Performance

1. **Use in-memory database**: SQLite in-memory for fast tests
2. **Minimize database queries**: Use factories efficiently
3. **Parallel testing**: Consider using parallel testing for large test suites

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite
        
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
      
    - name: Run tests
      run: ./run-tests.sh -a -c
      
    - name: Upload coverage
      uses: codecov/codecov-action@v1
      with:
        file: ./coverage.xml
```

## Debugging Tests

### Common Issues

1. **Database not refreshed**: Ensure `RefreshDatabase` trait is used
2. **Authentication failures**: Check JWT secret and token generation
3. **Factory relationships**: Ensure all required relationships are properly set
4. **File uploads**: Use `Storage::fake()` for file upload tests

### Debugging Commands

```bash
# Run single test with debug output
./vendor/bin/phpunit --filter test_name --debug

# Show SQL queries in tests
DB_LOG_QUERIES=true ./vendor/bin/phpunit

# Run tests with verbose output
./run-tests.sh -v
```

## Contributing

When adding new features:

1. **Write tests first**: Follow TDD approach when possible
2. **Test all scenarios**: Include happy path, error cases, and edge cases
3. **Update factories**: Add or update factories for new models
4. **Document tests**: Update this guide when adding new test patterns
5. **Maintain coverage**: Aim for high test coverage on critical code

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [Laravel HTTP Tests](https://laravel.com/docs/http-tests)
- [Laravel Database Testing](https://laravel.com/docs/database-testing)