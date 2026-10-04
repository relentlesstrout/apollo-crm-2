# Schedules

A schedule is a repeat plan for one service at one property, for example "clean the front windows at 12 High Street every 4 weeks". The daily job generator uses the schedules to create cleaning jobs.

## Data

Table: `schedules`. Model: `App\Models\Schedule`.

| Column | Type | Notes |
|---|---|---|
| `property_id` | foreign key | The property. |
| `service_id` | foreign key | The service. The property must have a price for it. See [Services and prices](../services/README.md). |
| `frequency_weeks` | small integer | How often: 1, 2, 4, 8, 12 or 16 weeks. |
| `active_at` | timestamp, nullable | The date and time the schedule was switched on. **Empty means the schedule is switched off.** |
| `next_due_at` | date | The date of the next visit. The daily job generator creates a job when this date is today or earlier. |

Relationships:

- `property()` and `service()`.
- A schedule links to the cleaning jobs that it created, through the `cleaning_job_schedule` table.

## Pages

These routes are shallow. The create and save routes are under the property URL.

| Page | Route name | URL |
|---|---|---|
| Create form | `properties.schedules.create` | `GET /properties/{property}/schedules/create` |
| Save new schedule | `properties.schedules.store` | `POST /properties/{property}/schedules` |
| Edit form | `schedules.edit` | `GET /schedules/{schedule}/edit` |
| Save changes | `schedules.update` | `PUT /schedules/{schedule}` |
| Remove | `schedules.destroy` | `DELETE /schedules/{schedule}` |

There is no list page for schedules. You see them in the **Schedules** panel on the property detail page.

## How it works

### Create a schedule

1. On the property detail page, the Admin clicks the add button in the **Schedules** panel.
2. The **Service** list shows only the services that have a price at this property.
3. The Admin chooses the frequency and the **Next Due** date (the date of the first visit).
4. `StoreScheduleRequest` validates the fields. It sets `active_at` to the current date and time, so the schedule is active immediately.
5. `CreateScheduleAction` saves the schedule.

### Edit a schedule

1. The Admin can change the service, the frequency, the **Next Due** date and the **Active** tickbox.
2. `UpdateScheduleRequest` works out `active_at`:
   - **Active** ticked, and the schedule was already active: the old `active_at` stays.
   - **Active** ticked, and the schedule was switched off: `active_at` becomes now.
   - **Active** not ticked: `active_at` becomes empty, so the schedule is switched off.
3. `UpdateScheduleAction` saves the changes.

### Remove a schedule

The controller deletes the schedule. The cleaning jobs that it created stay, but their link to the schedule is deleted.

### How `next_due_at` changes

Three events move `next_due_at` forward. The details are in [Cleaning jobs](../cleaning-jobs/README.md).

| Event | New `next_due_at` | Code |
|---|---|---|
| The daily generator creates a job | Old `next_due_at` + frequency | `GenerateCleaningJobsAction` |
| A job is marked **Completed** | Completion date + frequency | `UpdateCleaningJobStatusAction` |
| A job is marked **Cancelled** | The job's scheduled date + frequency | `UpdateCleaningJobStatusAction` |

Completion and cancellation **overwrite** the value that the generator set.

**Example.** A schedule has a 4-week frequency and `next_due_at` = 1 March.

1. On 1 March, the generator creates a job for 1 March. `next_due_at` becomes 29 March.
2. The job is completed on 4 March (3 days late). `next_due_at` becomes 1 April.
3. All future visits are now 3 days later than the original plan.

### When a schedule creates jobs

A schedule creates a job only when all these conditions are true:

- `active_at` is not empty.
- `next_due_at` is today or earlier.
- The property status is `active`.

If one property has more than one schedule due on the same day, the generator creates **one** job that contains all the services.

## Code

| Layer | Files |
|---|---|
| Controller | `app/Http/Controllers/ScheduleController.php` |
| Actions | `app/Actions/Schedules/CreateScheduleAction.php`, `UpdateScheduleAction.php` |
| Requests | `app/Http/Requests/StoreScheduleRequest.php`, `UpdateScheduleRequest.php` |
| DTO | `app/DTOs/Schedule/ScheduleData.php` |
| Listener | `app/Listeners/DeactivatePropertySchedules.php` (switches off schedules when a property is cancelled) |
| Views | `resources/views/schedules/`, `resources/views/components/schedule-form-fields.blade.php` |
| Tests | `tests/Unit/Actions/Schedules/` |

## Planned changes

The decisions for these items are in [Decisions](../README.md#decisions). Each ticket is one GitHub issue.

| # | Current problem | Decision | Ticket |
|---|---|---|---|
| 1 | The generator adds the frequency to the due date, but completion resets it from the completion date. The rule was not decided. | **From the last clean.** Completed → completion date + frequency. Cancelled by hand → planned date + frequency. The generator does **not** move `next_due_at` any more. | [#22](https://github.com/relentlesstrout/apollo-crm-2/issues/22), [#23](https://github.com/relentlesstrout/apollo-crm-2/issues/23) |
| 2 | `StoreScheduleRequest` does not check that the property has a price for the service. | You cannot create a schedule or switch it on unless a price applies on its `next_due_at`. | [#18](https://github.com/relentlesstrout/apollo-crm-2/issues/18) |
| 3 | You can create two schedules for the same service at the same property. | Block a second active schedule for the same service at the same property. | [#18](https://github.com/relentlesstrout/apollo-crm-2/issues/18) |
| 4 | Activating a cancelled property does not switch its schedules on. | An **Activate form** lets you choose which schedules to switch on, and the date. | [#28](https://github.com/relentlesstrout/apollo-crm-2/issues/28) |
| 5 | Pausing a property does not change `next_due_at`. | On Activate after Pause, you choose the next visit date for all active schedules. | [#28](https://github.com/relentlesstrout/apollo-crm-2/issues/28) |
| 6 | The form requests return `authorize(): true`. | No change now. The routes stay Admin-only. | — |

### How `next_due_at` will change after tickets [#22](https://github.com/relentlesstrout/apollo-crm-2/issues/22) and [#23](https://github.com/relentlesstrout/apollo-crm-2/issues/23)

| Event | New `next_due_at` |
|---|---|
| The generator creates a job | No change. |
| A job is marked **Completed** | Completion date + frequency. The generator then runs for that property immediately. |
| A job is **Cancelled** by hand | The job's planned date + frequency. The generator then runs for that property immediately. |
| A job is cancelled because the property is paused or cancelled | No change. |
