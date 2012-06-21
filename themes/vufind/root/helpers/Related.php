<?php
/**
 * Related records view helper
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */

/**
 * Related records view helper
 *
 * @category VuFind2
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */
class VuFind_Theme_Root_Helper_Related extends Zend_View_Helper_Abstract
{
    /**
     * Render the output of a related records module.
     *
     * @param VF_Related_Interface $related The related records object to render
     *
     * @return string
     */
    public function related($related)
    {
        // Set up the rendering context:
        $oldContext = $this->view->context($this->view)->apply(
            array('related' => $related)
        );

        // Get the current related item module's class name, then start a loop
        // in case we need to use a parent class' name to find the appropriate
        // template.
        $className = get_class($related);
        while (true) {
            // Guess the template name for the current class:
            $classParts = explode('_', $className);
            $template = 'Related/' . array_pop($classParts) . '.phtml';
            try {
                // Try to render the template....
                $html = $this->view->render($template);
                $this->view->context($this->view)->restore($oldContext);
                return $html;
            } catch (Zend_View_Exception $e) {
                // If the template doesn't exist, let's see if we can inherit a
                // template from a parent class:
                $className = get_parent_class($className);
                if (empty($className)) {
                    // No more parent classes left to try?  Throw an exception!
                    throw new Zend_View_Exception(
                        'Cannot find template for related items class: ' .
                        get_class($related)
                    );
                }
            }
        }
    }
}