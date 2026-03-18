# Setup Wizard Implementation Review (posts-data-table vs AFRSM)

## Purpose

Review the **posts-data-table** plugin’s setup wizard for **ideas and flow only** (no code copy). Then align AFRSM’s wizard with those ideas using AFRSM’s existing structure and Freemius.

---

## 1. Posts-data-table: Main ideas

### Activation trigger

- **When:** Wizard is started only **once**, right after first activation.
- **How:**
  - `register_activation_hook` → run `on_activate()`.
  - In `on_activate()`: if “wizard should start” (see below), set a **short-lived transient** (e.g. 30 seconds), e.g. `_{plugin_slug}-setup-wizard_activation_redirect`.
  - On **admin_init**: if that transient exists, consider it “user just activated” → delete transient, **redirect** to the wizard URL (dedicated admin page, e.g. `admin.php?page=...-setup-wizard`), and set a **“wizard completed” option** so that **on reactivation** the wizard is not auto-started again.
- **“Should start”:** In posts-data-table, a custom `Starter` class decides (e.g. wizard not completed yet). So: **only set the activation transient when the wizard has not been completed before.**

### Run once / no repeat on reactivation

- After the first redirect, an option is stored (e.g. `posts-table-search-sort-setup-wizard_completed`).
- On **reactivation**, `should_start()` is false → transient is **not** set → no redirect. User is not sent to the wizard again.

### Dedicated wizard page

- Wizard has its **own admin page** (menu slug like `...-setup-wizard`), so the flow is: activate → redirect to that page → user goes through steps there. The page can be hidden from the menu but still reachable by URL.

### No third-party license in flow

- posts-data-table does not use Freemius in the same way; their wizard does not wrap a license/connect screen. So the “activation + redirect” idea is what we reuse, not their step structure.

---

## 2. AFRSM: Current flow and Freemius

### Current flow

- **Activation:** Activator sets transient `_welcome_screen_afrsm_pro_mode_activation_redirect_data` (30 s).
- **admin_init:** If transient exists → delete it and **redirect to** `admin.php?page=afrsm-pro-list` (shipping list page).
- **Freemius:** When the user has not connected yet, Freemius shows its **connect/opt-in screen**. That screen is often shown when the user first hits an admin page (or a specific Freemius URL).
- **Wizard + Freemius:** AFRSM uses `templates/connect.php` to **wrap** the Freemius connect HTML inside the 6-step wizard. So when Freemius shows the connect screen, the user sees: **Step 1** (creation method) + progress bar + **Step 2** (Freemius connect content). After connect/skip, `after_connect_url` sends the user to the list page, optionally with `wizard_step=3` (template path) or without (from-scratch path). The list page can render the wizard from step 3–6 when `wizard_step` is in the URL.

### Important point: Freemius

- The wizard is **not** a separate first redirect target. The first redirect goes to **afrsm-pro-list**. Freemius then controls whether the user sees the connect screen (and thus the wizard wrapper) or the list.
- So the “activation” idea we take from posts-data-table is: **only redirect after activation when the wizard has never been “completed”**. That way:
  - First install: activate → redirect to list → Freemius shows connect → wizard wraps it → user does steps 1–6 (or from-scratch and leaves after connect).
  - Reactivation: if wizard was already completed, **do not** set the activation transient → no redirect; user stays on plugins page (or current page).

---

## 3. What to implement in AFRSM (aligned with your structure)

1. **“Wizard completed” option**  
   - e.g. `afrsm_setup_wizard_completed`.  
   - Set to `true` when the user **finishes the wizard** (e.g. step 6: rule created successfully). Optionally also when they explicitly “skip” or leave the wizard (if you add a skip action).

2. **Activation transient only when wizard not completed**  
   - In the **Activator**: set the welcome/redirect transient **only if** `! get_option( 'afrsm_setup_wizard_completed' )`.  
   - So: first activation → set transient → redirect to list → Freemius + wizard as now.  
   - Reactivation after wizard was completed → do not set transient → no redirect.

3. **Keep Freemius handling as is**  
   - Keep using `templates/connect.php` to wrap the connect screen with the wizard.  
   - Keep `after_connect_url` (and cookie) to send template-path users to `wizard_step=3` and from-scratch users to the list.  
   - No need for a separate “wizard URL” redirect; the list page + Freemius connect URL already host the wizard.

4. **No copy of posts-data-table code**  
   - No React, no Barn2 library, no their step classes.  
   - Only the **concepts**: activation transient, one-time redirect, and a “completed” option so reactivation does not trigger the wizard again.

---

## 4. Summary

| Idea                     | Posts-data-table                    | AFRSM application                                                |
|--------------------------|-------------------------------------|------------------------------------------------------------------|
| Activation trigger       | Transient set in activation hook    | Keep current: transient in Activator                             |
| Redirect target          | Dedicated wizard page               | Keep: redirect to list; Freemius connect = wizard step 2        |
| Run once                 | Option “wizard completed”           | Add `afrsm_setup_wizard_completed`; set when step 6 done         |
| No redirect on reactivation | Transient only if !completed     | Set transient only if `! get_option( 'afrsm_setup_wizard_completed' )` |
| License / connect        | N/A                                 | Keep: wrap Freemius connect in wizard; after_connect_url + cookie |

This keeps your plugin’s flow and design, and only adds the “run once / no redirect on reactivation” behavior inspired by posts-data-table.
