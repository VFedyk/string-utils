<?php
/**
 * String utils
 *
 * @version 0.0.5
 * @author Volodymyr Fedyk <volodymyr.fedyk@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 */

/**
 * Constant on basic abstraction level
 */
define('INPUT_STRING', '%INPUT_STRING%');

/**
 * Constants of operation types.
 */
define('CASE_OPERATION', 1);
define('STRING_LENGTH_OPERATION', 2);
define('BASE64_OPERATION', 3);

/**
 * Constants of operation options
 */
// Case operation options
define('UPPER_CASE_OPERATION_OPTION', 1);
define('LOWER_CASE_OPERATION_OPTION', 2);
define('TITLE_CASE_OPERATION_OPTION', 3);
//Base64
define('BASE64_ENCODE_OPERATION_OPTION', 4);
define('BASE64_DECODE_OPERATION_OPTION', 5);

/**
 * Initial setting for MB library
 */
mb_internal_encoding("UTF-8");

/**
 * Mappings of operations and operation options
 */
$map = array(
	CASE_OPERATION => array(
		'label' => 'Changing case',
		'multiple' => true,
		'options' => array(
			UPPER_CASE_OPERATION_OPTION => array(
				'label' => 'Upper case',
				'handler' => array('name' => 'mb_strtoupper',),
			),

			LOWER_CASE_OPERATION_OPTION => array(
				'label' => 'Lower case',
				'handler' => array('name' => 'mb_strtolower'),
			),
			TITLE_CASE_OPERATION_OPTION => array(
				'label' => 'Title case',
				'handler' => array(
					'name' => 'mb_convert_case',
					'args' => array(INPUT_STRING, MB_CASE_TITLE),
				),
			),
		)
	),
	STRING_LENGTH_OPERATION => array(
		'label' => 'Length of input string',
		'handler' => array('name' => 'mb_strlen',),
	),
	BASE64_OPERATION => array(
		'label' => 'Base64',
		'multiple' => true,
		'options' => array(
			BASE64_ENCODE_OPERATION_OPTION => array(
				'label' => 'Encode',
				'handler' => array('name' => 'base64_encode'),
			),
			BASE64_DECODE_OPERATION_OPTION => array(
				'label' => 'Decode',
				'handler' => array('name' => 'base64_decode'),
			)
		)
	),
);

/**
 * Renders radio-buttons for operations and select boxes for operation options
 *
 * @param array $map
 * @param null $selected_operation
 * @param null $selected_operation_option
 * @param bool $as_string
 *
 * @return mixed
 */
function renderOptionBoxes($map, $selected_operation = null, $selected_operation_option = null, $as_string = true)
{
	$operation_boxes = array();
	foreach ($map as $operation_index => $operation) {
		$operation_code = "<input type='radio' name='operation' value='" . $operation_index . "'";
		if ($operation_index == $selected_operation) {
			$operation_code .= " checked='checked' ";
		}
		$operation_code .= "/>" . $operation['label'];
		if (isset($operation['multiple']) && $operation['multiple']) {
			$operation_options = array();
			foreach ($operation['options'] as $option_index => $operation_option) {
				$option_code = "<option value='" . $option_index . "'";
				if ($option_index == $selected_operation_option && $operation_index == $selected_operation) {
					$option_code .= " selected='selected' ";
				}
				$option_code .= ">" . $operation_option['label'] . "</option>";

				$operation_options[] = $option_code;
			}
			$operation_code .= ": <select name='operation_option[" . $operation_index . "]'>" . implode('', $operation_options) . "</select>";
		}
		$operation_boxes[] = "<label>" . $operation_code . "</label><br/>";
	}

	if ($as_string) {
		$result = implode('', $operation_boxes);
	} else {
		$result = $operation_boxes;
	}

	return $result;
}

/**
 * Gives control of handling input string by defined function in $map array
 *
 * @param array $map
 * @param string $input_string
 * @param int $selected_operation
 * @param null|int $selected_operation_option
 *
 * @return bool|mixed
 */
function handleOperation($map, $input_string, $selected_operation, $selected_operation_option = null)
{
	if (!array_key_exists((int) $selected_operation, $map)) {
		return false;
	}

	$mapped_operation = $map[$selected_operation];
	$is_multiple = isset($mapped_operation['multiple']) && $mapped_operation['multiple'];

	if($is_multiple && !array_key_exists($selected_operation_option, $mapped_operation['options'])) {
		$selected_operation_option = reset(array_keys($mapped_operation['options']));
	}

	if ($is_multiple) {
		$handler = $mapped_operation['options'][$selected_operation_option]['handler'];
	} else {
		$handler = $mapped_operation['handler'];
	}

	if (!isset($handler['args'])) {
		$handler['args'][] = $input_string;
	} else {
		foreach($handler['args'] as &$arg) {
			if($arg == INPUT_STRING) {
				$arg = $input_string;
			}
		}
	}

	return call_user_func_array($handler['name'], $handler['args']);
}

/**
 * Main Logic start
 */

$operation = (isset($_POST['operation'])) ? (int) $_POST['operation'] : FALSE;
$operation_option = (isset($_POST['operation_option'][$operation])) ? (int) $_POST['operation_option'][$operation] : FALSE;
$input_string = (isset($_POST['input_string'])) ? $_POST['input_string'] : FALSE;

$result = handleOperation($map, $input_string, $operation, $operation_option);
$result = ($result) ? $result : (($input_string) ? 'Specify operation, please.' : 'Specify desired string and operation on it, please.');

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>String Operations</title>
	<style type="text/css">
		body{
			font-family: sans-serif;
		}
		.wrapper{
			margin: auto;
			width: 75%;
		}

		.form-container textarea{
			height: 300px;
			width: 100%;
		}
	</style>
</head>
<body>
<div class="wrapper">
	<h1>String Utils</h1>
	<div class="result">
		<p><?= $result ?></p>
	</div>
	<div class="form-container">
		<form method="POST">
			<div>
				<label><textarea name="input_string"><?php if($input_string) echo $input_string; ?></textarea></label>
			</div>
			<div>
				<?= renderOptionBoxes($map, $operation, $operation_option); ?><br/>
			</div>
			<input type='submit'>
		</form>
	</div>
</div>
</body>
</html>