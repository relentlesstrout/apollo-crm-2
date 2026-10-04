# Feature documentation

These documents explain how each feature of Apollo CRM works now: the data, the pages, the rules and the code that does the work. Each document also has a **Planned changes** section. It lists the problems in the current code, the decision for each problem, and the GitHub issue that will make the change.

For the first-time setup, see the [main README](../README.md).

## Features

| Feature | Document | Summary |
|---|---|---|
| Customers | [customers/](customers/README.md) | The people who pay you. The status comes from their properties. Portal access is optional. |
| Properties | [properties/](properties/README.md) | The addresses you clean. The status controls the customer status and the schedules. |
| Services and prices | [services/](services/README.md) | The catalogue of work types, and the price of each service at each property. |
| Schedules | [schedules/](schedules/README.md) | The repeat plan: "clean service X at property Y every N weeks". |
| Cleaning jobs | [cleaning-jobs/](cleaning-jobs/README.md) | One visit to one property. The app creates jobs from schedules each day, or you add them by hand. |

## How the features connect

```
Service (catalogue)
   │
Customer ──< Property ──< PropertyService (price per service, with dates)
                 │
                 └──< Schedule (service + every N weeks + next_due_at)
                          │  each day at 06:00: cleaning-jobs:generate
                          ▼
                     CleaningJob ──< services (a copy of the price)
                          │      └──< cleaners
                          ▼
             Completed / Cancelled → the schedule's next_due_at moves forward
```

`──<` means "has many". For example, one customer has many properties.

## Common patterns in the code

All the features use the same structure. When you add to a feature, follow the same pattern:

| Layer | Folder | Job |
|---|---|---|
| Route | `routes/web.php` | Connects a URL to a controller method. All the routes in these documents use the `auth` and `role:admin` middleware, so only an Admin can open them. |
| Form request | `app/Http/Requests/` | Validates the form input and changes it into a DTO. |
| DTO (Data Transfer Object) | `app/DTOs/` | A small read-only class that carries the validated data to the action. |
| Action | `app/Actions/` | Does the business work, for example "create a property". Each action has one public `execute()` method. |
| Controller | `app/Http/Controllers/` | Calls the form request and the action, then shows a view or redirects. The controller has no business logic. |
| Livewire table | `resources/views/components/tables/` | A list page with live search and filters. |

Money is always saved in **pence** as a whole number. For example, `£12.50` is saved as `1250`. The forms take pounds and change them to pence in the form request.

## Decisions

These design decisions were agreed on 27 September 2026. They describe how the app **will** work after the planned tickets are done. The "How it works" sections in the feature documents describe the code **as it is now**. When a ticket is done, update the matching feature document.

The tickets are on the GitHub Project board [Apollo CRM](https://github.com/users/relentlesstrout/projects/5). The columns are **Todo → In Progress → QA → Done**. Move a ticket to QA when its code is merged, and to Done when its acceptance criteria are checked in the running app.

### Schedule engine

| Topic | Decision |
|---|---|
| When jobs appear | A **rolling 14-day window**. Each daily run creates the next job when its due date is within today + 14 days. The job date is the schedule's `next_due_at`, or today if that date is in the past. The number of days is a config value (`CLEANING_JOB_WINDOW_DAYS`). |
| Next visit date | **From the last clean.** Completed → completion date + frequency. Cancelled by hand → planned date + frequency. The generator does not move `next_due_at`. |
| Open jobs | **One open job per schedule.** The generator skips a schedule that has an open (Scheduled or In Progress) job. |
| Old open jobs | They **stay open and show as overdue**. There is no auto-cancel. |
| Combining | Schedules for the same property that are **due on the same date** share one job. Different dates make separate jobs. |
| Manual jobs | **One-off extras only.** They have no schedule link and do not change any schedule. |
| No price | You cannot create a schedule or switch it on unless a price applies. If a price has ended when the generator runs, the job gets the last known price and a **"price needs review"** flag. |
| After complete or cancel | The generator runs for that property immediately. |
| Reschedule a cancelled job | Blocked if the schedule already has a newer open job. |

### Property lifecycle

| Status change | Decision |
|---|---|
| → Cancelled | Cancel all open jobs (with no schedule move). Switch off the schedules. |
| → Paused | Cancel all open jobs (with no schedule move). The schedules stay on, but no new jobs are made. |
| Paused → Active | An **Activate form** asks for the next visit date for all active schedules. |
| Cancelled → Active | The **Activate form** lists the old schedules with tickboxes and a date. You choose which schedules to switch on. |

A holiday or building work is not a Pause. Cancel the one job instead.

### Prices and services

| Topic | Decision |
|---|---|
| Price change | A **Change price** action closes the old row (`effective_to` = the day before) and adds a new row. **Edit** is for corrections only. Overlapping date ranges are blocked. |
| Delete a service | It becomes **Archive** (soft delete). The archive cascades: the service's prices are soft deleted, its schedules are switched off, and it is removed from open jobs (a job left with no services is cancelled). Completed and cancelled jobs do not change. **Restore** brings back the service and the prices archived with it. The schedules stay off. |
| Remove a price row | It is a **soft delete**. The price history stays in the database. |

### Job workflow and cleaner access

| Topic | Decision |
|---|---|
| Complete | Allowed straight from Scheduled. Start stays optional. |
| Actual price | A **Complete form** lets you confirm or change the price of each service. The value is saved to `actual_price`. |
| Who did it | A new `completed_by` column records the user who completed the job. The cleaner tickboxes stay optional. |
| Your account | You do the round with a **separate Cleaner login**, as an employee would. |
| Cleaner sees | A **"My round"** page: overdue jobs, then today, then the rest of the 14-day window. |
| Cleaner can | Start, Complete (with actual price), Cancel and add job notes. The cleaner cannot reschedule, edit services or prices, or open the admin pages. |
| Cancel | There is one **Cancel** action for all reasons. There is no separate "Skip". |
| Status rules | Scheduled → In Progress, Completed or Cancelled. In Progress → Completed or Cancelled. Cancelled → Scheduled only through Reschedule. The backend enforces these rules. |

### Out of scope for now

- Invoicing and payments
- The Admin dashboard
- Customer archive or delete
- Geocoding and rounds
- Customer portal pages
- Reschedule by a cleaner
