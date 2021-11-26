<?php
/**
 * GraphQL Field - SelectFieldValue
 * Values for an individual Select field.
 *
 * @package WPGraphQL\GF\Type\WPObject\FormField\FieldProperties\ValueProperty
 * @since   0.5.0
 */

namespace WPGraphQL\GF\Type\WPObject\FormField\FieldValue;

use GF_Field;

/**
 * Class - SelectFieldValue
 */
class SelectFieldValue extends AbstractFieldValue {
	/**
	 * Type registered in WPGraphQL that we are adding the field to.
	 *
	 * @var string
	 */
	public static string $type = 'SelectField';

	/**
	 * Existing type registered in WPGraphQL that we are adding the field to.
	 *
	 * @var string
	 */
	public static string $field_name = 'value';

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'Select field value.', 'wp-graphql-gravity-forms' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_field_type() : string {
		return 'String';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get( array $entry_values, GF_Field $field ) {
		return isset( $entry_values[ $field->id ] ) ? (string) $entry_values[ $field->id ] : null;
	}
}
