# Wakatime Stats – Improvement Tasks

Generated on: 2025-09-10 00:14

1. [ ] Immediately rotate and revoke any leaked credentials committed to the repo (WAKATIME_API_KEY, WAKATIME_USER_ID, GH_TOKEN); purge secrets from git history if applicable. 
2. [ ] Add .env to .gitignore and provide a sanitized .env.example with documented variables and defaults. 
3. [ ] Introduce strict types across the codebase by adding `declare(strict_types=1);` to all PHP files. 
4. [ ] Standardize configuration: create a Config class to read and validate inputs from GitHub Action env (INPUT_*) and .env, with consistent precedence and defaults. 
5. [ ] Fix repository validation in src/stats.php: remove the incorrect check enforcing owner == repo name; validate format `<owner>/<repo>` only. 
6. [ ] Refactor src/stats.php to a Symfony Console command or an Application service to separate orchestration from script glue code. 
7. [ ] Add return types, parameter types, and PHPDoc where needed in all classes (WakatimeDataFetcher, WakatimeStatsDataProcessor, GitHubStatsUpdater, etc.). 
8. [ ] WakatimeDataFetcher: inject Guzzle client via constructor (optional factory) to enable testability and easier configuration. 
9. [ ] WakatimeDataFetcher: set sensible HTTP options (timeout, connect_timeout, retries with backoff, User-Agent header). 
10. [ ] WakatimeDataFetcher: handle non-2xx responses and JSON decode errors; throw domain-specific exceptions with context. 
11. [ ] WakatimeDataFetcher: either implement fetchProjectStats and fetchCommitStats or remove these unused stubs to avoid dead code. 
12. [ ] WakatimeDataFetcher: remove or fix getReadableRange (currently uses unset $this->range); alternatively, accept a range param and/or set the range during fetch. 
13. [ ] Introduce a typed value object/Enum for time ranges (PHP 8.1+), replacing magic strings ('last_7_days', 'all_time', etc.). 
14. [ ] WakatimeStatsDataProcessor: add null/array-key checks and guard against missing keys in Wakatime API responses. 
15. [ ] WakatimeStatsDataProcessor: ensure numeric validation for MAX_LANGUAGES input is correct (cast after validation) and add upper bound if needed. 
16. [ ] WakatimeStatsDataProcessor: extract presentation logic (table formatting) from data transformation; return DTOs for easier testing. 
17. [ ] WakatimeStatsDataProcessor: support configurable column widths and optional localization of headers. 
18. [ ] GitHubStatsUpdater: avoid echo/exit inside library code; return result objects/booleans and let the entrypoint handle process exit. 
19. [ ] GitHubStatsUpdater: add retries/backoff for GitHub API calls and handle rate limiting (HTTP 403 with X-RateLimit headers). 
20. [ ] GitHubStatsUpdater: verify and set appropriate Accept headers and API version; handle ETag/If-Match to avoid overwriting concurrent changes. 
21. [ ] GitHubStatsUpdater: validate README markers more robustly; provide clear error if markers are missing and optionally support configurable markers. 
22. [ ] Centralize constants (marker strings, API URLs, header names) to avoid duplication and typos. 
23. [ ] Introduce a domain-specific exception hierarchy (e.g., ConfigException, NetworkException, ApiException, ProcessingException). 
24. [ ] Add PSR-3 logging (e.g., monolog/monolog) and emit structured logs at key steps with a LOG_LEVEL input to control verbosity. 
25. [ ] Add comprehensive unit tests:
    - WakatimeDataFetcher (range validation, error handling, JSON parsing, retries via mocked client).
    - WakatimeStatsDataProcessor (table generation for each category, limits, edge cases like unknown editors, missing keys).
    - GitHubStatsUpdater (marker replacement logic, request building; mock HTTP).
26. [ ] Add integration tests using a mocked HTTP server (e.g., httpmock/guzzle) for end-to-end flow without external calls. 
27. [x] Configure PHPUnit and add a phpunit.xml.dist with coverage configuration. 
28. [x] Add static analysis (PHPStan or Psalm) and fix reported issues; target at least PHPStan level 8. 
29. [x] Add coding standards (friendsofphp/php-cs-fixer or squizlabs/php_codesniffer) with a project ruleset; provide composer scripts to run it. 
30. [x] Add Composer scripts: `lint`, `test`, `analyze`, `fix` to standardize local workflows. 
31. [x] Set up GitHub Actions CI workflow to run on PRs: composer install with cache, lint, static analysis, tests on PHP 8.1/8.2. 
32. [ ] Add Dependabot (or Renovate) configuration for Composer and GitHub Actions updates. 
33. [ ] Improve Dockerfile: pin image version (e.g., php:8.2-cli-alpine), use multi-stage build, cache Composer deps, set WORKDIR, use non-root user, and minimize layers. 
34. [ ] Dockerfile: add healthcheck and define ARGs/ENV for optional inputs; avoid installing unnecessary packages. 
35. [ ] Document all inputs in README (including optional ones like TABLE_STYLE, MAX_LANGUAGES, WAKATIME_TIME_RANGE) with valid values; fix typos (e.g., `vmaster` -> `master`). 
36. [ ] Update README with troubleshooting section (rate limiting, missing markers, invalid tokens, Wakatime delays). 
37. [ ] Provide a sample repository snippet in README showing correct marker placement and example output. 
38. [ ] Add CONTRIBUTING.md, CODE_OF_CONDUCT.md, and issue/PR templates to streamline contributions. 
39. [ ] Add SECURITY.md with instructions for reporting vulnerabilities and the credentials policy. 
40. [ ] Adopt semantic versioning and add a CHANGELOG.md; create release workflow to tag versions after CI passes. 
41. [ ] Evaluate raising minimum PHP version to 8.1+ to leverage Enums, readonly properties, and stricter typing. 
42. [ ] Add configurable dry-run mode to skip pushing changes to GitHub and instead print the resulting README diff. 
43. [ ] Support custom README path or multiple files (optional input) for mono-repos. 
44. [ ] Add simple caching (with TTL) for Wakatime stats to reduce API calls during repeated runs in short intervals. 
45. [ ] Add metrics/telemetry (optional) such as timing and counts, emitted as logs for observability. 
46. [ ] Ensure Unicode and wide-character rendering is handled in table output to avoid misalignment. 
47. [ ] Add resilience to network flakiness: exponential backoff with jitter, and circuit-breaker pattern if repeated failures occur. 
48. [ ] Review and improve error messages to be actionable; include next steps or links to docs where appropriate. 
49. [ ] Create a small façade service (e.g., StatsUpdateService) that orchestrates fetch -> process -> update, to simplify stats.php and improve testability. 
50. [ ] Add type-safe DTOs for Wakatime response segments (languages, editors, operating systems) and map API responses into them. 
51. [ ] Validate and sanitize all external inputs (env vars) early, with clear errors (e.g., empty values, invalid ranges). 
52. [ ] Add rate-limit aware sleep between Wakatime and GitHub API calls if limits are near exhaustion. 
53. [ ] Add configurable logging of raw API responses (redacted) under a DEBUG flag for troubleshooting. 
54. [ ] Implement graceful fallback if Wakatime data is not up to date: either skip update with a clear message or show last known stats with a warning. 
55. [ ] Provide a Makefile or task runner instructions (optional) to simplify local dev: `make install`, `make test`, etc. 
56. [ ] Add CODEOWNERS to define maintainers and review requirements. 
