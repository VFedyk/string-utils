<?php
/**
 * String utils
 *
 * @version 0.0.1
 * @author Volodymyr Fedyk <volodymyr.fedyk@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 */

$result = FALSE;

/**
 * Constants of operation types.
 */

define('CASE_OPERATION', 1);

/**
 * Constants of operation options
 */

// Case operations
define('UPPERCASE_OPERATION_OPTION', 1);
define('LOWERCASE_OPERATION_OPTION', 2);
define('TITLECASE_OPERATION_OPTION', 3);

/**
 * Mappings of operations and operation options
 */
$map = array(
	CASE_OPERATION => array(
		'label' => 'Changing case',
		'multiple' => true,
		'options' => array(
			UPPERCASE_OPERATION_OPTION => 'Upper case',
			LOWERCASE_OPERATION_OPTION => 'Lower case',
			TITLECASE_OPERATION_OPTION => 'Title case',
		)
	),
);

/**
 * Renders radiobuttons for operations and select boxes for operation options
 *
 * @param array $map
 * @param null $operation
 * @param null $operation_option
 * @param bool $out
 *
 * @return void|array
 */
function renderOptionBoxes($map, $selected_operation = null, $selected_operation_option = null, $out = true)
{
	$operation_boxes = array();
	foreach ($map as $operation_index => $operation) {
		$operation_code = "<input type='radio' name='operation' value='" . $operation_index . "'";
		if ($operation_index == $selected_operation) {
			$operation_code .= " checked='checked' ";
		}
		$operation_code .= "/>" . $operation['label'] . ": ";
		if (isset($operation['multiple']) && $operation['multiple']) {
			$operation_options = array();
			foreach ($operation['options'] as $option_index => $operation_option) {
				$option_code = "<option value='" . $option_index . "'";
				if ($option_index == $selected_operation_option && $operation_index == $selected_operation) {
					$option_code .= " selected='selected' ";
				}
				$option_code .= ">" . $operation_option . "</option>";

				$operation_options[] = $option_code;
			}
			$operation_code .= "<select name='operation_option'>" . implode('', $operation_options) . "</select>";
		}
		$operation_boxes[] = "<label>" . $operation_code . "</label>";
	}

	if ($out) {
		echo implode('', $operation_boxes);
	} else {
		return $operation_boxes;
	}

}

/**
 * Main Logic start
 */

/**
 * @todo Refactor this code
 */
$operation = (isset($_POST['operation'])) ? $_POST['operation'] : FALSE;
$operation_option = (isset($_POST['operation_option'])) ? $_POST['operation_option'] : FALSE;
$input_string = (isset($_POST['input_string'])) ? $_POST['input_string'] : FALSE;

if ($operation && $input_string) {
	if ($operation == CASE_OPERATION) {
		if ($operation_option == UPPERCASE_OPERATION_OPTION) {
			$result = mb_strtoupper($input_string);
		}

		if ($operation_option == LOWERCASE_OPERATION_OPTION) {
			$result = mb_strtolower($input_string);
		}

		if ($operation_option == TITLECASE_OPERATION_OPTION) {
			$result = mb_convert_case($input_string, MB_CASE_TITLE);
		}
	}
}

$result = ($result) ? $result : (($input_string) ? 'Specify operation, please.' : 'Specify desired string and operation on it, please.');

?>
<!DOCTYPE html>
<html>
<head>
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
				<textarea name="input_string"><?php if($input_string) echo $input_string; ?></textarea>
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