# Zaku Frontend App Issues

Last updated: 2026-05-20

This file tracks issues specifically for the Laravel Blade app in this folder.

## FE-001 - Rename visible product text to Zaku and remove e-wallet wording

Priority: High
Status: Todo

Files to check:
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/chat/index.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/css/app.css`

Acceptance criteria:
- [ ] User-facing text uses "Zaku".
- [ ] Browser title fallback uses "Zaku".
- [ ] CSS pseudo content no longer says "DOMPET".
- [ ] UI does not suggest real money transfer, top up, or withdrawal.

## FE-002 - Configure backend API URL

Priority: High
Status: Todo

Set local API URL:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

Acceptance criteria:
- [ ] `.env.example` contains the local backend API URL.
- [ ] `resources/js/api-client.js` sends requests to backend Zaku.
- [ ] Login works with `demo@zaku.test` / `password`.

## FE-003 - Align auth refresh behavior with backend

Priority: High
Status: Todo

Problem:
The app currently tries to refresh token using `refresh_token`, but backend refresh expects a Bearer token. Until a separate refresh-token contract exists, 401 should clear auth and redirect to login.

Acceptance criteria:
- [ ] 401 clears auth storage.
- [ ] User redirects to `/login?session=expired`.
- [ ] No invalid refresh request is sent.

## FE-004 - Fix profile budget payload

Priority: High
Status: Todo

Current incorrect payload:

```json
{ "amount": 4000000 }
```

Expected payload:

```json
{ "monthly_budget": 4000000 }
```

File:
- `resources/views/dashboard/profile.blade.php`

Acceptance criteria:
- [ ] Save budget succeeds against backend.
- [ ] UI updates after success.
- [ ] Validation errors show with toast.

## FE-005 - Add reset password completion screen

Priority: Medium
Status: Todo

Dependency:
- Backend endpoint `POST /api/auth/reset-password`.

Acceptance criteria:
- [ ] User enters email, token, new password, and confirmation.
- [ ] Success redirects to login.
- [ ] Invalid token and validation errors are shown clearly.

## FE-006 - Manual smoke test before release

Priority: Medium
Status: Todo

Checklist:
- [ ] Register.
- [ ] Verify email.
- [ ] Login.
- [ ] Dashboard.
- [ ] Add expense via chat/manual flow.
- [ ] Add income via chat/manual flow.
- [ ] Transaction detail.
- [ ] Delete transaction.
- [ ] Profile update.
- [ ] Budget update.
- [ ] Logout.

## FE-007 - Remove Top Up, Withdraw, and Send Money UI

Priority: High
Status: Todo

Problem:
Zaku is for tracking income and expenses only. It is not a wallet or money-transfer app.

Files to check:
- `resources/views/wallet/topup.blade.php`
- `resources/views/wallet/withdraw.blade.php`
- `resources/views/wallet/send-money.blade.php`
- `resources/views/components/navigation.blade.php`
- `routes/web.php`

Acceptance criteria:
- [ ] No visible Top Up menu.
- [ ] No visible Withdraw menu.
- [ ] No visible Send Money menu.
- [ ] Main flow points users to record income or expense.
