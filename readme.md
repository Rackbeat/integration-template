# Integration Template

## Chronological todo list

- [ ] Clone this repository
- [ ] Go to `database/migrations/2018_02_18_130938_create_connections_table.php`.

Add columns necessary for the integration (example: e-conomic agreement, Ordrestyring API token or whatever)

- [ ] Run migrations

```bash
$ php artisan migrate
```

- [ ] Go to `app/Http/Controllers/OnboardingController.php` and look at todos.

Check all `// todo` and `// consider` comments.

- [ ] Go to `console/Kernel.php` and `console/Commands/*` and setup sync jobs.

- [ ] Update these 3 .env variables:

```
RACKBEAT_DOMAIN=                Should be your local rackbeat host + / (eg: localhost/)
RACKBEAT_ENDPOINT=              Should be your local rackbeat host + /api/ (eg: localhost/api/)
RACKBEAT_INTEGRATION_ENDPOINT=  Should be your local rackbeat host + /integration/ (eg: localhost/integration/)
```

- [ ] You should be good to go with the template.

Now, create your own logic etc.   
