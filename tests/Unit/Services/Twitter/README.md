# PKCEService Unit Tests

## Overview

This directory contains comprehensive unit tests for `App\Services\Twitter\PKCEService`, which implements the OAuth 2.0 PKCE (Proof Key for Code Exchange) flow for Twitter API authentication.

## Test Coverage

The `PKCEServiceTest.php` file includes 12 test cases covering all major functionality:

### PKCE Flow Generation Tests

- `test_generate_state_returns_random_string` - Verifies state generation with default length
- `test_generate_state_with_custom_length` - Tests state generation with custom length
- `test_generate_code_verifier_returns_valid_format` - Validates code verifier format
- `test_generate_code_challenge_from_verifier` - Tests SHA256 challenge computation
- `test_generate_authorize_url_with_correct_parameters` - Verifies authorization URL construction

### State Verification Tests

- `test_verify_state_success` - Tests successful state verification
- `test_verify_state_throws_exception_on_mismatch` - Validates exception on state mismatch

### Token Operations Tests

- `test_generate_token_success` - Tests initial token generation from auth code
- `test_refresh_token_success` - Validates token refresh flow
- `test_refresh_token_failure_throws_exception` - Tests error handling during refresh
- `test_revoke_token_success` - Verifies token revocation
- `test_revoke_token_handles_api_failure_gracefully` - Tests graceful handling of API failures

## Running the Tests

### Prerequisites

Ensure all dependencies are installed:

```bash
composer install
```

### Run All Unit Tests

```bash
php artisan test --testsuite=Unit
```

Or use PHPUnit directly:

```bash
vendor/bin/phpunit --testsuite=Unit
```

### Run Only PKCEService Tests

```bash
php artisan test --filter=PKCEServiceTest
```

Or:

```bash
vendor/bin/phpunit tests/Unit/Services/Twitter/PKCEServiceTest.php
```

### Run a Specific Test

```bash
php artisan test --filter=test_generate_token_success
```

## Test Patterns and Best Practices

### Mocking Dependencies

All external dependencies are mocked to ensure unit test isolation:

```php
$mockClient = $this->mock(Client::class, function (MockInterface $mock): void {
    $mock->expects('request')
        ->once()
        ->with(/* expected parameters */)
        ->andReturn(/* mocked response */);
});
```

### Time-Dependent Testing

Carbon is injected for time-dependent operations:

```php
$now = Carbon::parse('2024-01-01 00:00:00');
$sut = $this->getSUT($now, ...);
```

### Repository Mocking

OauthTokenRepository operations are mocked to avoid database dependencies:

```php
$mockRepository = $this->mock(OauthTokenRepository::class, function (MockInterface $mock): void {
    $mock->expects('updateOrCreate')
        ->once()
        ->with(/* expected parameters */)
        ->andReturn(/* mocked model */);
});
```

## Code Quality

### Syntax Validation

```bash
php -l tests/Unit/Services/Twitter/PKCEServiceTest.php
```

### Code Style (Pint)

```bash
composer run pint
```

### Static Analysis (PHPStan)

```bash
composer run phpstan
```

## Integration with CI/CD

These tests are automatically run as part of the CI/CD pipeline. Ensure all tests pass before merging:

```bash
composer run all  # Runs ide-helper, rector, phpstan, and pint
php artisan test --testsuite=Unit
```

## Troubleshooting

### Tests Not Found

If tests are not discovered, regenerate the autoload files:

```bash
composer dump-autoload
```

### Mockery Errors

Ensure Mockery is properly closed after each test (handled by `Tests\Unit\TestCase::tearDown()`).

### Carbon/Time Issues

All time-dependent tests use injected Carbon instances to ensure deterministic behavior.

## Contributing

When adding new test cases:

1. Follow the existing naming convention: `test_<method>_<scenario>`
2. Use the `getSUT()` helper method to create service instances
3. Mock all external dependencies
4. Add descriptive comments for complex assertions
5. Ensure tests are independent and can run in any order

## Related Files

- Source: `app/Services/Twitter/PKCEService.php`
- Exception: `app/Services/Twitter/Exceptions/InvalidStateException.php`
- Model: `app/Models/OauthToken.php`
- Repository: `app/Repositories/OauthTokenRepository.php`
