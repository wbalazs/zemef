## Quick context (what this repository is)

- Laravel 10 application (PHP >= 8.1) bootstrapped from Creative Tim's "Light Bootstrap Dashboard Pro" starter.
- Main features: user/role management (spatie/permission), items/categories/tags CRUD, observers for file cleanup, many AJAX endpoints and a webshop/stock/sales domain.
- Frontend assets built with Laravel Mix (webpack) and Bootstrap 4; images and user pictures are stored on the `public` disk (use `php artisan storage:link`).

## Where to look first

- app/: Eloquent models, Observers (`app/Observers`), Policies (`app/Policies`), and core controllers are here. Example: `app/Item.php` (uses `tags()` pivot and `path()` -> `/storage/{picture}`).
- routes/web.php: the canonical list of routes. Notice a mix of `Route::resource(...)`, grouped `ajax` and `master` prefixes, and many POST endpoints used by the JS frontend.
- config/zemef.php: small project-specific constants used during imports (e.g. `import_customer_id`, `import_currency_id`).
- database/migrations` and `database/seeders`: seeds create default roles/users (admin/creator/member) — used by the README examples.
- README.md: contains many usage examples and snippets (profile update, Item store flow, Observer deletion). Good source for reproducing behavior.

## Important developer workflows & commands

- Install / bootstrap
  - composer install
  - copy `.env.example` -> `.env` and update DB creds
  - php artisan key:generate
  - php artisan migrate --seed
  - php artisan storage:link (images and profile photos rely on the public disk)

- Local dev assets
  - npm install
  - npm run dev (or `npm run watch`) — Laravel Mix (see `webpack.mix.js` and `package.json`)

- Typical runtime / debug
  - php artisan serve (or use your local Apache/nginx as README suggests)
  - php artisan tinker
  - php artisan route:list (helpful to discover route names used in blade and controllers)

- Tests
  - vendor/bin/phpunit (phpunit.xml present)

## Project-specific conventions & patterns

- Storage and images
  - Controllers store uploaded files with `$request->photo->store('pictures','public')` or `store('profile','public')` and models expose `path()` returning `/storage/{file}`. Always ensure `storage:link` in dev.
  - Deletion is handled by Observers (see `app/Observers/ItemObserver.php`), which call `File::delete(storage_path("/app/public/{$item->picture}"))` and detach pivot relations.

- Validation and Requests
  - Validation lives in `App\Http\Requests` (e.g. `ItemRequest`, `UserRequest`) and commonly uses `Rule::unique((new Model)->getTable())->ignore($this->route()->model->id ?? null)` to handle create vs update.

- Authorization
  - Uses Policies under `app/Policies` (e.g. `ItemPolicy`, `RolePolicy`, `UserPolicy`) and `spatie/laravel-permission` for roles. Check middleware and `Auth` routes in `routes/web.php`.

- Routes and controllers
  - Routes use `Route::resource(..., ['except' => ['show']])` widely (show is often not used). Many AJAX-only endpoints live under the `ajax` prefix and are POST-heavy — changing the JS requires checking these controller methods.
  - Mixing of string controller syntax (`'ProductController@printLabel'`) and class array syntax (`[ProductController::class, 'printLabel']`) — both are present.

## Integration points & 3rd-party packages worth noting

- spatie/laravel-permission — role and permission management (affects seeding and policy checks).
- intervention/image — image manipulation is available in the project (useful when changing uploads).
- spatie/laravel-data — present in composer.json (affects DTO handling if added later).
- barryvdh/laravel-dompdf — used for PDF generation in many /pdf routes (see routes under `ajax` for print endpoints).

## Quick examples to copy-paste (common tasks)

- Create and seed locally

  composer install
  copy .env.example .env
  php artisan key:generate
  php artisan migrate --seed
  php artisan storage:link

- Add an image upload in a controller (consistent with project pattern)

  $path = $request->file('photo')->store('pictures', 'public');
  $model->update(['picture' => $path]);

- Remove image on model deletion (example from observer)

  File::delete(storage_path("/app/public/{$item->picture}"));
  $item->tags()->detach();

## Where to be careful / gotchas

- The project expects a `public` disk storage link; many UI pages show images using `/storage/...` paths — broken symlink => missing images.
- Routes are numerous and contain POST-only AJAX endpoints; changing route names or methods requires updating the JS (search for `ajax/...` endpoints in `public/js` or blade templates).
- Many controllers assume seeded reference data (roles, default users) — if you get authorization errors, confirm the DB seed ran properly.

## If you need more
- Tell me which part you want more detail on (authorization, an API endpoint, migrations, or the frontend asset pipeline). I can expand this file with examples or add quick references to specific controller methods.
