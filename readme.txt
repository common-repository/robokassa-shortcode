=== Robokassa Shortcode ===
Contributors: loomst
Tags: robokassa, payment getaway, ecommerce
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to place a payment button in any Robokassa your post or on any page.
== Description ==
Place the shortcode provide a description of payment and amount.

[rk_button price="100" sku="test" description="Test payment"]

* В Robokassa прописываем:
*
* Оповещение о платеже - [имя сайта]/rksc/res.php
* Ссылка об удачном платеже - [имя сайта]/rksc/success.php
* Ссылка о неудачном платеже - [имя сайта]/rksc/fail.php

В ближайшее время в плагине будут доступны пользовательские поля для ваших форм, список поступлений денег. Следите за обновлениями
  


== Installation ==

1. Unzip and upload contents of the plugin to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==
= 1.4.2 =
* Small translation fixes
= 1.4.1 =
* Small cosmetic changes

= 1.4 =
* Fix problem with utf-8 convertation on hostings without iconv
* Add parameter to turn off admin notification about new payment

= 1.3 =
* Added robokassa shortcode mediabutton
* Fixed localization

= 1.2 = 
* Added widget for robokassa payments
* Fixed some bugs


= 1.1 =
* Added featre: If you do not enter price value user can enter them by them self 
* Fix problem with multiple forms on one page
* adder russian localization
    
= 1.0 =
* First public release


