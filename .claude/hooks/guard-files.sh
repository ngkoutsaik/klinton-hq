#!/usr/bin/env bash
# PreToolUse guard: no file tool writes outside the project, and every shell
# command that deletes or discards files needs the user's confirmation.

input=$(cat)
tool=$(jq -r '.tool_name' <<<"$input")
project=$(realpath -m "${CLAUDE_PROJECT_DIR:-$(jq -r '.cwd' <<<"$input")}")
memory="$HOME/.claude/projects/$(sed 's|/|-|g' <<<"$project")/memory"

decide() {
    jq -n --arg decision "$1" --arg reason "$2" '{
        hookSpecificOutput: {
            hookEventName: "PreToolUse",
            permissionDecision: $decision,
            permissionDecisionReason: $reason
        }
    }'
    exit 0
}

case "$tool" in
    Write|Edit|MultiEdit|NotebookEdit)
        path=$(jq -r '.tool_input.file_path // .tool_input.notebook_path // empty' <<<"$input")
        [[ -z "$path" ]] && exit 0
        path=$(realpath -m "$path")

        if [[ "$path" != "$project" && "$path" != "$project"/* && "$path" != "$memory"/* ]]; then
            decide deny "Writing outside the project is not allowed: $path. Put generic output in storage/app/agent/."
        fi
        ;;

    Bash)
        command=$(jq -r '.tool_input.command // empty' <<<"$input")

        # Deleting only scratch output the agent owns is fine.
        if [[ "$command" != *..* ]] \
            && grep -qE '^rm( -[a-zA-Z]+)*( (\./)?storage/app/agent/[^ ;&|`$()]+)+$' <<<"$command"; then
            exit 0
        fi

        deletes='(^|[;&|(`[:space:]])(rm|rmdir|unlink|shred|truncate)[[:space:]]'
        deletes+='|[[:space:]]-delete([[:space:]]|$)'
        deletes+='|git([[:space:]]+-[^[:space:]]+)*[[:space:]]+(clean|rm)([[:space:]]|$)'
        deletes+='|git([[:space:]]+-[^[:space:]]+)*[[:space:]]+reset[[:space:]].*--hard'
        deletes+='|git([[:space:]]+-[^[:space:]]+)*[[:space:]]+(checkout|restore)[[:space:]].*(--[[:space:]]|\.([[:space:]]|$))'
        deletes+='|git([[:space:]]+-[^[:space:]]+)*[[:space:]]+stash[[:space:]]+(drop|clear)'
        deletes+='|os\.(remove|unlink|rmdir)|shutil\.rmtree|\.unlink\(|unlink\(|rmdir\('

        if grep -qE "$deletes" <<<"$command"; then
            decide ask "This command may delete or discard files. Only the user can approve deleting files the agent didn't create."
        fi
        ;;
esac

exit 0
