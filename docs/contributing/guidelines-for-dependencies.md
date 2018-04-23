# Guidelines for Dependencies

We care about `composer.json` unity across all repositories and we try to avoid hidden dependencies.
So we created a few rules we follow.

## External dependencies

We use asterisk `*` notation for PHP extensions.

We use caret `^` notation for external dependencies and if there are more major versions possible, we use single pipe `|` notation without spaces.
For example `^6.2.0`, `^7.0`, `^5.0|^6.0|^7.0.4`.

If there is a problem, you can stabilize the dependency in a patch version, for example `6.4.2`.
Please do it in a separate commit with an explanation in the commit message.

## Working with dependencies

After you change a dependency in a package or project-base, you have to reflect the change in the `composer.json` of a given package obviously.

Packages' `composer.json` are not used automatically during development in monorepo.
Monorepo uses root `composer.json` that have to contain all dependencies of all packages.
Monorepo dependencies are managed manually.
If you add or change any dependency in package or project-base, reapply the change into monorepo `composer.json`.

## How to deal with Shopsys dependencies

If the package or project-base depends on a shopsys package, we declare `dev-master` dependency.
During the release, we change `dev-master` dependency to released tag like `7.0.0.-alpha1`.
