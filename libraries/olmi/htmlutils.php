<?php // $Id: htmlutils.inc.php,v 1.1 2010/11/11 09:51:41 nastya Exp $

class HtmlUtils {

  /**
   * Returns JavaScript to set focus on the field of the form
   * @param string $formName
   * @param string $fieldName
   * @return string
   */
  function setFocus($formName, $fieldName) {
    $result = '';
    $result .= "<script>\n";
    $result .= "<!--\n";
    $result .= "document.".$formName.".".$fieldName.".focus();\n";
    $result .= "-->\n";
    $result .= "</script>\n";
    return $result;
  }

  /**
   * Returns INPUT tag with type=hidden
   * @param mixed $name
   * @param string $value
   * @return string
   */
  function hidden($name, $value) {
    return "<input type=\"hidden\" name=\"".$name."\" value=\"".htmlspecialchars($value)."\">";
  }

  /**
   * Returns string of INPUT tags with type=hidden
   * @param array $list
   * @return string
   */
  function hiddens($list) {
    if (is_array($list)) {
      $result = '';
      foreach ($list as $key => $value) {
        $result .= HtmlUtils::hidden($key, $value);
      }
      return $result;
    }
    else {
      return NULL;
    }
  }

  /**
   * Returns INPUT tags with type=submit
   * @param string $name
   * @param string $value
   * @param mixed $attributes
   * @return string
   */
  function submit($name = NULL, $value = NULL, $attributes = NULL) {
    $result = '<input type="submit"';
    if ($name) {
      $result .= ' name="'.$name.'"';
    }
    if ($value) {
      $result .= ' value="'.$value.'"';
    }
    if ($attributes) {
      $result .= HtmlUtils::attributes($attributes);
    }
    $result .= '>';
    return $result;
  }

  /**
   * Returns INPUT tags with type=reset
   * @param string $value
   * @param mixed $attributes
   * @return string
   */
  function reset($value = NULL, $attributes = NULL) {
    $result = '<input type="reset"';
    if ($value) {
      $result .= ' value="'.$value.'"';
    }
    if ($attributes) {
      $result .= HtmlUtils::attributes($attributes);
    }
    $result .= '>';
    return $result;
  }

  /**
   * Returns INPUT tag with type=submit and predefined name=CANCEL_BUTTON_NAME.
   * @param string $value
   * @param mixed $attributes
   * @return string
   */
  function cancel($value, $attributes = NULL) {
    return HtmlUtils::submit(CANCEL_BUTTON_NAME, $value, $attributes);
  }

  /**
   * Returns opening FORM tag.
   * @param string $action
   * @param string $method
   * @param string $name
   * @param bool $multipartType
   * @param array $attributes
   * @return string
   */
  function form($action, $method, $name = NULL, $multipartType = FALSE, $attributes = NULL) {
    $result = '<form action="'.$action.'"';
    if ($method) {
      $result .= ' method="'.$method.'"';
    }
    if ($name) {
      $result .= ' name="'.$name.'"';
    }
    if ($multipartType) {
      $result .= ' enctype="multipart/form-data"';
    }
    if ($attributes) {
      if (is_array($attributes)) {
        $result .= HtmlUtils::attributes($attributes);
      }
      else {
        //compatibility...
        $result .= ' target="'.$attributes.'"';
      }
    }
    $result .= '>';
    return $result;
  }

  /**
   * Returns string of html attributes.
   * @param mixed $attributes
   * @return string
   */
  function attributes($attributes) {
    if (is_array($attributes)) {
      $result = '';
      foreach ($attributes as $key => $value) {
        $result .= ' '.$key.'="'.htmlspecialchars($value).'"';
      }
      return $result;
    }
    else {
      if ($attributes) {
        return ' '.((string) $attributes);
      }
      else {
        return NULL;
      }
    }
  }

  /**
   * Returns A tag with content.
   * @param string $href
   * @param string $text
   * @param mixed $attributes
   * @return string
   */
  function link($href, $text, $attributes = NULL) {
    $result = "";
    $result = '<a href="';
    if (is_object($href)) {
      $result .= htmlspecialchars($href->toString());
    }
    else {
      $result .= htmlspecialchars($href);
    }
    $result .= '"';
    if ($attributes) {
      $result .= HtmlUtils::attributes($attributes);
    }
    $result .= '>';
    $result .= $text;
    $result .= '</a>';
    return $result;
  }

  /**
   * Returns opening HTML and HEAD tags.
   * @return string
   */
  function begin() {
    return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n
            <html>\n
            <head>\n";
  }

  /**
   * Returns TITLE tag.
   * @param string $text
   * @return string
   */
  function title($text) {
    return "<title>".$text."</title>\n";
  }

  /**
   * Returns tag to include css-file.
   * @param string $cssfile
   * @return string
   */
  function css($cssfile) {
    return "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$cssfile."\">\n";
  }

  /**
   * @param mixed $attributes
   * @return string
   */
  function body($attributes = NULL) {
    return "</head>\n<body".HtmlUtils::attributes($attributes).">\n";
  }

  /**
   * Returns closing BODY and HTML tags.
   * @return string
   */
  function end() {
    return "</body>\n</html>\n";
  }

  /**
   * Returns <script src=jsfile>
   * @param string $jsfile
   * @return string
   */
  function jscript($jsfile) {
    if ($jsfile) return "<script language=\"JavaScript\" type=\"text/javascript\" src=\"".$jsfile."\"></script>\n";
  }

  /**
   * Returns select box with options
   * @param string $name
   * @param array $values
   * @param string $selected
   * @return string
   */
  function select($name, $values, $selected = NULL) {
    $result = "<select name=\"".$name."\">\n";
    if (is_array($values)) {
      foreach ($values as $key => $value) {
        $result .= "<option value=\"".$key."\"";
        if ($key == $selected) $result .= " selected"; //=\"1\" не XHTML, пока.
        $result .= ">".$value;
        $result .= "</option>\n";
      }
    }
    $result .= "</select>";
    return $result;
  }

  /**
   * @param array $args
   * @return string
   */
  function style($args) {
    $result = "<style>\n";
    foreach ($args as $key => $value) {
      $result .= $key." {\n";
      foreach ($value as $styleName => $styleValue) {
        $result .= "  ".$styleName.": ".$styleValue.";\n";
      }
      $result .= "}\n";
    }
    $result .= "</style>\n";
    return $result;
  }

  /**
   *
   * @param array $styles
   * @return string
   */
  function inlineStyles($styles) {
    $result = "";
    foreach ($styles as $styleName => $styleValue) {
      $result .= $styleName.":".$styleValue.";";
    }
    return $result;
  }

  function colgroup($columns) {
    if (is_array($columns)) {
      $result = '<colgroup>';
      foreach ($columns as $column) {
        $result .= '<col width="'.$column.'%">';
      }
      $result .= '</colgroup>';
      return $result;
    }
    else {
      return $columns;
    }
  }
}

?>
