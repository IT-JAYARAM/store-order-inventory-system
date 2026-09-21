````markdown

\\# Store Order \\\& Inventory Mini-System



A Laravel-based Store Order \\\& Inventory Mini-System developed as a take-home assignment.



\\## Tech Stack



\\- Laravel 13.4

\\- PHP 8.3

\\- MySQL

\\- Eloquent ORM

\\- PHPUnit

\\- REST APIs

\\- Queue Jobs



\\## Features



\\- Product management

\\- Customer management

\\- Order creation with multiple products

\\- Automatic subtotal, tax and grand total calculation

\\- Stock validation and deduction

\\- Transaction-safe order creation

\\- Concurrency-safe stock deduction

\\- Customer order history API

\\- Configurable low-stock API

\\- Queued order confirmation job

\\- Automated feature tests

\\- Database factories and seeders



\\## Setup



\\### 1. Install dependencies



```bash

composer install

````



\### 2. Configure database



Create a MySQL database named:



```text

store\\\_order\\\_app

```



Configure the database details in `.env`.



```env

DB\\\_CONNECTION=mysql

DB\\\_HOST=127.0.0.1

DB\\\_PORT=3306

DB\\\_DATABASE=store\\\_order\\\_app

DB\\\_USERNAME=root

DB\\\_PASSWORD=

```



\### 3. Generate application key



```bash

php artisan key:generate

```



\### 4. Run migrations and seed data



```bash

php artisan migrate:fresh --seed

```



\### 5. Start the application



```bash

php artisan serve

```



Application:



```text

http://127.0.0.1:8000

```



\## API Endpoints



\### Create Order



```text

POST /api/orders

```



Example request:



```json

{

\&#x20;   "customer": {

\&#x20;       "name": "Arun Kumar",

\&#x20;       "email": "arun@example.com"

\&#x20;   },

\&#x20;   "items": \\\[

\&#x20;       {

\&#x20;           "product\\\_id": 1,

\&#x20;           "quantity": 2

\&#x20;       }

\&#x20;   ]

}

```



\### Customer Order History



```text

GET /api/customers/{email}/orders

```



Example:



```text

GET /api/customers/arun@example.com/orders

```



\### Low Stock Products



```text

GET /api/products/low-stock

```



Custom threshold:



```text

GET /api/products/low-stock?threshold=10

```



Default threshold is 5 and can be configured using:



```env

LOW\\\_STOCK\\\_THRESHOLD=5

```



\## Order Calculation



```text

Line Subtotal = Unit Price × Quantity

Line Tax = Line Subtotal × Tax Percentage / 100

Line Total = Line Subtotal + Line Tax

```



The order stores subtotal, tax and grand total.



Product name, price and tax percentage are also stored in the order item as a snapshot for historical orders.



\## Stock Concurrency



Order creation uses a database transaction with `lockForUpdate()`.



This locks the product row while the order is being processed and prevents multiple transactions from consuming the same available stock.



If only one unit is available and another order attempts to purchase it after the stock has been consumed, the order fails with an insufficient-stock validation error.



\## Queue Job



After a successful order, the following queued job is dispatched:



```text

SendOrderConfirmationJob

```



The job simulates an order confirmation email and records the information in the Laravel log.



To process the queued job:



```bash

php artisan queue:work --once

```



\## Testing



Run the automated tests:



```bash

vendor\\\\bin\\\\phpunit

```



Current result:



```text

OK (4 tests, 15 assertions)

```



Tests cover:



\* Order creation

\* Tax and total calculation

\* Stock deduction

\* Insufficient stock

\* Customer order history

\* Low-stock endpoint

\* Queue dispatch

\* Stock overselling protection



\## Project Structure



```text

app/

├── Http/Controllers/

├── Http/Requests/

├── Jobs/

├── Models/

└── Services/



database/

├── factories/

├── migrations/

└── seeders/



routes/

├── api.php

└── web.php



tests/

└── Feature/

```



\## Design Decisions



\### Service Layer



Order business logic is handled in:



```text

app/Services/OrderService.php

```



This keeps the controller thin and separates business logic from HTTP handling.



\### Form Request



Order validation is handled by:



```text

app/Http/Requests/StoreOrderRequest.php

```



\### Database Transaction



Order creation and stock deduction are performed inside a database transaction.



\### Product Snapshot



Product name, price and tax percentage are stored in `order\\\_items` so historical orders retain the original values even if the product changes later.



\## Assumptions



\* Customer email uniquely identifies a customer.

\* Customers are created automatically when the email does not already exist.

\* Order quantity must be a positive integer.

\* Orders cannot be created when sufficient stock is unavailable.

\* Tax is calculated per order line.

\* Monetary values are stored with two decimal places.



\## AI-Assisted Development



AI assistance was used during development for project structure, Laravel implementation, API design, validation, testing, debugging and documentation.



Prompt evidence is included in the `prompts` directory as required by the assignment.



````






