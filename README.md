Magento RewritesOptimizer
=========================

Magento module for optimizing the way URL rewrites are generated. 

Depending on configured options, it can exclude invisible and/or disabled products, as well as the store code from URLs, resulting in a smaller and more manageable URL db table. 
Partly based on AgenceDnd's Patch Index URL module. You can find their module here: http://www.dnd.fr/2012/09/magento-patch-how-to-optimize-re-index-processing-time-for-url-rewrite/

INSTALLATION
============

1. Get files (clone or download zip) and unzip if need be.
2. Copy files from 'html' folder to your Magento root.
3. Clear Magento cache.


USE INSTRUCTIONS
================

1. Go to Magento Admin, System -> Configuration -> Advanced -> Developer, the tab labeled 'URL Rewrites Optimizer'. Choose which options you want enabled.
2. Truncate the 'core_url_rewrite' table.
3. Run the 'Catalog URL Rewrites' from System -> Index Management (or from command line if you have the option).
4. Enjoy!


NOTICE!
=======

The module has been tested on Magento CE 1.8 and CE 1.9. Do not use on production environments without thorough testing!