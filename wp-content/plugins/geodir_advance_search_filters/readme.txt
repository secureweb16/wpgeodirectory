=== GeoDirectory Advanced Search Filters ===
Contributors: stiofansisland, paoltaia, ayecode
Donate link: https://wpgeodirectory.com
Tags: advance search, geodirectory, geodirectory search, search
Requires at least: 4.9
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 2.2.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

GeoDirectory Advanced Search Filters plugin allows to expands the default GeoDirectory search functionality by adding a range of filters.

== Description ==

The GeoDirectory Advanced Search Filters plugin expands the default GeoDirectory search widget by adding a range of filters such as:
- Search Autocompleter
- GeoLocation
- Proximity Search
- Radius Search
- Filter by any custom field

The possibilities are limitless and your users will love their Advanced Search experience.

The Advanced Search add-on enables Ajax Autocompleter function to both of GeoDirectory search fields: "Search For" and "Near".

The "Search For" field will search for keywords in the listing title, text content, categories and tags.

While the "Near" field will query locations and return results sorted by distance.

== Installation ==

1. Upload 'geodir_advance_search_filters' directory to the '/wp-content/plugins/' directory
2. Activate the plugin "GeoDirectory Advance Search Filters" through the 'Plugins' menu in WordPress

== Changelog ==

= 2.2.1 =
* Select dropdown placeholder should show field title instead of "Select option" - CHANGED
* Time search input always shows military time - FIXED
* Search field range type not saved - FIXED

= 2.2 =
* Changes to support GeoDirectory v2.2 new settings UI - CHANGED

= 2.1.1.1 =
* Open Hours by days shows incorrect results for some timezone - FIXED

= 2.1.1.0 =
* Classifieds/Real-estate Sold Functionality changes - ADDED

= 2.1.0.9 =
* Show checkbox field label instead of "Yes" text in search - CHANGED
* Address field is missing in search field setting - FIXED

= 2.1.0.8 =
* GD > Search block shows block validation error in console - FIXED
* Fieldset in advance search field breaking HTML with AUI - FIXED

= 2.1.0.7 =
* AUI Datepicker is not working date fields loaded via CPT change in search form - FIXED

= 2.1.0.6 =
* Unable to change search bar category label from field setting - FIXED
* Less/more toggle don't shows optgroup labels - FIXED

= 2.1.0.5 =
* Business Hours field web accessibility issue - FIXED
* Field to filter posts by Service Distance added - ADDED

= 2.1.0.4 =
* Show month & year dropdown in search form datepicker - CHANGED

= 2.1.0.3 =
* Mobile scroll over advance search category trigger click event - FIXED
* Search field LINK list shows incorrect url - FIXED

= 2.1.0.2 =
* .hide class in advance search more option create conflict - FIXED
* Search suggestions for AUI styles changed to bootstrap dropdown for better overflow ability - CHANGED
* Checkboxes are not left aligned in Supreme with bootstrap style - FIXED
* Advanced search categories should show multiple levels of sub cats - FIXED

= 2.1.0.1 =
* Change Jquery doc ready to pure JS doc ready so jQuery can be loaded without render blocking  - CHANGED
* Price range field is not working properly with bootstrap style - FIXED

= 2.1.0.0 =
* Changes for AyeCode UI compatibility - CHANGED

= 2.0.1.2 =
* Web accessibility compatibility changes - CHANGED

= 2.0.1.1 =
* Open Now search shows incorrect results when used in advance toggle search bar - FIXED
* Open Now with weekend search shows duplicate results - FIXED

= 2.0.1.0 =
* Open Now search functionality for listing business hours - FIXED

= 2.0.0.17 =
* Datepicker is not working when multiple search forms are on the page - FIXED

= 2.0.0.16 =
* Chrome browser shows category field in main search bar shifted - FIXED

= 2.0.0.15 =
* Search form advance fields layout don't shows labels - FIXED

= 2.0.0.14 =
* REST API allow search posts by GPS, IP and near address - ADDED
* JS variable conflict with Rank Math plugin - FIXED

= 2.0.0.13 =
* Option added to show listings from child categories for searched parent category - CHANGED
* Category should be auto-selected on category archive page - CHANGED
* Datepicker in search form shows untranslated text - FIXED
* Delete subsite removes data from main site on multisite network - FIXED
* Autocomplete results categories should redirect to searched location - FIXED

= 2.0.0.12 =
* Option added to hide posts/categories from search suggestions - ADDED

= 2.0.0.11 =
* Autocomplete given a slight delay between key presses before sending request to minimise requests to the server - CHANGED

= 2.0.0.10 =
* Search for category goes to 404 page when Location Manager not installed - FIXED

= 2.0.0.9 =
* Allow to search listings with special offers & video - ADDED
* v1 to v2 conversion can set autocompleter_max_results to blank - FIXED
* Translations in some cases can break the autocompleter - FIXED

= 2.0.0.8 =
* Add clear db version feature to diagnose plugin data - ADDED

= 2.0.0.7 =
* Static search_sort() method called in a way that does not work with older PHP versions - FIXED

= 2.0.0.6 =
* Some autocomplete search texts are not translated - FIXED
* Search by distance not working - FIXED

= 2.0.0.5 =
* Clearing near searched parameter does not clear GPS info - FIXED

= 2.0.0.4 =
* Advanced search shortcode always open parameter not working - FIXED
* Advanced search block always open parameter not showing - FIXED

= 2.0.0.3-beta =
* Changes for upcoming events addon - CHANGED

= 2.0.0.2-beta =
* Changes for CPT addon compatibility - CHANGED
* Uninstall settings function updated to latest version - CHANGED

= 2.0.0.1-beta =
* Old body class filter can cause problems on search page - FIXED

= 2.0.0.0-beta =
* Initial beta release - INFO
