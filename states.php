<?php
// States list
$states = array(
    'Alabama' => 'alabama', 'Alaska' => 'alaska', 'Arizona' => 'arizona', 'Arkansas' => 'arkansas', 'California' => 'california',
    'Colorado' => 'colorado', 'Connecticut' => 'connecticut', 'Delaware' => 'delaware', 'Florida' => 'florida', 'Georgia' => 'georgia',
    'Hawaii' => 'hawaii', 'Idaho' => 'idaho', 'Illinois' => 'illinois', 'Indiana' => 'indiana', 'Iowa' => 'iowa',
    'Kansas' => 'kansas', 'Kentucky' => 'kentucky', 'Louisiana' => 'louisiana', 'Maine' => 'maine', 'Maryland' => 'maryland',
    'Massachusetts' => 'massachusetts', 'Michigan' => 'michigan', 'Minnesota' => 'minnesota', 'Mississippi' => 'mississippi', 'Missouri' => 'missouri',
    'Montana' => 'montana', 'Nebraska' => 'nebraska', 'Nevada' => 'nevada', 'New Hampshire' => 'new-hampshire', 'New Jersey' => 'new-jersey',
    'New Mexico' => 'new-mexico', 'New York' => 'new-york', 'North Carolina' => 'north-carolina', 'North Dakota' => 'north-dakota', 'Ohio' => 'ohio',
    'Oklahoma' => 'oklahoma', 'Oregon' => 'oregon', 'Pennsylvania' => 'pennsylvania', 'Rhode Island' => 'rhode-island', 'South Carolina' => 'south-carolina',
    'South Dakota' => 'south-dakota', 'Tennessee' => 'tennessee', 'Texas' => 'texas', 'Utah' => 'utah', 'Vermont' => 'vermont',
    'Virginia' => 'virginia', 'Washington' => 'washington', 'West Virginia' => 'west-virginia', 'Wisconsin' => 'wisconsin', 'Wyoming' => 'wyoming'
);

// Create States as Categories
function create_states_as_categories( $taxonomy, $object_type ) {
    if ( 'locations' == $taxonomy ) {
        if ( wp_count_terms( $taxonomy ) <= 0 ) {
            // State Name => State Name as Slug
            foreach ($GLOBALS['states'] as $state => $slug) {
                $term = term_exists( $state, 'locations' );
                if ( 0 == $term && null == $term ) {
                    wp_insert_term( $state, 'locations', array( 'slug' => $slug, ) );
                }
            }
        }
	}
}

?>
