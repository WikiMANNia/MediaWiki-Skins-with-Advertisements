# MediaWiki Skins with Advertisements

Provide original MediaWiki skins with advertising possibilities

The advertising space 1 alternates randomly with Sitenotice. Advertising space 2 is located at the bottom of the article content. The advertising spaces 3 and 4 are located in the Sidebar. The exact positioning is determined with the entries “*AD1” and “*AD2” in the “MediaWiki:Sidebar”.

IMPORTANT NOTE: The branch "master" contains the original files of the skins monobook, modern, cologneblue and vector. For the skins [colognebanner](https://github.com/WikiMANNia/MediaWiki-Skins-with-Advertisements/tree/REL1_35-colognebanner), [vectorad](https://github.com/WikiMANNia/MediaWiki-Skins-with-Advertisements/tree/REL1_35-vectorad) and [wima](https://github.com/WikiMANNia/MediaWiki-Skins-with-Advertisements/tree/REL1_35-wima) you have to switch and select the corresponding branch!!!

SEE ALSO: More detailed information on [MediaWiki](https://www.mediawiki.org/wiki/Skin:VectorAd#Use).

Two advertising spaces can also be used as event information. These variables have to be set:

* $wgTopBannerType = 'advertising';
* $wgBottomBannerType = 'advertising';
* $wgAdSidebarTopType = 'eventnote';
* $wgAdSidebarBottomType = 'hint';

The default value is “advertising”. These variables can therefore be omitted for advertising insertions.

HTML code must be assigned to these variables:

* $wgTopBannerCode = '';
* $wgBottomBannerCode = '';
* $wgAdSidebarTopCode = '';
* $wgAdSidebarBottomCode = '';

If a variable is not set or contains its string of zero length, the corresponding ad space remains unoccupied.

These variables must be assigned CSS style specifications, for example:

* $wgTopBannerStyle = 'text-align:center;border:1px solid blue;';
* $wgBottomBannerStyle = 'text-align:center;border:1px dotted red;';

The advertising space 1 alternates randomly with Sitenotice. Advertising space 2 is located at the bottom of the article content. The advertising spaces 3 and 4 are located in the sitenotice. The exact positioning is determined with the entries “*AD1” and “*AD2” in the “MediaWiki:Sidebar”.

The skin is localized for the languages "de", "en", "es", "fr", "it", "nl", "pt" and "ru".

The donation button can be hidden by $wgDonationButton and the link to the donation page can be set explicitly by $wgDonationButtonURL:

* $wgDonationButton = false;
* $wgDonationButtonURL = 'yourdomain.org/donationpage.php?lang=';
* $wgDonationButtonIMG = $wgServer.'/images/Donate_Button.gif';

The facebook button can be hidden by $wgFacebookButton and the link to the facebook page can be set explicitly by $wgFacebookButtonURL:

* $wgFacebookButton = false;
* $wgFacebookButtonURL = 'www.facebook.com/pages/YourPage/xxxxxxxxxxxxxxx';
* $wgFacebookButtonIMG = 'yourdomain.org/images/Logo-Facebook.png';

An age classification can be set:

* $wgAgeClassificationButton = true;
* $wgAgeClassificationURL = 'www.altersklassifizierung.de/';
* $wgAgeClassificationIMG = 'yourdomain.org/skins/fsm-aks148.png';
* $wgAgeClassificationMetaName = 'age-de-meta-label';
* $wgAgeClassificationMetaContent = 'age=0 hash: yourdigitalcode v=1.0 kind=sl protocol=all';

