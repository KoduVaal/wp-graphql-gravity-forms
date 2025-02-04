<?php
/**
 * Test PostTagsField.
 *
 * @package Tests\WPGraphQL\GF
 */

use Tests\WPGraphQL\GF\TestCase\FormFieldTestCase;
use Tests\WPGraphQL\GF\TestCase\FormFieldTestCaseInterface;
/**
 * Class -PostTagsCheckboxFieldTest
 */
class PostTagsCheckboxFieldTest extends FormFieldTestCase implements FormFieldTestCaseInterface {
	public int $tag_id_1;
	public int $tag_id_2;
	public int $tag_id_3;

	public function setUp(): void {
		// Before...
		$this->tag_id_1 = self::factory()->tag->create();
		$this->tag_id_2 = self::factory()->tag->create();
		$this->tag_id_3 = self::factory()->tag->create();
		parent::setUp();

		$this->clearSchema();
	}

	public function tearDown() : void {
		wp_delete_term( $this->tag_id_1, 'post_tag' );
		wp_delete_term( $this->tag_id_2, 'post_tag' );
		wp_delete_term( $this->tag_id_3, 'post_tag' );
		parent::tearDown();
	}

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
		return $this->tester->getPropertyHelper( 'PostTagsField' );
	}

	/**
	 * Generates the form fields from factory. Must be wrapped in an array.
	 */
	public function generate_fields() : array {
		return [
			$this->factory->field->create(
				array_merge(
					$this->property_helper->values,
					[ 'inputType' => 'checkbox' ],
					[
						'choices' => [
							[
								'text'       => self::factory()->tag->get_object_by_id( $this->tag_id_1 )->name,
								'value'      => (string) self::factory()->tag->get_object_by_id( $this->tag_id_1 )->term_id,
								'isSelected' => false,
							],
							[
								'text'       => self::factory()->tag->get_object_by_id( $this->tag_id_2 )->name,
								'value'      => (string) self::factory()->tag->get_object_by_id( $this->tag_id_2 )->term_id,
								'isSelected' => false,
							],
							[
								'text'       => self::factory()->tag->get_object_by_id( $this->tag_id_3 )->name,
								'value'      => (string) self::factory()->tag->get_object_by_id( $this->tag_id_3 )->term_id,
								'isSelected' => false,
							],
						],
					],
					[
						'inputs' => [
							[
								'label' => self::factory()->tag->get_object_by_id( $this->tag_id_1 )->name,
								'name'  => null,
								'id'    => '1.1',
							],
							[
								'label' => self::factory()->tag->get_object_by_id( $this->tag_id_2 )->name,
								'name'  => null,
								'id'    => '1.2',
							],
							[
								'label' => self::factory()->tag->get_object_by_id( $this->tag_id_3 )->name,
								'name'  => null,
								'id'    => '1.3',
							],
						],
					],
				)
			),
		];
	}

	/**
	 * The value as expected in GraphQL.
	 */
	public function field_value() {
		return [
			[
				'inputId' => (float) $this->fields[0]['inputs'][0]['id'],
				'text'    => $this->fields[0]['choices'][0]['text'],
				'value'   => $this->fields[0]['choices'][0]['value'],
			],
			[
				'inputId' => (float) $this->fields[0]['inputs'][1]['id'],
				'text'    => $this->fields[0]['choices'][1]['text'],
				'value'   => (string) null,
			],
			[
				'inputId' => (float) $this->fields[0]['inputs'][2]['id'],
				'text'    => $this->fields[0]['choices'][2]['text'],
				'value'   => $this->fields[0]['choices'][2]['value'],
			],
		];
	}

	/**
	 * The graphql field value input.
	 */
	public function field_value_input() {
		$field_value = $this->field_value();
		return [
			[
				'inputId' => $field_value[0]['inputId'],
				'value'   => (string) $this->fields[0]['choices'][0]['value'],
			],
			[
				'inputId' => $field_value[1]['inputId'],
				'value'   => $field_value[1]['value'],
			],
			[
				'inputId' => $field_value[2]['inputId'],
				'value'   => (string) $this->fields[0]['choices'][2]['value'],
			],
		];
	}
	/**
	 * The graphql field value input.
	 */
	public function updated_field_value_input() {
		$field_value = $this->updated_field_value();
		return [
			[
				'inputId' => $field_value[0]['inputId'],
				'value'   => $field_value[0]['value'],
			],
			[
				'inputId' => $field_value[1]['inputId'],
				'value'   => $field_value[1]['value'],
			],
			[
				'inputId' => $field_value[2]['inputId'],
				'value'   => $field_value[2]['value'],
			],
		];
	}



	/**
	 * The value as expected in GraphQL when updating from field_value().
	 */
	public function updated_field_value() {
		return [
			[
				'inputId' => (float) $this->fields[0]['inputs'][0]['id'],
				'value'   => null,
				'text'    => $this->fields[0]['choices'][0]['text'],
			],
			[
				'inputId' => (float) $this->fields[0]['inputs'][1]['id'],
				'text'    => $this->fields[0]['choices'][1]['text'],
				'value'   => $this->fields[0]['choices'][1]['value'],
			],
			[
				'inputId' => (float) $this->fields[0]['inputs'][2]['id'],
				'text'    => $this->fields[0]['choices'][2]['text'],
				'value'   => $this->fields[0]['choices'][2]['value'],
			],
		];
	}

	/**
	 * The value as expected by Gravity Forms.
	 */
	public function value() {
		return [
			(string) $this->field_value[0]['inputId'] => $this->field_value[0]['value'],
			(string) $this->field_value[2]['inputId'] => $this->field_value[2]['value'],
		];
	}


	/**
	 * The GraphQL query string.
	 *
	 * @return string
	 */
	public function field_query():string {
		return '... on PostTagsField {
				adminLabel
				canPrepopulate
				conditionalLogic {
					actionType
					logicType
					rules{
						fieldId
						operator
						value
					}
				}
				cssClass
				defaultValue
				description
				descriptionPlacement
				errorMessage
				inputName
				isRequired
				label
				labelPlacement
				pageNumber
				personalData {
					isIdentificationField
					shouldErase
					shouldExport
				}
				placeholder
				size
				... on PostTagsCheckboxField {
					checkboxValues {
						inputId
						text
						value
					}
					choices {
						isSelected
						text
						value
					}
					hasChoiceValue
					hasSelectAll
					inputs {
						id
						label
						name
					}
				}
			}
		';
	}

	/**
	 * SubmitForm mutation string.
	 */
	public function submit_form_mutation(): string {
		return '
			mutation ($formId: ID!, $fieldId: Int!, $value: [CheckboxFieldInput]!, $draft: Boolean) {
				submitGfForm( input: { id: $formId, saveAsDraft: $draft, fieldValues: {id: $fieldId, checkboxValues: $value}}) {
					errors {
						id
						message
					}
					entry {
						formFields {
							nodes {
								... on PostTagsCheckboxField {
									checkboxValues {
										inputId
										text
										value
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
	 */
	public function update_entry_mutation(): string {
		return '
			mutation updateGfEntry( $entryId: ID!, $fieldId: Int!, $value: [CheckboxFieldInput]! ){
				updateGfEntry( input: { id: $entryId, shouldValidate: true, fieldValues: {id: $fieldId, checkboxValues: $value} }) {
					errors {
						id
						message
					}
					entry {
						formFields {
							nodes {
								... on PostTagsCheckboxField {
									checkboxValues {
										inputId
										text
										value
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
	 */
	public function update_draft_entry_mutation(): string {
		return '
			mutation updateGfDraftEntry( $resumeToken: ID!, $fieldId: Int!, $value: [CheckboxFieldInput]! ){
				updateGfDraftEntry( input: {id: $resumeToken, idType: RESUME_TOKEN, shouldValidate: true, fieldValues: {id: $fieldId, checkboxValues: $value} }) {
					errors {
						id
						message
					}
					entry: draftEntry {
						formFields {
							nodes {
								... on PostTagsCheckboxField {
									checkboxValues {
										inputId
										text
										value
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
	 */
	public function expected_field_response( array $form ): array {
		$expected   = $this->getExpectedFormFieldValues( $form['fields'][0] );
		$expected[] = $this->expected_field_value( 'checkboxValues', $this->field_value );

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
	public function expected_mutation_response( string $mutationName, $value ):array {
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
											$this->expected_field_value( 'checkboxValues', $value ),
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
		$this->assertEquals( $this->field_value[0]['value'], $actual_entry[ $form['fields'][0]['inputs'][0]['id'] ] );
		$this->assertEquals( $this->field_value[1]['value'], $actual_entry[ $form['fields'][0]['inputs'][1]['id'] ] );
		$this->assertEquals( $this->field_value[2]['value'], $actual_entry[ $form['fields'][0]['inputs'][2]['id'] ] );
	}
}
