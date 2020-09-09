<?php
/**
 * Core functions loaded here
 *
 * @package blockshop
 */

?>
<?php

require_once get_template_directory() . '/core/functions/function-globals.php';

require_once get_template_directory() . '/core/customizer/class/class-fonts.php';
require_once get_template_directory() . '/core/functions/function-setup.php';
require_once get_template_directory() . '/core/customizer/backend.php';
require_once get_template_directory() . '/core/customizer/frontend.php';
require_once get_template_directory() . '/core/customizer/class-blockshop-opt.php';
require_once get_template_directory() . '/core/customizer/styles.php';

require_once get_template_directory() . '/core/functions/function-body-classes.php';
require_once get_template_directory() . '/core/functions/function-template.php';

require_once get_template_directory() . '/core/functions/wc/actions.php';
require_once get_template_directory() . '/core/functions/wc/filters.php';

require_once get_template_directory() . '/core/metaboxes/page.php';
require_once get_template_directory() . '/core/metaboxes/post.php';

require_once get_template_directory() . '/core/hooks/hooks-blog.php';

require_once get_template_directory() . '/core/tgm/class-tgm-plugin-activation.php';
require_once get_template_directory() . '/core/tgm/plugins.php';

require_once get_template_directory() . '/core/demo/class-blockshop-ocdi-setup.php';
