# **Magento 2.0 Free Shipping Remaining Cost Extension** #


## Description ##

The extension adds functionality to show the remaining amount that must be spent by the customer to gain free shipping. The extension will display the notification in a block if the free shipping method is enabled, and it has the appropriate minimum order amount (not zero). The module can be configured to set custom amount for calculation, and this will be used instead of the default free shipping order value in this case. If the customer adds products to the cart, the notification will refresh automatically (without page reload). The update also happens for any other interactions if the user removes, modify or update items in the cart.

The administrator can customize the notification text and the block title at the extension’s configuration panel. There are also options how to show tax information as well. The extension can be configured to display the message on the cart page automatically. The administrator can setup the module to show the information box automatically in custom positions like in the sidebar area, content area or almost anywhere on the frontend.

The extension has widget option too with two templates, so it's easy to place the notification block or only the message on cms pages or static blocks. Using the Magento 2.0 widgets functionality, it's very simple to display the block in different areas. If free shipping available for the customer there is an option to show a customizable success message. The progress bar indicator indicates the percentage with a left to right animation. This promotional tool will attract more attention.

## Features ##

- Module enable / disable
- Automatically display notification block or only the message in different areas on the frontend
- Customize notification text and the block title as well
- Enable or disable to show tax information
- All currency support and tax display
- Setup custom amount for calculation to use instead of the default free shipping order amount
- The widget(s) can be placed anywhere easily on the store frontend in Magento 2.0
- Progress bar indicator
- Custom success message
- Multistore support
- Supported languages: English

Individual module, i. e. it does not modify the standard Magento 2.0 files.
 
Support:
Magento Community Edition  2.1.x, 2.2.x

## Installation ##

** Important! Always install and test the extension in your development environment, and not on your live or production server. **
 
1. Backup Your Data 
Backup your store database and web directory. 
 
2. Clear Cache and cookies 
Clear the store cache under var/cache and all cookies for your store domain. 
 
3. Disable Compilation 
Disable Compilation, if it’s enabled.

4. Upload Files 
Unzip extension contents on your computer and navigates inside the extracted folder. Create folder app/code on your web server if you don't have it already. Using your FTP client upload the content of the directory to your store root/app/code folder.
Important! If the module contents don't include the Me/Lff directory in the zip file, you must create root/app/code/Me/Lff folder and upload the extension here.

5. Enable extension
Please use the following commands in the /bin directory of your Magento 2.0 instance:

    php magento module:enable Me_Lff

    php magento setup:upgrade 

One more time clear the cache under var/cache and var/page_cache login to Magento backend (admin panel).

## Configuration ##
 
Login to Magento backend (admin panel).  You can find the module configuration here: Stores / Configuration, in the left menu Magevolve Extensions / Free Shipping Remaining Cost.

## Change Log ##

Version 1.0.4 - Apr 2, 2018
- Compatibility with Magento 2.2.x
- Compatibility with Magento 2.1.x
- Layout handle fix
- Success message fix

Version 1.0.3 - Feb 4, 2018
- Compatibility with Magento 2.2.x
- Compatibility with Magento 2.1.x
- Code Sniffer fixes

Version 1.0.2 - May 9, 2017
- Add success message and progress indicator.

Version 1.0.1 - Feb 21, 2017
- Compatibility with Magento 2.1.x
- Compatibility with Magento 2.0.x

Version 1.0.0 - Sep 10, 2016
- Compatibility with Magento 2.0.x

## Troubleshooting ##
 
1. After the extension installation I receive a 404 error in Stores / Configuration / Free Shipping Remaining Cost. 
Clear the store cache, browser cookies, logout from backend and login back. 
 
2. My configuration changes do not appear on the store frontend.
Clear the store cache, clear your browser cache and domain cookies and refresh the page. 
 
## Extension license ##
 
The module license description included in the Terms and Conditions:
http://magevolve.com/terms-and-conditions  
 
## Support ##
 
If you have any questions about the extension, please contact us.

## License ##

See COPYING.txt for license details.

Copyright © 2016 Magevolve Ltd. All rights reserved.