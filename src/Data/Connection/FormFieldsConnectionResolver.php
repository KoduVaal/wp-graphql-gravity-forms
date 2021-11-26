<?php
/**
 * ConnectionResolver - RootQueryEntry
 *
 * Resolves connections to FormFields.
 *
 * @package WPGraphQL\GF\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\GF\Data\Connection;

use GFAPI;
use GFFormsModel;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;

/**
 * Class - FormFieldsConnectionResolver
 */
class FormFieldsConnectionResolver {

	/**
	 * Coerces form fields into a format GraphQL can understand.
	 *
	 * Instead of a Model.
	 *
	 * @param array $data array of form fields.
	 */
	public static function prepare_data( array $data ) : array {
		foreach ( $data as &$field ) {
			// Set layoutGridColumnSpan to int.
			$field->layoutGridColumnSpan = ! empty( $field->layoutGridColumnSpan ) ? (int) $field->layoutGridColumnSpan : null;

			// Set empty values to null.
			foreach ( get_object_vars( $field ) as $key => $value ) {
				if ( '' !== $value ) {
					continue;
				}
				$field->$key = null;
			}

			if ( in_array( $field->type, [ 'address', 'name' ], true ) ) {
				foreach ( $field->inputs as $input_index => $input ) {
					// set isHidden to boolean.
					$field->inputs[ $input_index ]['isHidden'] = ! empty( $field->inputs[ $input_index ]['isHidden'] );

					$input_keys = 'address' === $field->type ? self::get_address_input_keys() : self::get_name_input_keys();

					$field->inputs[ $input_index ]['key'] = $input_keys[ $input_index ];
				}
			}

			// Set choices for single-column list fields, so we can use the same mutation for both.
			if ( 'list' === $field->type ) {
				$empty_choices = [
					'text'       => null,
					'value'      => null,
					'isSelected' => null,
					'price'      => null,
				];

				if ( empty( $field['choices'] ) ) {
					$field['choices'] = $empty_choices;
				}
			}
		}

		return $data;
	}

	/**
	 * The connection resolve method.
	 *
	 * @param mixed       $source  The object the connection is coming from.
	 * @param array       $args    Array of args to be passed down to the resolve method.
	 * @param AppContext  $context The AppContext object to be passed down.
	 * @param ResolveInfo $info    The ResolveInfo object.
	 *
	 * @return mixed|array|Deferred
	 */
	public static function resolve( $source, array $args, AppContext $context, ResolveInfo $info ) {
		if ( ! is_array( $source ) || empty( $source ) ) {
			return null;
		}

		$fields = self::prepare_data( $source );

		$connection = Relay::connectionFromArray( $fields, $args );

		$nodes = [];
		if ( ! empty( $connection['edges'] ) && is_array( $connection['edges'] ) ) {
			foreach ( $connection['edges'] as $edge ) {
				$nodes[] = ! empty( $edge['node'] ) ? $edge['node'] : null;
			}
		}

		$connection['nodes'] = ! empty( $nodes ) ? $nodes : null;

		return $connection;
	}

	/**
	 * Returns input keys for Address field.
	 *
	 * @return array
	 */
	private static function get_address_input_keys() : array {
		return [
			'street',
			'lineTwo',
			'city',
			'state',
			'zip',
			'country',
		];
	}

	/**
	 * Returns input keys for Name field.
	 *
	 * @return array
	 */
	private static function get_name_input_keys() : array {
		return [
			'prefix',
			'first',
			'middle',
			'last',
			'suffix',
		];
	}
}
