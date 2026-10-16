---
name: pr-description
description: Write a pull request description for the current branch in this project's format. Use when asked for a PR description, PR body or PR summary.
---

# PR description

Base the description only on what is committed on the branch, never on uncommitted changes or earlier conversation:

```bash
git log --oneline main..HEAD
git diff main...HEAD --stat
git diff main...HEAD
```

If the branch contains changes unrelated to its purpose, point them out to the user and suggest moving them to their own branch instead of describing them.

## Format

```markdown
## Summary

<One sentence: what changes and why.>

- **<Area>:** <What changed, naming the key file, class or env var>.
- **Tests:** <Tests added or updated, CI changes>.
- **Docs:** <README or docs updates>.

## Deploying

<Only if needed: env vars, migrations, manual steps.>
```

- One bullet per area, with a bold label. No file-by-file list.
- Leave out the Tests and Docs bullets if nothing changed there.
- Leave out the Deploying section if nothing is needed beyond a normal deploy.
- No AI attribution lines.

## Example

```markdown
## Summary

Choose the public resume by the admin's email instead of a hardcoded user ID, and show a "coming soon" page when there's nothing published yet.

- **Admin by email:** New `DEFAULT_ADMIN_EMAIL` env var (`config/admin.php`). It replaces the hardcoded `user_id = 1`. A new `Resume::publishedFor($email)` scope does the lookup.
- **Coming soon page:** `/` now shows a `noindex` "coming soon" page instead of a 404 when the admin has no published resume. `/download` and `/sitemap.xml` still return 404.
- **Tests:** `HomePageTest` updated for the new behaviour, and a new `SeoControllerTest` added. CI now sets `DEFAULT_ADMIN_EMAIL`.
- **Docs:** README updated for the new env var.

## Deploying

Set `DEFAULT_ADMIN_EMAIL` to the admin's email in production before deploying.
```
