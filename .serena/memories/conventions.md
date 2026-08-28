# Conventions
- Preserve CodeIgniter 4 application separation: controllers under `app/Controllers`, models under `app/Models`, views under `app/Views`.
- Controller naming currently uses `Con` + role/domain naming (e.g. `ConAdminBudgetLedger`, `ConUserPlan`). Preserve established names unless a deliberate refactor is requested.
- Keep public/static concerns separated under `public/`; runtime/generated data belongs in `writable/` and user uploads in `uploads/`.