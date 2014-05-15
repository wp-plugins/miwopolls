<?php
/**
 * @package        MiwoPolls
 * @copyright    2009-2014 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('_MEXEC') or die;

// Load the tooltip behavior.
MHtml::_('behavior.tooltip');
MHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Miwi.submitbutton = function (task) {
            if (document.formvalidator.isValid(document.id('component-form'))) {
                Miwi.submitform(task, document.getElementById('component-form'));
            }
        }
    });
</script>

<form action="<?php echo MRoute::getActiveUrl(); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
    <div class="tab-content">
        <?php echo MHtml::_('tabs.start', 'miwopolls', array('useCookie'=>1)); ?>

        <?php $fieldSets = $this->form->getFieldsets(); ?>
        <?php foreach ($fieldSets as $name => $fieldSet) { ?>
            <?php
            if ($fieldSet->name == 'permissions') {
                continue;
            }
            ?>
            <!-- Details -->
            <?php echo MHtml::_('tabs.panel', MText::_($fieldSet->label), 'details'); ?>
            <?php foreach ($this->form->getFieldset($name) as $field) { ?>
                <div class="control-group">
                    <?php if (!$field->hidden) { ?>
                        <div class="control-label">
                            <?php echo $field->label; ?>
                        </div>
                    <?php } ?>
                    <div class="controls">
                        <?php echo $field->input; ?>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>

        <?php echo MHtml::_('tabs.end'); ?>
    </div>
    <div>
        <input type="hidden" name="task" value=""/>
        <?php echo MHtml::_('form.token'); ?>
    </div>
</form>
