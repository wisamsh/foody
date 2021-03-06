<?php

/**
 * The Gravity Forms Query JSON Literal class.
 */
class GF_Query_JSON_Literal extends GF_Query_Literal{
	/**
	 * @var int|string|float The value.
	 */
	private $_value;

	/**
	 * A literal value.
	 *
	 * @param string $value
	 */
	public function __construct( $value ) {
		if ( is_string( $value ) ) {
			$this->value = $value;
		}
	}

	/**
	 * Get SQL for this.
	 *
	 * @param GF_Query $query The query.
	 * @param string $delimiter The delimiter for arrays.
	 *
	 * @return string The SQL.
	 */
	public function sql( $query, $delimiter = '' ) {
		global $wpdb;

		if ( is_string( $this->value ) ) {
			$this->value = str_replace( '/', '\\\\/', $this->value );
			$this->value = str_replace( '"', '\\\\"', $this->value );
			return $wpdb->prepare( '%s', $this->value );
		}

		return '';
	}

	/**
	 * Proxy read-only values.
	 */
	public function __get( $key ) {
		switch ( $key ) :
			case 'value':
				return $this->_value;
		endswitch;
	}
}
