# Services and prices

This feature has two parts:

- **Services**: the catalogue of work types, for example "Front windows", "Conservatory" or "Gutters".
- **Property services**: the price of one service at one property, for a date range.

A property must have a price for a service before you can add a schedule for that service. See [Schedules](../schedules/README.md).

## Data

### Services

Table: `services`. Model: `App\Models\Service`.

| Column | Type | Notes |
|---|---|---|
| `name` | string | Required. |
| `description` | text, nullable | Optional. |
| `deleted_at` | timestamp, nullable | Set when the service is archived (soft delete). Archived services do not show in lists or pickers. |

### Property services (prices)

Table: `property_service`. Model: `App\Models\PropertyService`.

| Column | Type | Notes |
|---|---|---|
| `property_id` | foreign key | The property. |
| `service_id` | foreign key | The service. |
| `price` | unsigned integer | The price in **pence**. The form takes pounds, for example `12.50`, and saves `1250`. |
| `description` | text, nullable | Optional. For example "front and back, not the extension". |
| `effective_from` | date | Required. The first date that this price applies. |
| `effective_to` | date, nullable | Optional. The last date that this price applies. Empty means "no end date". |
| `deleted_at` | timestamp, nullable | Set when the price row is removed, or when its service is archived (soft delete). Removed rows do not show and are not used for prices. |

Relationships:

- `Service::propertyServices()`: a service has many property prices.
- `PropertyService::property()` and `PropertyService::service()`.

## Pages

### Services

| Page | Route name | URL |
|---|---|---|
| List | `services.index` | `GET /services` |
| Create form | `services.create` | `GET /services/create` |
| Save new service | `services.store` | `POST /services` |
| Edit form | `services.edit` | `GET /services/{service}/edit` |
| Save changes | `services.update` | `PUT /services/{service}` |
| Archive | `services.destroy` | `DELETE /services/{service}` |
| Restore | `services.restore` | `POST /services/{service}/restore` |

### Property services

These routes are shallow. The create and save routes are under the property URL.

| Page | Route name | URL |
|---|---|---|
| Create form | `properties.property-services.create` | `GET /properties/{property}/property-services/create` |
| Save new price | `properties.property-services.store` | `POST /properties/{property}/property-services` |
| Edit form | `property-services.edit` | `GET /property-services/{property_service}/edit` |
| Save changes | `property-services.update` | `PUT /property-services/{property_service}` |
| Remove | `property-services.destroy` | `DELETE /property-services/{property_service}` |

## How it works

### Manage the service catalogue

1. The Admin opens **Services** in the top menu.
2. The list shows all services in name order, with edit and delete buttons.
3. `StoreServiceRequest` or `UpdateServiceRequest` validates the name and description.
4. `CreateServiceAction` or `UpdateServiceAction` saves the service.
5. The **Show archived services** toggle (`?archived=1`) adds archived services to the list, with an **Archived** badge and a **Restore** button.

### Archive a service

The **Archive** button runs `ArchiveServiceAction` in one database transaction:

1. It soft deletes the service.
2. It soft deletes every price row (`property_service`) for the service, at every property. The rows get **the same `deleted_at` value as the service**, so a restore can find them.
3. It switches off every schedule for the service (`active_at = null`).
4. It removes the service from each **open** (Scheduled or In Progress) job. If a job has no services left, the job is cancelled. These cancels do not move any schedule.
5. Completed and cancelled jobs do not change. They keep the service and its price as history.

The success message shows how many prices, schedules and jobs changed.

### Restore a service

The **Restore** button runs `RestoreServiceAction` in one transaction:

1. It restores the price rows whose `deleted_at` is the same as the service's `deleted_at`. These are the rows that the archive removed. A price row that you removed on its own before the archive stays removed.
2. It restores the service.
3. The schedules **stay switched off**. Switch each one on again on the property page, so that you check the date and the price first.

### Add a price to a property

1. On the property detail page, the Admin clicks the add button in the **Services** panel.
2. The Admin chooses a service, types the price in pounds, and sets the **Effective From** date. **Effective To** and the description are optional.
3. `StorePropertyServiceRequest` validates the fields:
   - The service must exist.
   - The price must be at least `0.01`.
   - **Effective To** must be after **Effective From**.
4. The request changes the price to pence: `round(price × 100)`.
5. `CreatePropertyServiceAction` saves the row.

### Edit or remove a price

- **Edit**: `UpdatePropertyServiceAction` overwrites all the fields of the row.
- **Remove**: the controller soft deletes the row. The row is kept in the database with `deleted_at` set, but the app does not show it or use it for prices. The schedules and past jobs do not change. There is no restore button for one price row yet.

### How the app chooses a price

When the daily job generator creates a job, it chooses a price for each service like this (`effectivePrice()` in `GenerateCleaningJobsAction`):

1. It looks for a row for this property and service where `effective_from` is on or before the job date, **and** `effective_to` is empty or on or after the job date. If more than one row matches, it uses the row with the latest `effective_from`.
2. If no row matches, it uses the row with the latest `effective_from`, even if that row has expired.
3. If the property has no row for the service, there is no price. The service is left out of the job.

The job keeps a **copy** of the price. If you change the price later, the jobs that already exist do not change.

**Example.** A property has two rows for "Front windows":

| `price` | `effective_from` | `effective_to` |
|---|---|---|
| `1000` (£10.00) | 2026-01-01 | 2026-06-30 |
| `1200` (£12.00) | 2026-07-01 | *(empty)* |

A job on 2026-05-10 gets £10.00. A job on 2026-08-01 gets £12.00.

## Code

| Layer | Files |
|---|---|
| Controllers | `app/Http/Controllers/ServiceController.php`, `PropertyServiceController.php` |
| Actions | `app/Actions/Services/`, `app/Actions/PropertyServices/` |
| Requests | `app/Http/Requests/StoreServiceRequest.php`, `UpdateServiceRequest.php`, `StorePropertyServiceRequest.php`, `UpdatePropertyServiceRequest.php` |
| DTOs | `app/DTOs/Service/ServiceData.php`, `app/DTOs/PropertyService/PropertyServiceData.php` |
| Views | `resources/views/services/`, `resources/views/property-services/`, `resources/views/components/service-form-fields.blade.php`, `property-service-form-fields.blade.php` |
| Tests | `tests/Unit/Actions/Services/`, `tests/Unit/Actions/PropertyServices/` |

## Planned changes

The decisions for these items are in [Decisions](../README.md#decisions). Each ticket is one GitHub issue.

| # | Current problem | Decision | Ticket |
|---|---|---|---|
| 1 | **Delete a service also deletes its history.** | **Done in [PR #33](https://github.com/relentlesstrout/apollo-crm-2/pull/33).** **Archive** (soft delete) cascades: prices are archived, schedules are switched off, and the service is removed from open jobs. **Restore** brings back the archived prices. All history stays. | [#14](https://github.com/relentlesstrout/apollo-crm-2/issues/14) |
| 2 | **Edit** overwrites the price row, so the old price is lost. | A new **Change price** action sets `effective_to` on the old row to the day before the new start date, and adds a new row. **Edit** is for corrections only. | [#21](https://github.com/relentlesstrout/apollo-crm-2/issues/21) |
| 3 | The app does not check for date ranges that overlap. | Block overlapping date ranges on create, edit and change. | [#21](https://github.com/relentlesstrout/apollo-crm-2/issues/21) |
| 4 | A schedule can exist with no price, and the generator leaves the service out with no warning. | You cannot create a schedule or switch it on unless a price applies. If a price has ended when the generator runs, the job gets the last known price and a **"price needs review"** flag. | [#18](https://github.com/relentlesstrout/apollo-crm-2/issues/18), [#24](https://github.com/relentlesstrout/apollo-crm-2/issues/24) |
| 5 | The job forms show one row for each price row, not one row for each service. | One row per service, filled in with the price that applies on the job date. | [#19](https://github.com/relentlesstrout/apollo-crm-2/issues/19) |
| 6 | The form requests return `authorize(): true`. | No change now. The routes stay Admin-only. | — |
