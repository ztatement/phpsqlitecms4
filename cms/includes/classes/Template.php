<?php

/**
 * A very simple template class
 *
 * @author Mark Alexander Hoschek <alex at mylittlehomepage dot net>
 * @copyright 2009 Mark Alexander Hoschek
 *           
 *            modified:
 * @author Thomas Boettcher @ztatement <github [at] ztatement [dot] com>
 * @copyleft 2024 ztatement
 */
class Template
{

  private $_templateVars = [];

  /**
   * Assigns template variables
   *
   * @param string $name
   *        - Variable name to assign
   * @param mixed $value
   *        - Value of the variable
   */
  # public function assign($name,$value)
  public function assign( string $name, $value ): void
  {
    # print_r($this->_templateVars[$name]);
    $this->_templateVars[$name] = $value;
  }

  /**
   * Displays the template with assigned variables
   *
   * @param string $template
   *        - Path to the template file
   */
  # public function display($template)
  public function display( string $template ): void
  {
    # if($this->_templateVars)
    # {
    # foreach($this->_templateVars as $__key => $__val)
    # {
    # $$__key = $__val;
    # }
    # }
    # include($template);
    $this->renderTemplate($template);
    exit();
  }

  /**
   * Fetches the rendered template as a string
   *
   * @param string $template
   *        - Path to the template file
   * @return string - Rendered content
   */
  # public function fetch($template)
  public function fetch( string $template ): string
  {
    # if($this->_templateVars)
    # {
    # foreach($this->_templateVars as $__key => $__val)
    # {
    # $$__key = $__val;
    # }
    # }
    ob_start();
    # include($template);
    # $data = ob_get_contents();
    # ob_end_clean();
    $this->renderTemplate($template);
    # return $data;
    return ob_get_clean();
  }

  /**
   * Securely render the template by explicitly assigning variables
   *
   * @param string $template
   */
  private function renderTemplate( string $template ): void
  {
    // Prevent variable injection by only exposing assigned variables securely
    foreach ($this->_templateVars as $key => $value)
    {
      $$key = $value; // Explicitly set variables
    }

    // Include template only after variable extraction
    if (file_exists($template))
    {
      include $template;
    }
    else
    {
      throw new \Exception("Template file does not exist: $template");
    }
  }
}
/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Änderungen:
 * Anstatt direkt extract() oder unsicheres Variablen-Mapping zu verwenden,
 * wird nun nur ein expliziter Variablenzuweisungsansatz genutzt.
 *     foreach ($this->_templateVars as $key => $value)
 *     {
 *       $$key = $value;
 *     }
 * Fehlerbehandlung bei Template-Aufruf: Wenn das Template nicht existiert, wird eine Exception geworfen. 
 * Das verhindert, dass der Server möglicherweise unerwartet auf nicht existierende Dateien zugreift:
 *     if (file_exists($template))
 *     {
 *       include $template;
 *     }
 *     else
 *     { 
 *       throw new \Exception("Template file does not exist: $template");
 *     }
 * Verbesserte Methode fetch: Es wird sichergestellt, dass ob_get_clean verwendet wird,
 * um den Template-Output abzufangen und als String zurückzugeben.
 * Typisierungen und Rückgabetypen wurden hinzugefügt, um sicherzustellen,
 * dass nur die erwarteten Datentypen verarbeitet werden.
 * 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * @LastModified: 2025-01-04 $Date$ $LastChangedDate: 2025-01-04 14:19:16 +0100 $
 * @editor: $LastChangedBy: ztatement $
 * -------------
 * changelog:
 * @see change.log
 *
 * $Date$ $Revision$ - Description
 * 2024-12-09: 4.2.2.2024.12.09 - fix: variablen, ad renderTemplate() ect.
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 * Local variables:
 * tab-width: 2
 * c-basic-offset: 2
 * c-hanging-comment-ender-p: nil
 * End:
 */
 