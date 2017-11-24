# Hop New Relic

## Setup

* Login to your New Relic account on [https://newrelic.com](https://newrelic.com)
* Once logged-ed, go to your *account settings*. In the left sidebar menu, go to *API Keys*
* Copy your API key (generate one if you don't have any)
* Back to EE, go to Hop New Relic add-on settings and paste your New Relic API key. Save the settings
* Now that the key is saved, the *New Relic App* dropdown should be populated, select an app and save the settings
* Now that the app is saved, the *New Relic Server* dropdown should be populated, select one and save the settings (if it's already selected, save the settings anyway)
* Go back to the add-on main page, it should display the app and server summary data


## Usage

If you ever need to display the data summary on the front-end, the add-on provides 3 tags for that:

* *{exp:hop_new_relic:app_data ttl="300"}*
* *{exp:hop_new_relic:server_data ttl="300"}*
* *{exp:hop_new_relic:enduser_data ttl="300"}*

The *ttl* parameter is optionnary, it represents the time (in seconds) the data will be kept in cache before being refreshed. The minimum value is 30 seconds. The default value is 300.

A tag will generate something like `<span>111 ms</span> <span>131 rpm</span> <span>0 err%</span> <span>apdex 0.99</span>`. It's up to you to put-in in a `div` and style it the way you want.

## Support

Having issues ? Found a bug ? Suggestions ? Contact us at [tech@hopstudios.com](mailto:tech@hopstudios.com)


## Changelog

### 1.0.1

* Changes for EE4
* Bug fix in custom datasets

### 1.0.0

* Initial Release

## License
Updated: Jan. 6, 2009

#### Permitted Use

One license grants the right to perform one installation of the Software. Each additional installation of the Software requires an additional purchased license. For free Software, no purchase is necessary, but this license still applies.

#### Restrictions

Unless you have been granted prior, written consent from Hop Studios, you may not:

* Reproduce, distribute, or transfer the Software, or portions thereof, to any third party.
* Sell, rent, lease, assign, or sublet the Software or portions thereof.
* Grant rights to any other person.
* Use the Software in violation of any U.S. or international law or regulation.

#### Display of Copyright Notices

All copyright and proprietary notices and logos in the Control Panel and within the Software files must remain intact.
Making Copies

You may make copies of the Software for back-up purposes, provided that you reproduce the Software in its original form and with all proprietary notices on the back-up copy.

#### Software Modification

You may alter, modify, or extend the Software for your own use, or commission a third-party to perform modifications for you, but you may not resell, redistribute or transfer the modified or derivative version without prior written consent from Hop Studios. Components from the Software may not be extracted and used in other programs without prior written consent from Hop Studios.

#### Technical Support

Technical support is available through e-mail, at sales@hopstudios.com. Hop Studios does not provide direct phone support. No representations or guarantees are made regarding the response time in which support questions are answered.
Refunds

Hop Studios offers refunds on software within 30 days of purchase. Contact sales@hopstudios.com for assistance. This does not apply if the Software is free.
Indemnity

You agree to indemnify and hold harmless Hop Studios for any third-party claims, actions or suits, as well as any related expenses, liabilities, damages, settlements or fees arising from your use or misuse of the Software, or a violation of any terms of this license.

#### Disclaimer Of Warranty

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, WARRANTIES OF QUALITY, PERFORMANCE, NON-INFRINGEMENT, MERCHANTABILITY, OR FITNESS FOR A PARTICULAR PURPOSE. FURTHER, HOP STUDIOS DOES NOT WARRANT THAT THE SOFTWARE OR ANY RELATED SERVICE WILL ALWAYS BE AVAILABLE.

#### Limitations Of Liability

YOU ASSUME ALL RISK ASSOCIATED WITH THE INSTALLATION AND USE OF THE SOFTWARE. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS OF THE SOFTWARE BE LIABLE FOR CLAIMS, DAMAGES OR OTHER LIABILITY ARISING FROM, OUT OF, OR IN CONNECTION WITH THE SOFTWARE. LICENSE HOLDERS ARE SOLELY RESPONSIBLE FOR DETERMINING THE APPROPRIATENESS OF USE AND ASSUME ALL RISKS ASSOCIATED WITH ITS USE, INCLUDING BUT NOT LIMITED TO THE RISKS OF PROGRAM ERRORS, DAMAGE TO EQUIPMENT, LOSS OF DATA OR SOFTWARE PROGRAMS, OR UNAVAILABILITY OR INTERRUPTION OF OPERATIONS.