<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Core;

/**
 * Form class tests
 *
 * @group Core
 * @group Form
 */
class Test_Form extends TestCase
{
	private static $config_security;

	public static function setUpBeforeClass()
	{
		Config::load('security');
		static::$config_security = Config::get('security');
	}

	public static function tearDownAfterClass()
	{
		Config::set('security', static::$config_security);
	}

	protected function setUp()
	{
		Config::load('form');
		Config::set('form', array(
			'prep_value'            => true,
			'auto_id'               => true,
			'auto_id_prefix'        => 'form_',
			'form_method'           => 'post',
			'form_template'         => "\n\t\t{open}\n\t\t<table>\n{fields}\n\t\t</table>\n\t\t{close}\n",
			'fieldset_template'     => "\n\t\t<tr><td colspan=\"2\">{open}<table>\n{fields}</table></td></tr>\n\t\t{close}\n",
			'field_template'        => "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg}</td>\n\t\t</tr>\n",
			'multi_field_template'  => "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n",
			'error_template'        => '<span>{error_msg}</span>',
			'required_mark'         => '*',
			'inline_errors'         => false,
			'error_class'           => 'validation_error',
		));

	}

	/**
	* Tests Form::input()
	*
	* test for data prepping
	*
	* @test
	*/
	public function test_input_prep()
	{
		$output = Form::input('name', '"H&M"');
		$expected = '<input name="name" value="&quot;H&amp;M&quot;" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', '');
		$expected = '<input name="name" value="" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', "0");
		$expected = '<input name="name" value="0" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', 0);
		$expected = '<input name="name" value="0" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', 1);
		$expected = '<input name="name" value="1" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', 10);
		$expected = '<input name="name" value="10" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', true);
		$expected = '<input name="name" value="1" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', false);
		$expected = '<input name="name" value="" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', null);
		$expected = '<input name="name" value="" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::input()
	*
	* test for dont_prep
	*
	* @test
	*/
	public function test_input_dont_prep()
	{
		$output = Form::input('name', '&quot;&#39;H&amp;M&#39;&quot;', array('dont_prep' => true));
		$expected = '<input name="name" value="&quot;&#39;H&amp;M&#39;&quot;" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', '', array('dont_prep' => true));
		$expected = '<input name="name" value="" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', "0", array('dont_prep' => true));
		$expected = '<input name="name" value="0" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', 0, array('dont_prep' => true));
		$expected = '<input name="name" value="0" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', 1, array('dont_prep' => true));
		$expected = '<input name="name" value="1" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', 10, array('dont_prep' => true));
		$expected = '<input name="name" value="10" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', true, array('dont_prep' => true));
		$expected = '<input name="name" value="1" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', false, array('dont_prep' => true));
		$expected = '<input name="name" value="" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);

		$output = Form::input('name', null, array('dont_prep' => true));
		$expected = '<input name="name" value="" type="text" id="form_name" />';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::textarea()
	*
	* test for data prepping
	*
	* @test
	*/
	public function test_textarea_prep()
	{
		$output = Form::textarea('name', '"H&M"');
		$expected = '<textarea name="name" id="form_name">&quot;H&amp;M&quot;</textarea>';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::textarea()
	*
	* test for dont_prep
	*
	* @test
	*/
	public function test_textarea_dont_prep()
	{
		$output = Form::textarea('name', '&quot;&#39;H&amp;M&#39;&quot;', array('dont_prep' => true));
		$expected = '<textarea name="name" id="form_name">&quot;&#39;H&amp;M&#39;&quot;</textarea>';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::select()
	*
	* test for data prepping
	*
	* @test
	*/
	public function test_select_prep()
	{
		$output = Form::select('fieldname', null,
			array(
						'key_H&M' => 'val_H&M',
						'key_""' => 'val_""',
			)
		);
		$expected = '<select name="fieldname" id="form_fieldname">'.PHP_EOL
					.'	<option value="key_H&amp;M">val_H&amp;M</option>'.PHP_EOL
					.'	<option value="key_&quot;&quot;">val_&quot;&quot;</option>'.PHP_EOL
					.'</select>';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::prep_value()
	*
	* @test
	*/
	public function test_prep_value()
	{
		$output = Form::prep_value('<"H&M">');
		$expected = '&lt;&quot;H&amp;M&quot;&gt;';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::select()
	*
	* test for dont_prep
	*
	* @test
	*/
	public function test_select_dont_prep()
	{
		$output = Form::select('fieldname', null,
			array(
						'key_H&amp;M' => 'val_H&amp;M',
						'key_&quot;&#39;&quot;' => 'val_&quot;&#39;&quot;',
			),
			array(
						'dont_prep' => true,
			)
		);
		$expected = '<select name="fieldname" id="form_fieldname">'.PHP_EOL
					.'	<option value="key_H&amp;M">val_H&amp;M</option>'.PHP_EOL
					.'	<option value="key_&quot;&#39;&quot;">val_&quot;&#39;&quot;</option>'.PHP_EOL
					.'</select>';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::prep_value()
	*
	* test of invalid string
	*
	* @test
	*/
	public function test_prep_value_invalid_utf8()
	{
		// 6 byte UTF-8 string, which is invalid now
		$utf8_string = "\xFC\x84\x80\x80\x80\x80";
		$output = Form::prep_value($utf8_string);
		$expected = '';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::label()
	*
	* @test
	*/
	public function test_label_auto_id_true()
	{
		$config = \Config::get('form.auto_id');
		\Config::set('form.auto_id', true);
		
		$form = \Form::forge(__METHOD__);
		
		$label = 'label';
		$id = 'id';
		$output = $form->label($label, $id);
		$expected = '<label for="form_id">label</label>';
		$this->assertEquals($expected, $output);
		
		\Config::set('form.auto_id', $config);
	}

	/**
	* Tests Form::label()
	*
	* @test
	*/
	public function test_label_auto_id_false()
	{
		$config = \Config::get('form.auto_id');
		\Config::set('form.auto_id', false);
		
		$form = \Form::forge(__METHOD__);
		
		$label = 'label';
		$id = 'id';
		$output = $form->label($label, $id);
		$expected = '<label for="id">label</label>';
		$this->assertEquals($expected, $output);
		
		\Config::set('form.auto_id', $config);
	}

	/**
	* Tests Form::open()
	*
	* @test
	*/
	public function test_open()
	{
		$form = \Form::forge(__METHOD__);
		
		$output = $form->open('uri/to/form');
		$expected = '<form action="uri/to/form" accept-charset="utf-8" method="post">';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::open()
	*
	* @test
	*/
	public function test_open_auto_csrf_token()
	{
		\Config::set('security.csrf_auto_token', true);

		$form = \Form::forge(__METHOD__);
		
		$output = $form->open('uri/to/form');
		$expected = '<form action="uri/to/form" accept-charset="utf-8" method="post">'.PHP_EOL.'<input name="fuel_csrf_token" value="%s" type="hidden" id="form_fuel_csrf_token" />';
		$this->assertStringMatchesFormat($expected, $output);
	}

	/**
	* Tests Form::open()
	*
	* @test
	*/
	public function test_open_static()
	{
		$output = Form::open('uri/to/form');
		$expected = '<form action="uri/to/form" accept-charset="utf-8" method="post">';
		$this->assertEquals($expected, $output);
	}

	/**
	* Tests Form::open()
	*
	* @test
	*/
	public function test_open_auto_csrf_token_static()
	{
		\Config::set('security.csrf_auto_token', true);

		$output = Form::open('uri/to/form');
		$expected = '<form action="uri/to/form" accept-charset="utf-8" method="post">'.PHP_EOL.'<input name="fuel_csrf_token" value="%s" type="hidden" id="form_fuel_csrf_token" />';
		$this->assertStringMatchesFormat($expected, $output);
	}
}
