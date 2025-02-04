<?php
/**
 * Test NameField.
 *
 * @package Tests\WPGraphQL\GF
 */

use Tests\WPGraphQL\GF\TestCase\FormFieldTestCase;
use Tests\WPGraphQL\GF\TestCase\FormFieldTestCaseInterface;
/**
 * Class -NameFieldTest
 */
class NameFieldTest extends FormFieldTestCase implements FormFieldTestCaseInterface {
	/**
	 * Tests the field properties and values.
	 */
	public function testField(): void {
		$this->runTestField();
	}
	/**
	 * Tests submitting the field values as a draft entry with submitGfForm.
	 */
	public function testSubmitDraft(): void {
		$this->runTestSubmitDraft();
	}
	/**
	 * Tests submitting the field values as an entry with submitGfForm.
	 */
	public function testSubmit(): void {
		$this->runTestSubmit();
	}
	/**
	 * Tests updating the field value with updateGfEntry.
	 */
	public function testUpdate(): void {
		$this->runTestUpdate();
	}
	/**
	 * Tests updating the draft field value with updateGfEntry.
	 */
	public function testUpdateDraft():void {
		$this->runTestUpdateDraft();
	}

	/**
	 * Sets the correct Field Helper.
	 */
	public function field_helper() {
		return $this->tester->getPropertyHelper( 'NameField' );
	}

	/**
	 * Generates the form fields from factory. Must be wrappend in an array.
	 */
	public function generate_fields() : array {
		return [ $this->factory->field->create( $this->property_helper->values ) ];
	}

	/**
	 * The value as expected in GraphQL.
	 */
	public function field_value() {
		return [
			'first'  => 'Martin',
			'last'   => 'King',
			'middle' => 'Luthor',
			'prefix' => 'Dr.',
			'suffix' => 'Jr.',
		];
	}

	/**
	 * The graphql field value input.
	 */
	public function field_value_input() {
		return $this->field_value;
	}

	/**
	 * Sets the value as expected by Gravity Forms.
	 */
	public function value() {
		return [
			$this->fields[0]['inputs'][0]['id'] => $this->field_value['prefix'],
			$this->fields[0]['inputs'][1]['id'] => $this->field_value['first'],
			$this->fields[0]['inputs'][2]['id'] => $this->field_value['middle'],
			$this->fields[0]['inputs'][3]['id'] => $this->field_value['last'],
			$this->fields[0]['inputs'][4]['id'] => $this->field_value['suffix'],
		];
	}

	/**
	 * The value as expected in GraphQL when updating from field_value().
	 */
	public function updated_field_value() {
			return [
				'first'  => 'Martina',
				'last'   => 'Queen',
				'middle' => 'Lucia',
				'prefix' => 'Prof.',
				'suffix' => 'Sr.',
			];
	}

	/**
	 * The GraphQL query string.
	 *
	 * @return string
	 */
	public function field_query() : string {
		return '
			... on NameField {
				adminLabel
				canPrepopulate
				conditionalLogic {
					actionType
					logicType
					rules {
						fieldId
						operator
						value
					}
				}
				cssClass
				description
				descriptionPlacement
				errorMessage
				hasAutocomplete
				inputName
				inputs {
					autocompleteAttribute
					choices {
						isSelected
						text
						value
					}
					customLabel
					defaultValue
					hasChoiceValue
					id
					isHidden
					key
					label
					name
					placeholder
				}
				isRequired
				labelPlacement
				label
				personalData {
					isIdentificationField
					shouldErase
					shouldExport
				}
				subLabelPlacement
				nameValues {
					first
					last
					middle
					prefix
					suffix
				}
			}
		';
	}

	/**
	 * SubmitForm mutation string.
	 *
	 * @return string
	 */
	public function submit_form_mutation() : string {
		return '
			mutation ($formId: ID!, $fieldId: Int!, $value: NameFieldInput!, $draft: Boolean) {
				submitGfForm( input: { id: $formId, saveAsDraft: $draft, fieldValues: {id: $fieldId, nameValues: $value}}) {
					errors {
						id
						message
					}
					entry {
						formFields {
							nodes {
								... on NameField {
									nameValues {
										first
										last
										middle
										prefix
										suffix
									}
								}
							}
						}
						... on GfSubmittedEntry {
							databaseId
						}
						... on GfDraftEntry {
							resumeToken
						}
					}
				}
			}
		';
	}

	/**
	 * Returns the UpdateEntry mutation string.
	 *
	 * @return string
	 */
	public function update_entry_mutation(): string {
		return '
			mutation updateGfEntry( $entryId: ID!, $fieldId: Int!, $value: NameFieldInput! ){
				updateGfEntry( input: { id: $entryId, shouldValidate: true, fieldValues: {id: $fieldId, nameValues: $value} }) {
					errors {
						id
						message
					}
					entry {
						formFields {
							nodes {
								... on NameField {
									nameValues {
										first
										last
										middle
										prefix
										suffix
									}
								}
							}
						}
					}
				}
			}
		';
	}

	/**
	 * Returns the UpdateDraftEntry mutation string.
	 *
	 * @return string
	 */
	public function update_draft_entry_mutation(): string {
		return '
			mutation updateGfDraftEntry( $resumeToken: ID!, $fieldId: Int!, $value: NameFieldInput! ){
				updateGfDraftEntry( input: {id: $resumeToken, idType: RESUME_TOKEN, shouldValidate: true, fieldValues: {id: $fieldId, nameValues: $value} }) {
					errors {
						id
						message
					}
					entry: draftEntry {
						formFields {
							nodes {
								... on NameField {
									nameValues {
										first
										last
										middle
										prefix
										suffix
									}
								}
							}
						}
					}
				}
			}
		';
	}
	/**
	 * The expected WPGraphQL field response.
	 *
	 * @param array $form the current form instance.
	 * @return array
	 */
	public function expected_field_response( array $form ) : array {
		$expected   = $this->getExpectedFormFieldValues( $form['fields'][0] );
		$expected[] = $this->expected_field_value( 'nameValues', $this->field_value );

		return [
			$this->expectedObject(
				'gfEntry',
				[
					$this->expectedObject(
						'formFields',
						[
							$this->expectedNode(
								'nodes',
								$expected,
								0
							),
						]
					),
				]
			),
		];
	}

	/**
	 * The expected WPGraphQL mutation response.
	 *
	 * @param string $mutationName .
	 * @param mixed  $value .
	 * @return array
	 */
	public function expected_mutation_response( string $mutationName, $value ) : array {
		return [
			$this->expectedObject(
				$mutationName,
				[
					$this->expectedObject(
						'entry',
						[
							$this->expectedObject(
								'formFields',
								[
									$this->expectedNode(
										'nodes',
										[
											$this->expected_field_value( 'nameValues', $value ),
										]
									),
								]
							),
						]
					),
				]
			),
		];
	}

	/**
	 * Checks if values submitted by GraphQL are the same as whats stored on the server.
	 *
	 * @param array $actual_entry .
	 * @param array $form .
	 */
	public function check_saved_values( $actual_entry, $form ): void {
		$this->assertEquals( $this->field_value['prefix'], $actual_entry[ $form['fields'][0]['inputs'][0]['id'] ], 'Submit mutation entry value 1 not equal' );
		$this->assertEquals( $this->field_value['first'], $actual_entry[ $form['fields'][0]['inputs'][1]['id'] ], 'Submit mutation entry value 2 not equal' );
		$this->assertEquals( $this->field_value['middle'], $actual_entry[ $form['fields'][0]['inputs'][2]['id'] ], 'Submit mutation entry value 3 not equal' );
		$this->assertEquals( $this->field_value['last'], $actual_entry[ $form['fields'][0]['inputs'][3]['id'] ], 'Submit mutation entry value 4 not equal' );
		$this->assertEquals( $this->field_value['suffix'], $actual_entry[ $form['fields'][0]['inputs'][4]['id'] ], 'Submit mutation entry value 5 not equal' );
	}
}
