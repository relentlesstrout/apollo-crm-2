# Customers

A customer is a person or business that pays you for cleaning. One customer can have many properties.

## Data

Table: `customers`. Model: `App\Models\Customer`.

| Column | Type | Notes |
|---|---|---|
| `name` | string | Required. |
| `phone` | string | Required. Must be a valid UK phone number (`App\Rules\UkPhoneNumber`). |
| `email` | string, nullable | Optional. Must be unique in the `customers` table. |
| `status` | enum `CustomerStatus` | `active`, `paused` or `cancelled`. The app calculates this value. You cannot set it by hand. See [Customer status](#customer-status). |
| `user_id` | foreign key, nullable | Links to a `users` row with the `customer` role. It is set only when the customer has portal access. |
| `deleted_at` | timestamp, nullable | Soft delete column (a deleted row is hidden, not removed). There is no delete route yet. |

Relationships:

- `properties()`: the customer has many properties.
- `user()`: the customer belongs to one portal login user (optional).

## Pages

| Page | Route name | URL |
|---|---|---|
| List | `customers.index` | `GET /customers` |
| Create form | `customers.create` | `GET /customers/create` |
| Save new customer | `customers.store` | `POST /customers` |
| Detail | `customers.show` | `GET /customers/{customer}` |
| Edit form | `customers.edit` | `GET /customers/{customer}/edit` |
| Save changes | `customers.update` | `PUT /customers/{customer}` |
| Give portal access | `customers.portal.grant` | `POST /customers/{customer}/portal` |
| Send the portal link again | `customers.portal.resend` | `POST /customers/{customer}/portal/resend` |

## How it works

### Create a customer

The create form makes a customer **and** their first property in one step. You cannot create a customer with no property.

1. The Admin fills in the customer fields (name, phone, email) and the property fields (house, street, area, postcode, notes).
2. The Admin can tick **Send portal invite**. This works only if the email is filled in.
3. `StoreCustomerRequest` validates all the fields. It makes two DTOs: `CustomerData` and `PropertyData`.
4. `CreateCustomerAction` creates the customer with the status `active`.
5. If **Send portal invite** is ticked and there is an email, `CreateCustomerAction` calls `GrantCustomerPortalAccessAction`. See [Portal access](#portal-access).
6. `CreatePropertyAction` creates the first property. It then calls `RecomputeCustomerStatusAction`.
7. The app opens the customer detail page.

### Edit a customer

1. The Admin changes the name, phone or email.
2. `UpdateCustomerRequest` validates the fields. The email must stay unique, but the check ignores this customer's own row.
3. `UpdateCustomerAction` saves the changes.
4. If the customer has a portal login, the action also copies the name, phone and email to the linked `users` row. This keeps the login email the same as the customer email.

### Customer status

You cannot choose the customer status. `RecomputeCustomerStatusAction` calculates it from the status of the customer's properties:

| If the customer has... | The customer status is |
|---|---|
| At least one **Active** property | `active` |
| No Active property, but at least one **Paused** property | `paused` |
| Only **Cancelled** properties, or no properties | `cancelled` |

The app runs this calculation each time a property is created, edited or changes status.

### Portal access

Portal access gives the customer a login account. The customer portal pages do not exist yet, so a customer who logs in gets a "403 Forbidden" error.

**Give access** (`GrantCustomerPortalAccessAction`):

1. The app creates a `users` row with the customer's name, phone and email, the role `customer` and a random password.
2. The app saves the new user's ID in `customers.user_id`.
3. The app sends a password reset link to the customer's email. The customer uses the link to choose their own password.

You can give access in two places:

- On the create form, with the **Send portal invite** tickbox.
- On the edit page, with the **Grant portal access** button. The button shows only when the customer has an email and no login.

**Send the link again**: the edit page shows this button when the customer already has a login. It sends a new password reset link.

### List page

The list is a Livewire table (`resources/views/components/tables/⚡customers.blade.php`). Livewire updates the list as you type, with no page reload.

- **Search** looks for the text in the name or email. The search ignores upper and lower case.
- **Status filter**: All, Active, Paused or Cancelled.
- The page shows 20 customers at a time.

## Code

| Layer | Files |
|---|---|
| Controller | `app/Http/Controllers/CustomerController.php` |
| Actions | `app/Actions/Customers/CreateCustomerAction.php`, `UpdateCustomerAction.php`, `GrantCustomerPortalAccessAction.php`, `RecomputeCustomerStatusAction.php` |
| Requests | `app/Http/Requests/StoreCustomerRequest.php`, `UpdateCustomerRequest.php` |
| DTO | `app/DTOs/Customer/CustomerData.php` |
| Policy | `app/Policies/CustomerPolicy.php` (only Admins can view, create and update) |
| Views | `resources/views/customers/`, `resources/views/components/tables/⚡customers.blade.php` |
| Tests | `tests/Unit/Actions/Customers/` |

## Planned changes

The decisions for these items are in [Decisions](../README.md#decisions). Each ticket is one GitHub issue.

| # | Current problem | Decision | Ticket |
|---|---|---|---|
| 1 | The customer and the first property are not saved in one database transaction. | Save both in one transaction. Send the portal email only after the save succeeds. | [#16](https://github.com/relentlesstrout/apollo-crm-2/issues/16) |
| 2 | If you remove the email of a customer who has portal access, the save fails. | Show a validation error. You cannot remove the email while a portal login exists. | [#15](https://github.com/relentlesstrout/apollo-crm-2/issues/15) |
| 3 | **Grant portal access** does not check for an email, an existing login, or an email that a user already has. | Check all three cases and show a clear error. | [#15](https://github.com/relentlesstrout/apollo-crm-2/issues/15) |
| 4 | **Send the link again** does not check that the customer has a login. | Show an error if there is no login. | [#15](https://github.com/relentlesstrout/apollo-crm-2/issues/15) |
| 5 | The create form limits property notes to 255 characters, but the column is `text`. | Make the limit the same on all property forms. | [#20](https://github.com/relentlesstrout/apollo-crm-2/issues/20) |
| 6 | The customer portal has no pages. | Out of scope for now. | — |
| 7 | There is no way to delete or archive a customer. | **Not now.** A customer who leaves becomes Cancelled through their properties. The list can filter them out. | — |
