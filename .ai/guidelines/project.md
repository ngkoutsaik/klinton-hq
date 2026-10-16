# Project conventions

## Running commands

The app runs in Laravel Sail. Run PHP, Artisan and Composer through it, not the host:

- Tests: `vendor/bin/sail composer test`
- Format: `vendor/bin/sail composer fix`
- Static checks (Pint, PHPMD, PHPStan), same as CI: `vendor/bin/sail composer check`

## Files

- Never delete a file you didn't create in the current task, in any permission mode, unless the user explicitly tells you to delete that file. This applies even more to files outside the project.
- Never create files outside the project directory.
- Put generic output that doesn't belong in the codebase (patches, images, exports, scratch files) in `storage/app/agent/`. It's gitignored.

## Facades over helpers

Use facades wherever one exists, in app code, tests and Blade views. Import the facade with `use` instead of calling the root alias (`\Config::get`).

- `Config::get()` not `config()`, `View::make()` not `view()`, `Response::make()` not `response()`
- `URL::route()` / `URL::to()` not `route()` / `url()`
- `App::make()` not `app()`, `Auth::user()` not `auth()`, `Date::now()` not `now()`, `App::abort()` not `abort()`

Exceptions:

- `config/*.php`: facades aren't registered yet, so `env()`, `storage_path()` etc. stay.
- Helpers with no facade: `blank`, `filled`, `collect`, `data_get`, `abort_if`, `abort_unless`.
- Blade directives such as `@vite`.

Older code still uses helpers. Don't convert it as part of unrelated work.

## Comments and PHPDoc

- Inline comments only for code that is surprising: a workaround, a non-obvious constraint, a gotcha.
- Native types first. Add PHPDoc only for what PHP can't express (generics like `HasMany<ResumeEntry, $this>`, iterable value types like `array<Component>`, array shapes). Larastan runs at level 6 and needs them.
- PHPDoc descriptions only when the name and signature don't tell the whole story: fallbacks, side effects, thrown exceptions.
- Leave framework stub comments ("Run the migrations.") as generated.
- Don't add comments or descriptions just for consistency.

## Branches and pull requests

- A branch contains only changes for its purpose. Unrelated fixes go on their own branch, unless the change can't work without them.
- No AI attribution in commits or PRs: no `Co-Authored-By` trailers, no "Generated with" lines.
