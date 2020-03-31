<?php
/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

/**
 * Hybrid_User_Profile object represents the current logged in user profile.
 * The list of fields available in the normalized user profile structure used by HybridAuth.
 *
 * The Hybrid_User_Profile object is populated with as much information about the user as
 * HybridAuth was able to pull from the given API or authentication provider.
 *
 * http://hybridauth.sourceforge.net/userguide/Profile_Data_User_Profile.html
 */
class Hybrid_User_Profile {
	/**
	 * The Unique user's ID on the connected provider
	 * @var Numeric
	 */
	public $identifier = null;

	/**
	 * User website, blog, web page
	 * @var String
	 */
	public $webSiteURL = null;

	/**
	 * URL link to profile page on the IDp web site
	 * @var String
	 */
	public $profileURL = null;

	/**
	 * URL link to user photo or avatar
	 * @var String
	 */
	public $photoURL = null;

	/**
	 * User displayName provided by the IDp or a concatenation of first and last name.
	 * @var String
	 */
	public $displayName = null;

	/**
	 * A short about_me
	 * @var String
	 */
	public $description = null;

	/**
	 * User's first name
	 * @var String
	 */
	public $firstName = null;

	/**
	 * User's last name
	 * @var String
	 */
	public $lastName = null;

	/**
	 * male or female
	 * @var String
	 */
	public $gender = null;

	/**
	 * Language
	 * @var String
	 */
	public $language = null;

	/**
	 * User age, we don't calculate it. we return it as is if the IDp provide it.
	 * @var Numeric
	 */
	public $age = null;

	/**
	 * User birth Day
	 * @var Numeric
	 */
	public $birthDay = null;

	/**
	 * User birth Month
	 * @var Numeric/String
	 */
	public $birthMonth = null;

	/**
	 * User birth Year
	 * @var Numeric
	 */
	public $birthYear = null;

	/**
	 * User email. Note: not all of IDp grant access to the user email
	 * @var String
	 */
	public $email = null;

	/**
	 * Verified user email. Note: not all of IDp grant access to verified user email
	 * @var String
	 */
	public $emailVerified = null;

	/**
	 * Phone number
	 * @var String
	 */
	public $phone = null;

	/**
	 * Complete user address
	 * @var String
	 */
	public $address = null;

	/**
	 * User country
	 * @var String
	 */
	public $country = null;

	/**
	 * Region
	 * @var String
	 */
	public $region = null;

	/**
	 * City
	 * @var String
	 */
	public $city = null;

	/**
	 * Postal code
	 * @var String
	 */
	public $zip = null;
}
