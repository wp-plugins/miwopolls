<?php
/**
 * @version		1.0.0
 * @package		MiwoPolls
 * @subpackage	MiwoPolls
 * @copyright	2009-2011 Miwisoft LLC, www.miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 
 *
 * Based on Apoll Component
 * @copyright (C) 2009 - 2011 Hristo Genev All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.afactory.org
 */

defined('MIWI') or die('Restricted access');

?>

<form>
    <table align="center" width="90%" cellspacing="2" cellpadding="2" border="0" >
        <tr>
            <td class="moduleheading" colspan="2"><?php echo $this->row->title; ?></td>
        </tr>
        <?php foreach ($this->options as $option) {
        if ($option->text != "") {?>
            <tr>
                <td valign="top" height="30">
                    <input type="radio" name="poll" value="<?php echo $option->text; ?>">
                </td>
                <td class="poll" width="100%" valign="top">
                    <?php echo $option->text; ?>
                </td>
            </tr>
            <?php
        }
    } ?>
        <tr>
            <td valign="middle" height="50" colspan="2" align="center">
                <input type="button" name="submit" value="<?php echo MText::_( 'COM_MIWOPOLLS_VOTE' ); ?>">&nbsp;&nbsp;
                <input type="button" name="result" value="<?php echo MText::_( 'COM_MIWOPOLLS_RESULTS' ); ?>">
            </td>
        </tr>
    </table>
</form>