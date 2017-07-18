var GeneanetEmbeddedIndividual = (function( $ ) {
	'use strict';

	/**
	 * All of the code for public-facing JavaScript source should reside in this file.
	 */

	/**
	 * Capitalize the first letter of a string.
	 *
	 * @param {string} string
	 * @returns {string}
	 */
	function capitalizeFirstLetter( string ) {
		return string.charAt( 0 ).toUpperCase() + string.slice( 1 );
	}

	/**
	 * Determine if there is data.
	 *
	 * @param {*} data
	 * @returns {boolean}
	 */
	function hasData( data ) {
		return ! ( 'undefined' === typeof data || null === data || '' === data || '?' === data );
	}

	/**
	 * Manage an element without data.
	 *
	 * @param {jQuery} $element
	 */
	function elementNoData( $element ) {
		$element.addClass( 'geneanet-embedded-individual-no-data' ).removeClass( 'geneanet-embedded-individual-loading' );
	}

	/**
	 * Display a field containing a date and a place.
	 *
	 * @param {jQuery} $element
	 * @param {string} date
	 * @param {string} dateText
	 * @param {string} place
	 */
	function displayDateAndPlace( $element, date, dateText, place ) {
		var hasDate = false;
		var hasPlace = false;
		var placeText = '';

		if ( hasData( date ) ) {
			hasDate = true;
			$element.children( '.geneanet-embedded-individual-date' ).html( capitalizeFirstLetter( dateText ) );
		}
		if ( hasData( place ) ) {
			hasPlace = true;
			if ( hasDate ) {
				placeText += ' - ';
			} else {
				place = capitalizeFirstLetter( place );
			}
			placeText += place;
			$element.children( '.geneanet-embedded-individual-place' ).html( placeText );
		}

		if ( ! hasDate && ! hasPlace ) {
			elementNoData( $element.parent( '.geneanet-embedded-individual-loading' ) );
		}
	}

	/**
	 * Display a field containing a full name.
	 *
	 * @param {jQuery} $element
	 * @param person
	 * @param person.firstname
	 * @param person.lastname
	 */
	function displayFullName( $element, person ) {
		var fullName = '';

		if ( hasData( person ) ) {
			if ( hasData( person.firstname ) ) {
				fullName += person.firstname;
			}
			if ( hasData( person.lastname ) ) {
				if ( fullName.length > 0 ) {
					fullName += ' ';
				}
				fullName += person.lastname;
			}

			if ( fullName.length > 0 ) {
				$element.html( fullName );
			} else {
				elementNoData( $element.parent( '.geneanet-embedded-individual-loading' ) );
			}
		} else {
			elementNoData( $element.parent( '.geneanet-embedded-individual-loading' ) );
		}
	}

	/**
	 * Display a field containing the families.
	 *
	 * @param {jQuery} $element
	 * @param {Array}  families
	 * @param {int}    sex
	 */
	function displayFamilies( $element, families, sex ) {
		var familiesText = '';
		var numberFamilies = 0;
		var hasSpouseFirstName = false;
		var i;

		if ( hasData( families ) ) {
			for ( i = 0; i < families.length; i++ ) {
				if ( hasData( families[ i ].spouse ) && ( hasData( families[ i ].spouse.firstname ) || hasData( families[ i ].spouse.lastname ) ) ) {
					familiesText += '<span class="geneanet-embedded-individual-date">';

					if ( ! hasData( families[ i ].marriage_type ) || 0 === families[ i ].marriage_type ) {
						if ( 0 === sex ) {
							familiesText += window.top.objectL10n.families_married_male;
						} else if ( 1 === sex ) {
							familiesText += window.top.objectL10n.families_married_female;
						} else {
							familiesText += window.top.objectL10n.families_married_unknown;
						}
					} else if ( 1 === families[ i ].marriage_type || 4 === families[ i ].marriage_type ) {
						familiesText += window.top.objectL10n.families_relation;
					} else if ( 2 === families[ i ].marriage_type ) {
						if ( 0 === sex ) {
							familiesText += window.top.objectL10n.families_engaged_male;
						} else if ( 1 === sex ) {
							familiesText += window.top.objectL10n.families_engaged_female;
						} else {
							familiesText += window.top.objectL10n.families_engaged_unknown;
						}
					}

					if ( hasData( families[ i ].marriage_date_text ) ) {
						familiesText += ' ' + families[ i ].marriage_date_text;
					}
					familiesText += '</span> ' + window.top.objectL10n.families_with + ' ';

					hasSpouseFirstName = false;
					if ( hasData( families[ i ].spouse.firstname ) ) {
						familiesText += families[ i ].spouse.firstname;
						hasSpouseFirstName = true;
					}
					if ( hasData( families[ i ].spouse.lastname ) ) {
						if ( true === hasSpouseFirstName ) {
							familiesText += ' ';
						}
						familiesText += families[ i ].spouse.lastname;
					}
					if ( hasData( families[ i ].children ) && 0 < families[ i ].children.length ) {
						familiesText += ' ';
						if ( 1 === families[ i ].children.length ) {
							familiesText += window.top.objectL10n.families_with_one_child;
						} else {
							familiesText += window.top.objectL10n.families_with_children.replace( '%d', families[ i ].children.length );
						}
					}
					if ( i < families.length - 1 ) {
						familiesText += '<br>';
					}
					numberFamilies++;
				}
			}
			if ( 0 < numberFamilies ) {
				$element.html( familiesText );
			} else {
				elementNoData( $element.parent( '.geneanet-embedded-individual-loading' ) );
			}
		}
	}

	/**
	 * Load a Geneanet Embedded Individual.
	 *
	 * @param {Element} element  Element containing the plugin.
	 * @param {string}  basename Basename to use for loading the data.
	 * @param {string}  index    Index of the individual to use for loading the data.
	 */
	function loadGeneanetEmbeddedIndividual( element, basename, index ) {
		var $element = $( element );

		$.ajax( window.top.objectL10n.api_url + '?basename=' + basename + '&i=' + index + '&lang=' + window.top.objectL10n.lang + '&origin=geneanet-embedded-individual', {
			success: function( data ) {
				var $content = $( $element.find( '.geneanet-embedded-individual-content' ) );

				var sex = 2;
				if ( 'undefined' !== typeof data.sex ) {
					sex = data.sex;
				}

				var $sosa = $( $content.find( '.geneanet-embedded-individual-sosa' ) );
				var $sosaNumber = $( $content.find( '.geneanet-embedded-individual-sosa-number' ) );
				if ( 'undefined' !== typeof data.sosa_nb ) {
					$sosaNumber.html( data.sosa_nb );
				} else {
					elementNoData( $sosa );
				}
				if ( 'undefined' !== typeof data.image ) {
					$( $content.find( '.geneanet-embedded-individual-image' ) ).attr( 'src', data.image.replace( /(http|https):\/\//, '//' ) );
				}
				displayDateAndPlace( $( $content.find( '.geneanet-embedded-individual-birth' ) ), data.birth_date_raw, data.birth_text, data.birth_place );
				displayDateAndPlace( $( $content.find( '.geneanet-embedded-individual-death' ) ), data.death_date_raw, data.death_text, data.death_place );
				displayFullName( $( $content.find( '.geneanet-embedded-individual-father' ) ), data.father );
				displayFullName( $( $content.find( '.geneanet-embedded-individual-mother' ) ), data.mother );
				displayFamilies( $( $content.find( '.geneanet-embedded-individual-families' ) ), data.families, sex );

				$content.find( '.geneanet-embedded-individual-loader' ).fadeOut( 700 );
				$content.find( '.geneanet-embedded-individual-loading' ).fadeIn( 700 ).removeClass( 'geneanet-embedded-individual-loading' )
					.filter( '.geneanet-embedded-individual-sosa' ).css( 'display', 'inline-block' );
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				var $content = $( $element.find( '.geneanet-embedded-individual-content' ) );

				$content.find( '.geneanet-embedded-individual-loader' ).fadeOut( 700 );

				var $error = $content.find( '.geneanet-embedded-individual-error' );
				if ( 0 === jqXHR.status ) {
					$error.html( window.top.objectL10n.error_ajax_request ).fadeIn( 700 );
				} else if ( 404 === jqXHR.status ) {
					$error.html( window.top.objectL10n.error_individual_not_found ).fadeIn( 700 );
				} else {
					$error.html( window.top.objectL10n.error_geneanet_api + jqXHR.status ).fadeIn( 700 );
				}
			},
			crossDomain: true
		});
	}

	return {
		loadGeneanetEmbeddedIndividual: loadGeneanetEmbeddedIndividual
	};

})( jQuery );
