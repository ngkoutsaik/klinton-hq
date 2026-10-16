---
name: check
description: Run the project's quality checks (Pint, PHPMD, PHPStan) and tests the same way CI does, and fix what fails. Use before committing, before opening a PR, or when asked to check, lint or verify the code.
---

# Check

Sail must be running (`vendor/bin/sail up -d`).

Run the same two steps as CI: the GrumPHP suite (Composer validation, Pint, PHPMD, PHPStan), then the tests.

```bash
vendor/bin/sail composer check
vendor/bin/sail composer test
```

To narrow down a failure, run the individual tools:

| Tool | Command | Fix |
|---|---|---|
| Pint (style) | `vendor/bin/sail composer lint` | `vendor/bin/sail composer fix` |
| PHPMD | `vendor/bin/sail composer lint` | Fix by hand, using the rules in `phpmd.xml` |
| PHPStan (Larastan, level 6) | `vendor/bin/sail composer analyse` | Fix by hand |
| Tests | `vendor/bin/sail composer test` | Fix by hand |
| Dependency audit | `vendor/bin/sail composer audit --locked` | Update only the affected package, on its own branch |

## Rules

- Fix the cause, not the check. Don't add PHPStan baseline entries, `@phpstan-ignore` or `@SuppressWarnings` unless the user agrees.
- PHPStan errors about missing iterable or generic types are fixed with PHPDoc (`array<int, Resume>`, `HasMany<ResumeEntry, $this>`).
- GrumPHP's `git_blacklist` task fails on `dd(`, `dump(`, `var_dump(`, `print_r(` and `ray(` in PHP files. Remove the debug call.
- If a failure is unrelated to the current branch (for example an audit advisory already on `main`), tell the user instead of fixing it here.
- Re-run both commands once after fixing, and report the result.
