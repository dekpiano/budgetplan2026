# Completion Checks
- Baseline automated check available: `composer test` from project root.
- Before considering a change complete, inspect relevant CI4 diagnostics and run the PHPUnit suite when practical.
- No project-specific lint/formatter command is declared in `composer.json`; do not invent one.