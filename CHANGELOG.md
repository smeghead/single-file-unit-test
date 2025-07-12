# ChangeLog

## v0.1.1 (2025-07-12)

 * Add `--generate-test-class` option for test template generation.
 * Support `--generate-test-class=ClassName` to generate specific test class.
 * Support `--generate-test-class` without value to generate ExampleTest.
 * Add `generateTestClass()` and `parseGenerateTestClassOption()` functions.
 * Update help text with new option documentation.
 * Implement feature using TDD approach with comprehensive test coverage.
 * Reduce barriers for beginners to start writing tests.

## v0.1.0 (2025-07-02)

 * Remove unused public methods from ColorSupport class (isNoColorSet, isTty, getTerm, isColorTermSet).
 * Clean up corresponding test methods in ColorSupportTest.
 * Improve code maintainability by keeping only essential methods.
 * Fixed ResultAccumulator to build summary strings.
 * Add FATAL error handling support for PHP 7+.
 * Add PHP 5.6 compatibility with Error class polyfill.
 * Prevent test process termination when FATAL errors occur.

## v0.0.3 (2025-07-01)

 * README rewritten in English.
 * Add Install section in README.

## v0.0.2 (2025-06-25)

 * fix to run as library error.

## v0.0.1 (2025-06-24)

 * add help option.
 * add version option.
 * add test result summary.

## v0.0.0 (2025-06-24)

 * Initial release