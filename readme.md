# Integration Template

## 1. Clone this repository

## 2. Go to `database/migrations/2018_02_18_130938_create_connections_table.php`

Add columns necessary for the integration (example: e-conomic agreement, Ordrestyring API token or whatever)

## 3. Run migrations

```bash
$ php artisan migrate
```

## 4. Go to `routes/onboarding.php` and look at todos.

Check all `// todo` and `// consider` comments.

## 5. Go to `console/Kernel.php` and `console/Commands/*` and setup sync jobs.

## 6. Update these 3 .env variables:
```
RACKBEAT_DOMAIN=                Should be your local rackbeat host + / (eg: localhost/)
RACKBEAT_ENDPOINT=              Should be your local rackbeat host + /api/ (eg: localhost/api/)
RACKBEAT_INTEGRATION_ENDPOINT=  Should be your local rackbeat host + /integration/ (eg: localhost/integration/)
```
## 7. You should be good to go with the template.

Now, create your own logic etc.   
