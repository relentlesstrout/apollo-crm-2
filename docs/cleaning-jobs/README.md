# Cleaning jobs

A cleaning job is one visit to one property on one date. A job lists the services to do, the price of each service and the cleaners who do the work.

In the code, the model is named `CleaningJob`. This is different from Laravel's queue "jobs" (the `jobs` table), which are background tasks.

## Data

### `cleaning_jobs`

Model: `App\Models\CleaningJob`.

| Column | Type | Notes |
|---|---|---|
| `property_id` | foreign key | The property. |
| `status` | enum `CleaningJobStatus` | `scheduled`, `in_progress`, `completed` or `cancelled`. The default is `scheduled`. |
| `notes` | text, nullable | Optional. |
| `scheduled_at` | timestamp | The date of the visit. |
| `started_at` | timestamp, nullable | Set when the job changes to In Progress. |
| `completed_at` | timestamp, nullable | Set when the job changes to Completed. |
| `invoice_id` | integer, nullable | For future invoices. There is no `invoices` table yet. |

### Link tables

| Table | Purpose | Extra columns |
|---|---|---|
| `cleaning_job_service` | The services on the job. | `price`: a copy of the price in pence on the job date. `actual_price`: the price you really charged (nothing sets this yet). |
| `cleaning_job_schedule` | The schedules that created the job. The app uses this link to move each schedule forward when the job is completed or cancelled. | — |
| `cleaning_job_user` | The cleaners assigned to the job. Only users with the `cleaner` role can be assigned. | — |

Relationships on `CleaningJob`: `property()`, `services()`, `schedules()` and `cleaners()`.

## Pages

| Page | Route name | URL |
|---|---|---|
| List of all jobs | `cleaning-jobs.index` | `GET /cleaning-jobs` |
| Create form | `properties.cleaning-jobs.create` | `GET /properties/{property}/cleaning-jobs/create` |
| Save new job | `properties.cleaning-jobs.store` | `POST /properties/{property}/cleaning-jobs` |
| Detail | `cleaning-jobs.show` | `GET /cleaning-jobs/{cleaning_job}` |
| Edit form | `cleaning-jobs.edit` | `GET /cleaning-jobs/{cleaning_job}/edit` |
| Save changes | `cleaning-jobs.update` | `PUT /cleaning-jobs/{cleaning_job}` |
| Change status | `cleaning-jobs.status` | `POST /cleaning-jobs/{cleaningJob}/status` |
| Reschedule | `cleaning-jobs.reschedule` | `POST /cleaning-jobs/{cleaningJob}/reschedule` |

## How it works

There are two ways to create a job: automatically from schedules, or by hand.

### Automatic jobs (the daily generator)

The Laravel scheduler runs the Artisan command `cleaning-jobs:generate` each day at 06:00 (see `routes/console.php`). The command calls `GenerateCleaningJobsAction`.

On your computer, the scheduler runs only while `vendor/bin/sail artisan schedule:work` runs. You can also run the command yourself:

```bash
vendor/bin/sail artisan cleaning-jobs:generate
```

The action does these steps:

1. It finds all the schedules where:
   - `active_at` is not empty (the schedule is switched on), **and**
   - `next_due_at` is today or earlier, **and**
   - the property status is `active`.
2. It groups the schedules by property.
3. For each property, in one database transaction:
   1. It creates one job with the status `scheduled` and `scheduled_at` = today.
   2. For each schedule, it finds the price for today. See [How the app chooses a price](../services/README.md#how-the-app-chooses-a-price). It adds the service to the job with a copy of that price. If there is no price, it leaves the service out.
   3. It links the job to each schedule in `cleaning_job_schedule`.
   4. It moves each schedule's `next_due_at` forward by its frequency.
4. The command prints the number of jobs it created.

A schedule that is overdue (for example, due last week) gets a job dated **today**, not the old due date.

The generator does not assign cleaners.

### Jobs added by hand

1. On the property detail page, the Admin clicks the add button in the **Cleaning jobs** panel.
2. The form shows one row for each price row (`property_service`) of the property, with that price filled in. The Admin can change the prices. The form does not check the effective dates. See [Planned changes](#planned-changes), item 13.
3. The Admin chooses the date, the services, the cleaners (optional) and notes (optional).
4. `StoreCleaningJobRequest` validates the fields:
   - At least one service is selected.
   - Each service must have a price at this property.
   - Each selected service must have a price of at least `0.01`.
   - Each cleaner must be a user with the `cleaner` role.
5. `CreateCleaningJobAction` creates the job with the status `scheduled`, and adds the services and cleaners.

A job added by hand is **not linked to a schedule**. When you complete or cancel it, no schedule changes.

### Edit a job

The **Edit** button shows only for Scheduled and In Progress jobs.

1. The Admin can change the date, notes, services, prices and cleaners.
2. `UpdateCleaningJobRequest` uses the same rules as the create form.
3. `UpdateCleaningJobAction` saves the changes in one transaction:
   - It replaces the list of services and prices. If a service stays on the job, its `actual_price` is kept.
   - It replaces the list of cleaners.

### Change the status

The job detail page shows these buttons:

| Current status | Buttons |
|---|---|
| Scheduled | Start, Cancel, Reschedule |
| In Progress | Complete, Cancel |
| Completed | *(none)* |
| Cancelled | Reschedule |

To complete a job, you must first click **Start**, then **Complete**.

When the status changes, `UpdateCleaningJobStatusAction` does these steps in one transaction:

1. It saves the new status.
2. **In Progress**: it sets `started_at` to now, if it is empty.
3. **Completed**: it sets `completed_at` to now. It also sets `started_at` to now, if it is empty.
4. If the status really changed, it moves the linked schedules forward. It changes only schedules that are switched on:

| New status | New `next_due_at` for each linked schedule | Reason |
|---|---|---|
| Completed | **Today** + frequency | The work happened today, so the next visit is one full frequency from today. |
| Cancelled | **The job's scheduled date** + frequency | The visit was skipped. The rhythm continues from the planned date. It does not restart from today. |

### Reschedule a job

1. The **Reschedule** button shows for Scheduled and Cancelled jobs.
2. The Admin chooses a new date. The date must be today or later (`RescheduleCleaningJobRequest`).
3. `RescheduleCleaningJobAction` saves the new date and sets the status to `scheduled`.

You can use this to move a visit, or to bring back a cancelled visit. A reschedule does **not** change any schedule. When you cancelled the job, its schedules already moved forward.

### List page

The list is a Livewire table (`resources/views/components/tables/⚡cleaning-jobs.blade.php`).

- **Search** looks for each word in the property's house or street.
- **Status filter**: All, or one status.
- The list shows the newest date first, 20 jobs at a time.

## Full example

A property has a "Front windows" schedule every 4 weeks, with `next_due_at` = 1 March. The price is £10.00.

| Date | What happens | Job | Schedule `next_due_at` |
|---|---|---|---|
| 1 March, 06:00 | The generator creates a job. | Scheduled, 1 March, Front windows £10.00 | 29 March |
| 3 March | The Admin clicks **Start**, then **Complete**. | Completed, `completed_at` = 3 March | 31 March (3 March + 4 weeks) |
| 31 March, 06:00 | The generator creates the next job. | Scheduled, 31 March | 28 April |
| 31 March | It rains. The Admin clicks **Cancel**. | Cancelled | 28 April (31 March + 4 weeks) |
| 28 April, 06:00 | The generator creates the next job. | Scheduled, 28 April | 26 May |

## Code

| Layer | Files |
|---|---|
| Command | `app/Console/Commands/GenerateCleaningJobs.php`, scheduled in `routes/console.php` |
| Controller | `app/Http/Controllers/CleaningJobController.php` |
| Actions | `app/Actions/CleaningJobs/GenerateCleaningJobsAction.php`, `CreateCleaningJobAction.php`, `UpdateCleaningJobAction.php`, `UpdateCleaningJobStatusAction.php`, `RescheduleCleaningJobAction.php` |
| Requests | `app/Http/Requests/StoreCleaningJobRequest.php`, `UpdateCleaningJobRequest.php`, `UpdateCleaningJobStatusRequest.php`, `RescheduleCleaningJobRequest.php` |
| DTO | `app/DTOs/CleaningJob/CleaningJobData.php` |
| Views | `resources/views/cleaning-jobs/`, `resources/views/components/cleaning-job-form-fields.blade.php`, `resources/views/components/tables/⚡cleaning-jobs.blade.php` |
| Tests | `tests/Unit/Actions/CleaningJobs/`, `tests/Feature/CleaningJobControllerTest.php`, `tests/Feature/GenerateCleaningJobsCommandTest.php` |

## Planned changes

The decisions for these items are in [Decisions](../README.md#decisions). Each ticket is one GitHub issue.

### Changes to current problems

| # | Current problem | Decision | Ticket |
|---|---|---|---|
| 1 | Jobs appear only on the day they are due. | A **rolling 14-day window**: each daily run creates the next job when its due date is within today + 14 days. The job date is the due date (or today, if the due date is in the past). The number of days is a config value. | [#22](https://github.com/relentlesstrout/apollo-crm-2/issues/22) |
| 2 | Late cleans move all future visits later, and the rule was not decided. | **From the last clean** is the rule. The next visit = completion date + frequency. | [#23](https://github.com/relentlesstrout/apollo-crm-2/issues/23) |
| 3 | If a job stays open, the generator creates another one. | **One open job per schedule.** The generator skips a schedule that has an open job. An old open job **stays open and shows as overdue**. There is no auto-cancel. | [#22](https://github.com/relentlesstrout/apollo-crm-2/issues/22), [#25](https://github.com/relentlesstrout/apollo-crm-2/issues/25) |
| 4 | Schedules due on the same date need a combining rule. | Schedules due on the **same date** at the same property share one job. If an open job already exists for that property and date, the service is added to it. | [#22](https://github.com/relentlesstrout/apollo-crm-2/issues/22) |
| 5 | A manual job is not linked to a schedule. | **No change.** Manual jobs are for one-off extras only. They do not change any schedule. | — |
| 6 | The backend accepts any status change. | The backend enforces the allowed changes: Scheduled → In Progress, Completed or Cancelled. In Progress → Completed or Cancelled. Cancelled → Scheduled only through Reschedule. | [#17](https://github.com/relentlesstrout/apollo-crm-2/issues/17) |
| 7 | If a service has no price, the generator leaves it out with no warning. | The generator adds the last known price and sets a **"price needs review"** flag on the job. | [#24](https://github.com/relentlesstrout/apollo-crm-2/issues/24) |
| 8 | Nothing sets `actual_price`. | A **Complete form** shows each service with its price filled in. You can change each price, and the value is saved to `actual_price`. | [#29](https://github.com/relentlesstrout/apollo-crm-2/issues/29) |
| 9 | Completion takes two clicks (Start, then Complete). | You can **Complete straight from Scheduled**. Start stays optional. | [#29](https://github.com/relentlesstrout/apollo-crm-2/issues/29) |
| 10 | There is no record of who did the work. | A new `completed_by` column records the user who completed the job. The cleaner tickboxes stay optional. | [#29](https://github.com/relentlesstrout/apollo-crm-2/issues/29) |
| 11 | Cancelling a property does not cancel its open jobs. | Cancel and Pause on a property cancel its open jobs. See [Properties](../properties/README.md#planned-changes). | [#26](https://github.com/relentlesstrout/apollo-crm-2/issues/26), [#27](https://github.com/relentlesstrout/apollo-crm-2/issues/27) |
| 12 | There is no `CleaningJobPolicy`. | Add `CleaningJobPolicy` and support more than one role in `RoleMiddleware`. | [#30](https://github.com/relentlesstrout/apollo-crm-2/issues/30) |
| 13 | The job forms show one row for each price row. | One row per service, filled in with the price that applies on the job date. | [#19](https://github.com/relentlesstrout/apollo-crm-2/issues/19) |
| 14 | A cancelled job can be rescheduled even if a newer job exists. | Block the reschedule if the schedule already has a newer open job. | [#23](https://github.com/relentlesstrout/apollo-crm-2/issues/23) |
| 15 | `invoice_id` exists, but there are no invoices. | Out of scope for now. | — |

### Cancel stays one action

There is one **Cancel** action for all reasons (the customer is away, rain, building work). A cancel by hand moves the schedule from the planned date. An automatic cancel (when a property is paused or cancelled) does not move the schedule.

### New: cleaner access

You will do the round with a separate **Cleaner** login, as an employee would.

| Item | Decision | Ticket |
|---|---|---|
| "My round" page | All open jobs: overdue first, then today, then the rest of the 14-day window. After login, a cleaner goes to this page. | [#31](https://github.com/relentlesstrout/apollo-crm-2/issues/31) |
| Cleaner job page | The cleaner sees the services, property notes, customer phone and job notes. The cleaner can Start, Complete (with actual price), Cancel and add notes. The cleaner cannot reschedule, edit services or prices, or open the admin pages. | [#32](https://github.com/relentlesstrout/apollo-crm-2/issues/32) |
| Jobs list filters | An overdue badge, and filters for Overdue, Today and Next 14 days. | [#25](https://github.com/relentlesstrout/apollo-crm-2/issues/25) |
