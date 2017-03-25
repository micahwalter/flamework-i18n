# flamework-i18n

A first pass at internationalization for Flamework.

Most of this happens in lib_i18n.php

## init.php

You will need to add `loadlib("i18n");` towards the bottom of your init.php file. If you want to store a user's locale in the db, this must happen after `login_check_login();`.

## Language switching

The first part of `i18n_init()` has to do with detecting and switching the current locale. This happens in three ways.

1. Check if there is already a cookie set
2. Check if the user has a preference set
3. Check if there is a `?local=en` in the URL

This last one takes precedence and it means that you can add `?locale=en` to the end of any URL to switch locales.

Once it's determined that the locale should switch, a global variable is set, the cookie is updated, and the user's preference is updated if they are logged in.

All this means is that you can switch locales. To do something with your locale you can use the global variable to fetch corresponding content from a database or API. In my example I am using Cosmic JS and its localization feature to fetch localized content depending on the currently selected locale.

You can also set up gettext to do local translations. This is especially useful for menu navs and UI elements throughout the site.

## Getext

Installing and setting up gettext is a little involved. In a nutshell you need to make sure it's installed on your server, and then make sure you have all the language packs you want.

You can list you installed language packs with:

    $ locale -a

Next you'll need to set up your translation files and edit them with something like poedit. These are all stored in the `/include/i18n` folder.

Once your translations are in place and compiled, you can use gettext("foo"); in PHP to translate a string.

## Smarty

In a Smarty template I am using this plugin - https://github.com/smarty-gettext/smarty-gettext - Just follow the instructions to install it and then wrap your strings in {t}foo{/t}. You will also need to add a locale setup at the top of your master template.
