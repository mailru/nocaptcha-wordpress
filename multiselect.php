<?php
function nocaptcha_multiselect( $args ) {
	$args = wp_parse_args( $args, array(
		'name'            => '',
        'values'          => array(),
        'selected'        => array(),
        'size'            => 5,
        'height'          => '100px',
        'label_selected'  => 'Selected',
        'label_available' => 'Available',
	) );

    $id = (string) rand();
    $available = array();
    foreach ( $args['values'] as $val => $text ) {
        if ( ! in_array( $val, $args['selected'] ) ) {
            $available[] = $val;
        }
    }
    $real_value = join( ',', $args['selected'] );

    ?>
    <table class="nocaptcha-multiselect-table">
        <tr class="nocaptcha-multiselect-header">
            <td><?php echo $args['label_selected']; ?>:</td>
            <td></td>
            <td><?php echo $args['label_available']; ?>:</td>
        </tr>
        <tr>
            <td>
                <select class="nocaptcha-multiselect" id="nocaptcha-multiselect-<?=$id?>-selected" size="<?php echo $args['size']; ?>" style="height: <?php echo $args['height'];?>">
                    <?php foreach ( $args['selected'] as $val ): ?>
                        <option value="<?php echo $val; ?>"><?php echo $args['values'][$val]; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td style="nocaptcha-multiselect-actions">
                <a href="javascript:void(0)" class="nocaptcha-multiselect-arrow" onclick="nocaptchaMultiselectRemoveOption('<?php echo $id; ?>')"><span class="dashicons dashicons-arrow-right-alt"></span></a><br>
                <a href="javascript:void(0)" class="nocaptcha-multiselect-arrow" onclick="nocaptchaMultiselectAddOption('<?php echo $id; ?>')"><span class="dashicons dashicons-arrow-left-alt"></span></a>
            </td>
            <td>
                <select class="nocaptcha-multiselect" id="nocaptcha-multiselect-<?=$id?>-available" size="<?php echo $args['size']; ?>" style="height: <?php echo $args['height'];?>">
                    <?php foreach ( $available as $val ): ?>
                        <option value="<?php echo $val; ?>"><?php echo $args['values'][$val]; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <input type="hidden" name="<?php echo $args['name']; ?>" id="<?php echo $id; ?>" value="<?php echo $real_value; ?>">
    <?php
}

function nocaptcha_multiselect_add_style_and_script() {
    ?>
    <style>
        .nocaptcha-wrap .nocaptcha-multiselect {
            height: auto;
            min-width: 150px;
        }
        .nocaptcha-wrap .nocaptcha-multiselect-table td:first-child {
            padding-left: 0;
        }
        .nocaptcha-wrap .nocaptcha-multiselect-actions {
            vertical-align: middle;
        }
        .nocaptcha-wrap .nocaptcha-multiselect-header td {
            padding-top: 0;
            padding-bottom: 0;
        }
        .nocaptcha-wrap .nocaptcha-multiselect-arrow {
            text-decoration: none;
            outline: none;
        }
    </style>
    <script type="text/javascript">
    function nocaptchaMultiselectMoveOption(fromSelect, toSelect) {
        option = document.createElement('option');
        option.value = fromSelect.value;
        option.innerHTML = fromSelect.options[fromSelect.selectedIndex].innerHTML;
        toSelect.appendChild(option);
        fromSelect.remove(fromSelect.selectedIndex);
    }
    function nocaptchaMultiselectAddOption(id) {
        var from = document.getElementById('nocaptcha-multiselect-' + id + '-available');
        var to = document.getElementById('nocaptcha-multiselect-' + id + '-selected');
        nocaptchaMultiselectMoveOption(from, to);
        nocaptchaMultiselectUpdateField(id);
    }
    function nocaptchaMultiselectRemoveOption(id) {
        var from = document.getElementById('nocaptcha-multiselect-' + id + '-selected');
        var to = document.getElementById('nocaptcha-multiselect-' + id + '-available');
        nocaptchaMultiselectMoveOption(from, to);
        nocaptchaMultiselectUpdateField(id);
    }
    function nocaptchaMultiselectUpdateField(id) {
        var select = document.getElementById('nocaptcha-multiselect-' + id + '-selected');
        var field = document.getElementById(id);
        var values = [];
        for (i = 0; i < select.options.length; i++) {
            values.push(select.options[i].value);
        }
        field.value = values.join();
    }
    </script>
    <?php
}

add_action( 'admin_head', 'nocaptcha_multiselect_add_style_and_script' );
?>
