<?php 
/**
 * Micro Data Class
 * This class is used to provide micro data per Schema.org's spec,
 * to elements in the site.
 *
 */
PLS_Micro_Data::init();

class PLS_Micro_Data {

	public static function init () {
		add_filter('wp_head', array(__CLASS__, 'schema_org_head_tags'));
	}

	public static function schema_org_head_tags () {
	    // Take meta tag designations, and apply them to the HTML elements
	    $tags = PLS_Meta_Tags::determine_appropriate_tags();

	    ob_start();
		?>
			<!-- Schema.org Tags -->
			<meta itemprop="name" content="<?php echo $tags['title']; ?>">
			<meta itemprop="email" content="<?php echo $tags['email']; ?>">
			<meta itemprop="address" content="<?php echo $tags['address']; ?>">
			<meta itemprop="description" content="<?php echo $tags['description']; ?>">
			<meta itemprop="url" content="<?php echo $tags['url']; ?>">
		<?php
		$tags_html = ob_get_clean();

		echo $tags_html;
	}

	public static function itemtype ($section_template, $include_itemscope = true) {
		$schema_url = 'http://schema.org/';

		// retrieve itemtype translations 
		$itemtype_library = self::get_itemtype_translations();

		// Assemble itemtype string
		$itemtype = '';

		if ($include_itemscope) {
			// Add 'itemscope'
			$itemtype = 'itemscope ';
		}

		$itemtype .= 'itemtype="' . $schema_url . $itemtype_library[$section_template] . '"';

		return $itemtype;
	}


	public static function itemprop ( $itemtype, $itemprop ) {
		// For Schema.org implementation
		$itemtypes = self::get_itemtype_translations();
		$itemprops = self::get_itemprop_library();

		$attr = '';
		if (in_array($itemprop, $itemprops[$itemtypes[$itemtype]])) {
	    	// Assemble itemprop string
			$attr = 'itemprop="' . $itemprop . '"';
		}

		return $attr;
	}

	private static function get_itemtype_translations () {
		// itemtypes with readable labels for easy use
		//
		// This also let's us change the values of labels if, for example, we decide that
		// single property pages shouldn't be itemtype'd to 'Offer', but instead to 'Place'
    	$itemtypes = array(
			'html' => 'WebPage',
			'company' => 'LocalBusiness',
			'header' => 'WPHeader',
			'footer' => 'WPFooter',
			'sidebar' => 'WPSideBar',
			'search' => 'SearchResultsPage',
			'nav' => 'SiteNavigationElement',
			'local-business' => 'LocalBusiness',
			'organization' => 'Organization',
			// 'place' => 'Place',
			'agent' => 'RealEstateAgent',
			'service' => 'ProfessionalService',
			'testimonial' => 'Review',
			// 'person' => 'Person',
			'listing' => 'Offer',
			'blog' => 'Blog',
			'single-post' => 'BlogPosting',
			'contact-point' => 'ContactPoint',
			'postal-address' => 'PostalAddress'
		);

		return $itemtypes;
	}


	// use schema.org's library to return Schema Item Types
	private static function get_itemprop_library () {
		// Before adding new properties to this library, check: http://schema.org

		$Thing = array(
			'image',
			'url',
			'name',
			'description',
			'sameAs'
		);

		// Thing > CreativeWork
		$CreativeWork = self::array_unique_merge(
			$Thing,
			array(
			  	'author',
				'datePublished',
				'keywords', 			// tags
				'headline', 			// not name, but secondary headline
				'about', 					// excerpt
				'creator' 				// for webpage?
			)
		);

		// Thing > CreativeWork > Blog
		$Blog = self::array_unique_merge(
			$CreativeWork,
			array('blogPost')
		);

		// Thing > CreativeWork > Article
		$Article = self::array_unique_merge(
		  	$CreativeWork,
		  	array(
				'articleBody',
				'articleSection' 	// category
			)
		);

		// Thing > CreativeWork > Article > BlogPosting
		$BlogPosting = $Article;

		// Thing > CreativeWork > Review
		$Review = self::array_unique_merge(
			$CreativeWork,
			array(
				'itemReviewed',
				'reviewBody',
				'reviewRating'
			)
		);

		// Thing > CreativeWork > WebPage
		$WebPage = self::array_unique_merge(
			$CreativeWork, 
			array('breadcrumb')
		);

		// Thing > CreativeWork > WebPage > ContactPage
		$ContactPage = $WebPage;

		// Thing > CreativeWork > WebPage > AboutPage
		$AboutPage = $WebPage;

		// Thing > CreativeWork > WebPage > SearchResultsPage
		$SearchResultsPage = $WebPage;

		// Thing > CreativeWork > WebPage > WebPageElement
		$WebPageElement = $WebPage;

		// Thing > CreativeWork > WebPage > WebPageElement > WPHeader
		$WPHeader = $WebPageElement;

		// Thing > CreativeWork > WebPage > WebPageElement > WPFooter
		$WPFooter = $WebPageElement;

		// Thing > CreativeWork > WebPage > WebPageElement > WPSidebar
		$WPSideBar = $WebPageElement;

		// Thing > CreativeWork > WebPage > WebPageElement > WPSiteNavigationElement
		$SiteNavigationElement = $WebPageElement;

		// Thing > Intagible > Offer
		$Offer = self::array_unique_merge(
			$Thing,
			array(
			  	'availability', 		// avail-on
				'businessFunction', // rent/buy/lease
				'category', 				// property-type ?
				'price',
				'review', 					// user reviews ?
				'seller', 					// matched agent or compliance
				'serialNumber' 			// MLS #
			)
		);

		// Thing > Organization
		$Organization = self::array_unique_merge(
			$Thing,
			array(
				'email',
				'employee',
				'founder',
				'foundingDate',
				'location'
			)
		);

		// Thing > Place
		$Place = self::array_unique_merge(
			$Thing,
			array(
				'address',
				'faxNumber',
				'logo',
				'map',
				'photo',
				'telephone'
			)
		);

		// Thing > Intangible > StructuredValue > ContactPoint
		$ContactPoint = self::array_unique_merge(
			$Thing,
			array(
				'contactType',
				'email',
				'faxNumber',
				'telephone'
			)
		);

		// Thing > Intangible > StructuredValue > ContactPoint > PostalAddress
		$PostalAddress = self::array_unique_merge(
			$ContactPoint,
			array(
		  		'addressCountry',
				'addressLocality',
				'addressRegion',
				'postalCode',
				'postOfficeBoxNumber',
				'streetAddress'
			)
		);

		// Thing > Organization/Place > Local Business
		$LocalBusiness = self::array_unique_merge(
			$Place,
			$Organization,
			array(
				'branchOf',
				'openingHours'
			)
		);

		// Thing > Organization/Place > Local Business > RealEstateAgent
		$RealEstateAgent = $LocalBusiness;

		// Thing > Organization/Place > Local Business > ProfessionalService
		$ProfessionalService = $LocalBusiness;

		$available_itemprops = array(
			'Thing' => $Thing,
			'CreativeWork' => $CreativeWork,
			'Blog' => $Blog,
			'Article' => $Article,
			'BlogPosting' => $BlogPosting,
			'Review' => $Review,
			'WebPage' => $WebPage,
			'ContactPage' => $ContactPage,
			'AboutPage' => $AboutPage,
			'SearchResultsPage' => $SearchResultsPage,
			'WebPageElement' => $WebPageElement,
			'WPHeader' => $WPHeader,
			'WPFooter' => $WPFooter,
			'WPSideBar' => $WPSideBar,
			'SiteNavigationElement' => $SiteNavigationElement,
			'Offer' => $Offer,
			'Organization' => $Organization,
			'ContactPoint' => $ContactPoint,
			'PostalAddress' => $PostalAddress,
			'Place' => $Place,
			'LocalBusiness' => $LocalBusiness,
			'RealEstateAgent' => $RealEstateAgent,
			'ProfessionalService' => $ProfessionalService
		);

		return $available_itemprops;
	}

	// merge arrays and remove duplicates
	private static function array_unique_merge () {
		$arrays = func_get_args();
		$merged_arrays = array();

		foreach ($arrays as $arr) {
			if (is_array($arr)) {
				$merged_arrays = array_merge($merged_arrays, $arr);
			}
		}

		return array_unique($merged_arrays);
	}

}