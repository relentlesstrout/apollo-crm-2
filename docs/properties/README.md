# Properties

A property is an address that you clean. Each property belongs to one customer. A property has its own prices, schedules and cleaning jobs.

## Data

Table: `properties`. Model: `App\Models\Property`.

| Column | Type | Notes |
|---|---|---|
| `customer_id` | foreign key | The customer who owns the property. If the customer row is deleted from the database, the property is deleted too. |
| `house` | string | Required. The house name or number. |
| `street` | string | Required. |
| `area` | string, nullable | Optional. The list page uses it as a filter, for example a village or part of a town. |
| `postcode` | string | Required. Must be a valid UK postcode when you create the property (`App\Rules\UkPostcode`). |
| `latitude`, `longitude` | decimal, nullable | For a future map or round plan. Nothing fills them yet. |
| `notes` | text, nullable | For example "side gate code 1234" or "dog in garden". |
| `status` | enum `PropertyStatus` | `active`, `paused` or `cancelled`. The default is `active`. |
| `deleted_at` | timestamp, nullable | Soft delete column. There is no delete route yet. |

Relationships:

- `customer()`: the property belongs to one customer.
- `propertyServices()`: the prices for this property. See [Services and prices](../services/README.md).
- `schedules()`: the repeat plans. See [Schedules](../schedules/README.md).
- `cleaningJobs()`: the visits. See [Cleaning jobs](../cleaning-jobs/README.md).

## Pages

The routes are "shallow". The create and save routes are under the customer URL, because a new property needs a customer. The other routes use only the property ID.

| Page | Route name | URL |
|---|---|---|
| List of all properties | `properties.index` | `GET /properties` |
| Create form | `customers.properties.create` | `GET /customers/{customer}/properties/create` |
| Save new property | `customers.properties.store` | `POST /customers/{customer}/properties` |
| Detail | `properties.show` | `GET /properties/{property}` |
| Edit form | `properties.edit` | `GET /properties/{property}/edit` |
| Save changes | `properties.update` | `PUT /properties/{property}` |
| Change status | `properties.status` | `POST /properties/{property}/status` |

## How it works

### Create a property

There are two ways to create a property:

- The customer create form creates the first property. See [Customers](../customers/README.md#create-a-customer).
- The customer detail page has a link to add more properties.

The steps are:

1. `StorePropertyRequest` validates the fields and makes a `PropertyData` DTO.
2. `CreatePropertyAction` creates the property with the status `active`.
3. The action calls `RecomputeCustomerStatusAction`, so the customer status is correct.
4. The app opens the property detail page.

### Edit a property

1. `UpdatePropertyRequest` validates the fields.
2. `UpdatePropertyAction` saves the address and the notes.
3. The action calculates the customer status again.
4. The app opens the customer detail page.

### Change the status

The property detail page shows buttons for the statuses that the property can change to:

| Current status | Buttons |
|---|---|
| Active | Pause, Cancel |
| Paused | Activate, Cancel |
| Cancelled | Activate |

When you click a button, `UpdatePropertyStatusAction` does these steps:

1. It saves the new status.
2. It calculates the customer status again. See [Customer status](../customers/README.md#customer-status).
3. If the new status is `cancelled`, it sends the `PropertyCancelled` event.

The `DeactivatePropertySchedules` listener receives the `PropertyCancelled` event. It sets `active_at = null` on every schedule of the property, so the schedules stop. Laravel finds this listener automatically because it is in `app/Listeners/`.

The effect of each status on cleaning jobs:

| Status | Effect |
|---|---|
| Active | The daily job generator creates jobs from the active schedules. |
| Paused | The daily job generator skips the property. The schedules stay active, but no jobs are created. |
| Cancelled | The schedules are switched off. The daily job generator skips the property. |

### Detail page

The property detail page shows:

- The address, notes and status, with the status buttons.
- The customer and the customer status.
- A **Services** panel: the prices for this property, with add, edit and remove buttons.
- A **Schedules** panel: the repeat plans, with add, edit and remove buttons.
- A **Cleaning jobs** panel: the visits for this property, with a button to add a job by hand.

### List page

The list is a Livewire table (`resources/views/components/tables/⚡properties.blade.php`).

- **Search** looks for each word in the house or the street. For example, "12 High" finds "12 High Street".
- **Area filter**: a list of all the areas that the properties use.
- **Status filter**: All, Active, Paused or Cancelled.

## Code

| Layer | Files |
|---|---|
| Controller | `app/Http/Controllers/PropertyController.php` |
| Actions | `app/Actions/Properties/CreatePropertyAction.php`, `UpdatePropertyAction.php`, `UpdatePropertyStatusAction.php` |
| Requests | `app/Http/Requests/StorePropertyRequest.php`, `UpdatePropertyRequest.php`, `UpdatePropertyStatusRequest.php` |
| DTO | `app/DTOs/Property/PropertyData.php` |
| Event and listener | `app/Events/PropertyCancelled.php`, `app/Listeners/DeactivatePropertySchedules.php` |
| Policy | `app/Policies/PropertyPolicy.php` (only Admins can view, create and update) |
| Views | `resources/views/properties/`, `resources/views/components/property-form-fields.blade.php`, `resources/views/components/tables/⚡properties.blade.php` |
| Tests | `tests/Unit/Actions/Properties/`, `tests/Unit/Listeners/DeactivatePropertySchedulesTest.php`, `tests/Feature/PropertyPolicyTest.php` |

## Planned changes

The decisions for these items are in [Decisions](../README.md#decisions). Each ticket is one GitHub issue.

| # | Current problem | Decision | Ticket |
|---|---|---|---|
| 1 | When you cancel a property, its open jobs stay open. | **Cancel** a property cancels all its open jobs, with no schedule move. The schedules switch off (as now). | [#26](https://github.com/relentlesstrout/apollo-crm-2/issues/26) |
| 2 | When you pause a property, its open jobs stay open and `next_due_at` goes out of date. | **Pause** cancels all open jobs, with no schedule move. The schedules stay on, but no new jobs are made. | [#27](https://github.com/relentlesstrout/apollo-crm-2/issues/27) |
| 3 | Activate after Pause creates a job immediately with an old due date. | An **Activate form** asks for the next visit date. All active schedules get that date. | [#28](https://github.com/relentlesstrout/apollo-crm-2/issues/28) |
| 4 | Activate after Cancel leaves the schedules switched off. | The same **Activate form** lists the old schedules with tickboxes and a date. You choose which schedules to switch on. | [#28](https://github.com/relentlesstrout/apollo-crm-2/issues/28) |
| 5 | `UpdatePropertyRequest` does not use the `UkPostcode` rule. | Add the rule. | [#20](https://github.com/relentlesstrout/apollo-crm-2/issues/20) |
| 6 | Empty `area` and `notes` save as `""`, not `NULL`. | Save them as `NULL`. | [#20](https://github.com/relentlesstrout/apollo-crm-2/issues/20) |
| 7 | The latitude and longitude are never filled. | Out of scope for now (geocoding and rounds). | — |
| 8 | The property requests return `authorize(): true`. | No change now. The routes stay Admin-only. | — |

A holiday or building work is not a Pause. For one missed visit, cancel that job. See [Cleaning jobs](../cleaning-jobs/README.md#planned-changes).
