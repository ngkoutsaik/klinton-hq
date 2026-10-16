# AI tooling

The repo is set up for AI coding agents, mainly [Claude Code](https://code.claude.com). None of it is needed to run
the app, and none of it ships to production.

- **[Laravel Boost](https://laravel.com/docs/boost)** (dev only) gives agents an MCP server for the routes, database
  schema, logs and version-specific Laravel and Filament docs. It's configured in `boost.json` and `.mcp.json`, and
  runs through Sail, so Sail needs to be up.
- **Project rules** live in `.ai/guidelines/project.md`: running commands through Sail, facades over helpers,
  comments, branch scope and which files an agent may delete or create. Boost combines them with its own Laravel
  guidelines into `CLAUDE.md`. Edit the guidelines file, not `CLAUDE.md`, and run
  `./vendor/bin/sail artisan boost:update` to regenerate it.
- **Skills** in `.claude/skills/`: `check` runs the same checks as CI, and `pr-description` writes a pull request
  description in the repo's format. Boost's own skills (Laravel, Filament, testing, Tailwind) are gitignored and
  rebuilt on every `composer update`. After a fresh clone, run `./vendor/bin/sail artisan boost:update` once to
  install them.
- **Settings** in `.claude/settings.json`: an allowlist of safe commands, no reading `.env` files, and a hook plus a
  sandbox that are described below. Put personal overrides in `.claude/settings.local.json`, which is gitignored.

## Guardrails

Written rules only work if the agent follows them, so two things enforce them:

- **A hook** (`.claude/hooks/guard-files.sh`) checks every tool call. It blocks file writes outside the project, and
  makes any shell command that deletes or discards files (`rm`, `find -delete`, `git clean`, `git reset --hard`,
  `git restore` and similar) ask for confirmation, even in auto mode. The only exception is the agent's scratch
  folder, `storage/app/agent/`. The hook needs [`jq`](https://jqlang.org) on the host.
- **The Claude Code sandbox** limits shell commands to writing inside the project, at the OS level. Sail commands
  (`vendor/bin/sail ...`) are excluded, because on Linux the sandbox blocks the Docker socket that Sail needs.

> [!WARNING]
> Excluding Sail means an agent can get around the sandbox through Docker. Access to Docker is effectively root
> access: a command like `vendor/bin/sail docker run -v /:/host ...` can read and write anything on the machine, and
> so can a change to `compose.yaml` or `vendor/bin/sail` followed by a Sail command. Nothing in the sandbox can
> prevent that. What limits it in practice:
>
> - Only a few Sail commands (tests, lint, analyse, check, audit, Pint, `route:list`) are pre-approved. Any other
>   Sail command asks for confirmation, or goes through the auto-mode safety check.
> - Edits to `compose.yaml` show up in `git diff`. Edits to `vendor/bin/sail` don't, because `vendor/` is gitignored.
>
> For full isolation, run the agent inside the app container instead, so it never gets the Docker socket.

On Linux, the sandbox needs `bubblewrap` and `socat`:

```bash
sudo apt-get install bubblewrap socat
```

On Ubuntu 24.04 and later, AppArmor also blocks `bubblewrap` until it has a profile. See the
[sandbox docs](https://code.claude.com/docs/en/sandboxing) for the profile and the other platforms. Run `/sandbox` in
Claude Code to check that nothing is missing.
