<?php

  // zh_CN.utf8
  // zh_HK.utf8
  // zh_SG.utf8
  // zh_TW.utf8

  $GLOBALS['cfg']['supported_locales'] = array(
    "en" => array(
      "name" => "English US",
      "gettext" => "en_US.utf8",
      "cosmic" => "en",
    ),
    "cn" => array(
      "name" => "Simplified Chinese",
      "gettext" => "zh_CN.utf8",
      "cosmic" => "zh-Hans-CN",
    ),
    "hk" => array(
      "name" => "Hong Kong Chinese",
      "gettext" => "zh_HK.utf8",
      "cosmic" => "zh-Hans-HK",
    ),
    "tw" => array(
      "name" => "Traditional Chinese",
      "gettext" => "zh_TW.utf8",
      "cosmic" => "zh-Hant",
    ),
  );

  $GLOBALS['cfg']['default_locale'] = "en";
  $GLOBALS['cfg']['locale_cookie_name'] = "locale";

  $GLOBALS['cfg']['enable_feature_gettext'] = 1;


  #######################################################################################

  i18n_init();

  #######################################################################################

  function i18n_init(){

    # 1. Set default locale
    $locale = $GLOBALS['cfg']['default_locale'];

    # 2. Check if there is a cookie
    if (i18n_get_cookie($GLOBALS['cfg']['locale_cookie_name'])){
      $locale = i18n_get_cookie($GLOBALS['cfg']['locale_cookie_name']);
    }

    # 3. Check if there is a user pref
    if ($GLOBALS['cfg']['user']['locale']){
      $locale = $GLOBALS['cfg']['user']['locale'];
    }

    # 4. Check if there is a query param
    if (strlen(get_str("locale"))){

      $locale_new = get_str("locale");

      if (i18n_check_supported($locale_new)){
        $locale = $locale_new;
        $switched_locales = 1;
      }

    }

    #
    # Store locale
    #

    # 1. Set global variable
    $GLOBALS['cfg']['locale'] = $locale;

    # 2. Store locale in cookie
    i18n_set_cookie($GLOBALS['cfg']['locale_cookie_name'], $locale);

    # 3. Save user pref
    if ($GLOBALS['cfg']['user']['locale'] != $locale){
      $update = array(
        "locale" => $locale,
      );

      users_update_user($GLOBALS['cfg']['user'], $update);
    }

    $GLOBALS['cfg']['request_uri'] = trim($_SERVER['REQUEST_URI'], "/");
    $GLOBALS['cfg']['request_uri'] = preg_replace('#\?[^?]*$#', '', $GLOBALS['cfg']['request_uri']);

    #
    # Use gettext if enabled
    #

    if (features_is_enabled("gettext")){

      if (!function_exists('gettext')){
        die("[lib_localization] requires the gettext PHP extension\n");
      }

      $gettext_locale = i18n_get_gettext_locale($locale);
      putenv("LC_ALL=".$gettext_locale);
      setlocale(LC_ALL, $gettext_locale);

      $gettext_domain = "messages";
      $gettext_translations = FLAMEWORK_INCLUDE_DIR . "i18n";
      bindtextdomain($gettext_domain, $gettext_translations);
      textdomain($gettext_domain);

    }

  }

  #################################################################

  function i18n_check_supported($locale){
    return array_key_exists($locale, $GLOBALS['cfg']['supported_locales']);
  }

  #################################################################

  function i18n_get_gettext_locale($locale){
    return $GLOBALS['cfg']['supported_locales'][$locale]['gettext'];
  }

  #################################################################

  function i18n_get_cosmic_locale($locale){
    return $GLOBALS['cfg']['supported_locales'][$locale]['cosmic'];
  }


  #################################################################

  function i18n_get_cookie($name){
    return $_COOKIE[$name];
  }

  #################################################################

  function i18n_set_cookie($name, $value, $expire=0, $path='/'){
    $domain = ($GLOBALS['cfg']['environment'] == 'localhost') ? false : $GLOBALS['cfg']['locale_cookie_domain'];
    $securify = (($GLOBALS['cfg']['locale_cookie_require_https']) && (isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on')) ? 1 : 0;
    $res = setcookie($name, $value, $expire, $path, $domain, $securify);
  }
