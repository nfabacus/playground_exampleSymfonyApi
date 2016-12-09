NOTES
==========

### Some notes on DB setup:

- Creating the DB

```
php bin/console doctrine:database:create
```

- Updating/creating the schema from our entity annotations

```
php bin/console doctrine:schema:update --force
```
