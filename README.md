# laravel-audit-log
Audit Log for laravel

- Use `NeelBhanushali\LaravelAuditLog\Traits\Auditable` trait in your model.

## For advance use
- Lets say you need to audit `roles` but as per `users`.
- Use case: You need to check what/when `roles` were assigned to `users`.
- Add following `AUDIT` constant

```
const AUDIT = [
    'relation' => 'token',
    'entity_id' => 'key',
    'entity_type' => Token::class,
    'parent_id' => 'user_id',
    'parent_type' => User::class
];
```

* `entity_id`, `parent_id` can have following values:
  - `key` : gets value of primary key of current record
  - `column_name` : gets value of column from current record

* `entity_type`, `parent_type` can have following values:
  - `class_path`
  - `column_name` : gets value of column from current record (in case of morph)

