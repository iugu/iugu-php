<?php

//o-------------------------------o
//    Libraries
//o-------------------------------o
require( realpath(dirname(__FILE__) . "/../") . "/lib/Iugu.php" );

//o-------------------------------o
//    Configure time as GMT
//o-------------------------------o
date_default_timezone_set("GMT");

//Shell/Commands.php
class PHP_Shell_Commands {
  /*
  * instance of the current class
  *
  * @var PHP_Shell_Commands
  */
  static protected $instance;

  /**
  * registered commands 
  *
  * array('quit' => ... )
  *
  * @var array
  * @see registerCommand
  */
  protected $commands = array();

  /**
  * register your own command for the shell
  *
  * @param string $regex a regex to match against the input line
  * @param string $obj a Object
  * @param string $method a method in the object to call of the regex matches
  * @param string $cmd the command string for the help
  * @param string $help the full help description for this command
  */
  public function registerCommand($regex, $obj, $method, $cmd, $help) {
    $this->commands[] = array(
      'regex' => $regex,
      'obj' => $obj,
      'method' => $method,
      'command' => $cmd,
      'description' => $help
    );
  }

  /**
  * return a copy of the commands array
  *
  * @return all commands
  */
  public function getCommands() {
    return $this->commands;
  }

  static function getInstance() {
    if (is_null(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class();
    }
    return self::$instance;
  }
}

//Shell/Extensions.php

interface PHP_Shell_Extension {
  public function register();
}

/**
* storage class for Shell Extensions
*
* 
*/
class PHP_Shell_Extensions {
  /**
  * @var PHP_Shell_Extensions
  */
  static protected $instance;

  /**
  * storage for the extension
  *
  * @var array
  */
  protected $exts = array();

  /**
  * the extension object gives access to the register objects
  * through the a simple $exts->name->...
  *
  * @param string registered name of the extension 
  * @return PHP_Shell_Extension object handle
  */
  public function __get($key) {
    if (!isset($this->exts[$key])) {
      throw new Exception("Extension $s is not known.");
    }
    return $this->exts[$key];
  }

  /**
  * register set of extensions
  *
  * @param array set of (name, class-name) pairs
  */
  public function registerExtensions($exts) {
    foreach ($exts as $k => $v) {
      $this->registerExtension($k, $v);
    }
  }

  /**
  * register a single extension
  *
  * @param string name of the registered extension
  * @param PHP_Shell_Extension the extension object
  */
  public function registerExtension($k, PHP_Shell_Extension $obj) {
    $obj->register();

    $this->exts[$k] = $obj;
  }

  /**
  * @return object a singleton of the class 
  */
  static function getInstance() {
    if (is_null(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class();
    }
    return self::$instance;
  }
}

//Shell/Options.php
class PHP_Shell_Options implements PHP_Shell_Extension {
  /*
  * instance of the current class
  *
  * @var PHP_Shell_Options
  */
  static protected $instance;

  /**
  * known options and their setors
  *
  * @var array
  * @see registerOption
  */
  protected $options = array();

  /**
  * known options and their setors
  *
  * @var array
  * @see registerOptionAlias
  */
  protected $option_aliases = array();

  public function register() {
    $cmd = PHP_Shell_Commands::getInstance();
    $cmd->registerCommand('#^:set #', $this, 'cmdSet', ':set <var>', 'set a shell variable');
  }

  /**
  * register a option
  *
  * @param string name of the option
  * @param object a object handle
  * @param string method-name of the setor in the object
  * @param string (unused)
  */ 
  public function registerOption($option, $obj, $setor, $getor = null) {
    if (!method_exists($obj, $setor)) {
      throw new Exception(sprintf("setor %s doesn't exist on class %s", $setor, get_class($obj)));
    }

    $this->options[trim($option)] = array("obj" => $obj, "setor" => $setor);
  }

  /**
  * set a shell-var
  *
  * :set al to enable autoload
  * :set bg=dark to enable highlighting with a dark backgroud
  */
  public function cmdSet($l) {
    if (!preg_match('#:set\s+([a-z]+)\s*(?:=\s*([a-z0-9]+)\s*)?$#i', $l, $a)) {
      print(':set failed: either :set <option> or :set <option> = <value>');
      return;
    }

    $this->execute($a[1], isset($a[2]) ? $a[2] : null);
  }

  /**
  * get all the option names
  *
  * @return array names of all options
  */
  public function getOptions() {
    return array_keys($this->options);
  }

  /**
  * map a option to another option
  *
  * e.g.: bg maps to background
  *
  * @param string alias
  * @param string option
  */
  public function registerOptionAlias($alias, $option) {
    if (!isset($this->options[$option])) {
      throw new Exception(sprintf("Option %s is not known", $option));
    }

    $this->option_aliases[trim($alias)] = trim($option);

  }

  /**
  * execute a :set command
  *
  * calls the setor for the :set <option>
  * 
  * 
  */
  private function execute($key, $value) {
    /* did we hit a alias (bg for backgroud) ? */
    if (isset($this->option_aliases[$key])) {
      $opt_key = $this->option_aliases[$key];
    } else {
      $opt_key = $key;
    }

    if (!isset($this->options[$opt_key])) {
      print (':set '.$key.' failed: unknown key');
      return;
    }

    if (!isset($this->options[$opt_key]["setor"])) {
      return;
    }

    $setor = $this->options[$opt_key]["setor"];
    $obj = $this->options[$opt_key]["obj"];
    $obj->$setor($key, $value);
  }

  static function getInstance() {
    if (is_null(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class();
    }
    return self::$instance;
  }
}


class PHP_Shell {
  /** 
  * current code-buffer
  * @var string
  */
  protected $code; 

  /** 
  * set if readline support is enabled 
  * @var bool
  */
  protected $have_readline; 

  /** 
  * current version of the class 
  * @var string
  */
  protected $version = '0.3.0';

  /**
  *
  */
  protected $stdin;

  /**
  * init the shell and change if readline support is available
  */ 
  public function __construct() {
    $this->code = '';

    $this->stdin = null;

    $this->have_readline = function_exists('readline');

    if ($this->have_readline) {
      readline_completion_function('__shell_readline_complete');
    }

    $this->use_readline = true;

    $cmd = PHP_Shell_Commands::getInstance();

    $cmd->registerCommand('#^quit$#', $this, 'cmdQuit', 'quit', 'leaves the shell');
    $cmd->registerCommand('#^\?$#', $this, 'cmdHelp', '?', 'show this help');
    $cmd->registerCommand('#^\?\s+license$#', $this, 'cmdLicense', '? license', 'show license of the shell');
  }


  /**
  * parse the PHP code
  *
  * we parse before we eval() the code to
  * - fetch fatal errors before they come up
  * - know about where we have to wait for closing braces
  *
  * @return int 0 if a executable statement is in the code-buffer, non-zero otherwise
  */
  public function parse() {
    ## remove empty lines
    $this->code = trim($this->code);
    if ($this->code == '') return 1;

    $t = token_get_all('<?php '.$this->code.' ?>');

    $need_semicolon = 1; /* do we need a semicolon to complete the statement ? */
    $need_return = 1;    /* can we prepend a return to the eval-string ? */
    $eval = '';          /* code to be eval()'ed later */
    $braces = array();   /* to track if we need more closing braces */

    $methods = array();  /* to track duplicate methods in a class declaration */
    $ts = array();       /* tokens without whitespaces */

    foreach ($t as $ndx => $token) {
      if (is_array($token)) {
        $ignore = 0;

        switch($token[0]) {
        case T_WHITESPACE:
        case T_OPEN_TAG:
        case T_CLOSE_TAG:
          $ignore = 1;
          break;
        case T_FOREACH:
        case T_DO:
        case T_WHILE:
        case T_FOR:

        case T_IF:
        case T_RETURN:

        case T_CLASS:
        case T_FUNCTION:
        case T_INTERFACE:

        case T_PRINT:
        case T_ECHO:

        case T_COMMENT:
        case T_UNSET:

        case T_INCLUDE:
        case T_REQUIRE:
        case T_INCLUDE_ONCE:
        case T_REQUIRE_ONCE:
        case T_TRY:
          $need_return = 0;
          break;
        case T_EMPTY:
        case T_ISSET:
        case T_EVAL:

        case T_VARIABLE:
        case T_STRING:
        case T_NEW:
        case T_EXTENDS:
        case T_IMPLEMENTS:
        case T_OBJECT_OPERATOR:
        case T_DOUBLE_COLON:
        case T_INSTANCEOF:

        case T_CATCH:

        case T_ELSE:
        case T_AS:
        case T_LNUMBER:
        case T_DNUMBER:
        case T_CONSTANT_ENCAPSED_STRING:
        case T_ENCAPSED_AND_WHITESPACE:
        case T_CHARACTER:
        case T_ARRAY:
        case T_DOUBLE_ARROW:

        case T_CONST:
        case T_PUBLIC:
        case T_PROTECTED:
        case T_PRIVATE:
        case T_ABSTRACT:
        case T_STATIC:
        case T_VAR:

        case T_INC:
        case T_DEC:
        case T_SL:
        case T_SL_EQUAL:
        case T_SR:
        case T_SR_EQUAL:

        case T_IS_EQUAL:
        case T_IS_IDENTICAL:
        case T_IS_GREATER_OR_EQUAL:
        case T_IS_SMALLER_OR_EQUAL:

        case T_BOOLEAN_OR:
        case T_LOGICAL_OR:
        case T_BOOLEAN_AND:
        case T_LOGICAL_AND:
        case T_LOGICAL_XOR:
        case T_MINUS_EQUAL:
        case T_PLUS_EQUAL:
        case T_MUL_EQUAL:
        case T_DIV_EQUAL:
        case T_MOD_EQUAL:
        case T_XOR_EQUAL:
        case T_AND_EQUAL:
        case T_OR_EQUAL:

        case T_FUNC_C:
        case T_CLASS_C:
        case T_LINE:
        case T_FILE:

          /* just go on */
          break;
        default:
          /* debug unknown tags*/
          error_log(sprintf("unknown tag: %d (%s): %s".PHP_EOL, $token[0], token_name($token[0]), $token[1]));

          break;
        }
        if (!$ignore) {
          $eval .= $token[1]." ";
          $ts[] = array("token" => $token[0], "value" => $token[1]);
        }
      } else {
        $ts[] = array("token" => $token, "value" => '');

        $last = count($ts) - 1;

        switch ($token) {
        case '(':
          /* walk backwards through the tokens */

          if ($last >= 4 &&
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_OBJECT_OPERATOR &&
            $ts[$last - 3]['token'] == ')' ) {
            /* func()->method()
            * 
            * we can't know what func() is return, so we can't 
            * say if the method() exists or not
            * 
            */
          } else if ($last >= 3 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_OBJECT_OPERATOR &&
            $ts[$last - 3]['token'] == T_VARIABLE ) {

            /* $object->method( */


            /* $object has to exist and has to be a object */
            $objname = $ts[$last - 3]['value'];

            if (!isset($GLOBALS[ltrim($objname, '$')])) {
              throw new Exception(sprintf('Variable \'%s\' is not set', $objname));
            }
            $object = $GLOBALS[ltrim($objname, '$')];

            if (!is_object($object)) {
              throw new Exception(sprintf('Variable \'%s\' is not a class', $objname));
            }

            $method = $ts[$last - 1]['value'];

            /* obj */

            if (!method_exists($object, $method) && !method_exists($object, '__call') ) {
              throw new Exception(sprintf("Variable %s (Class '%s') doesn't have a method named '%s'", 
                $objname, get_class($object), $method));
            }
          } else if ($last >= 3 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_VARIABLE &&
            $ts[$last - 2]['token'] == T_OBJECT_OPERATOR &&
            $ts[$last - 3]['token'] == T_VARIABLE ) {

            /* $object->$method( */

            /* $object has to exist and has to be a object */
            $objname = $ts[$last - 3]['value'];

            if (!isset($GLOBALS[ltrim($objname, '$')])) {
              throw new Exception(sprintf('Variable \'%s\' is not set', $objname));
            }
            $object = $GLOBALS[ltrim($objname, '$')];

            if (!is_object($object)) {
              throw new Exception(sprintf('Variable \'%s\' is not a class', $objname));
            }

            $methodname = $ts[$last - 1]['value'];

            if (!isset($GLOBALS[ltrim($methodname, '$')])) {
              throw new Exception(sprintf('Variable \'%s\' is not set', $methodname));
            }
            $method = $GLOBALS[ltrim($methodname, '$')];

            /* obj */

            if (!method_exists($object, $method)) {
              throw new Exception(sprintf("Variable %s (Class '%s') doesn't have a method named '%s'", 
                $objname, get_class($object), $method));
            }

          } else if ($last >= 6 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_OBJECT_OPERATOR &&
            $ts[$last - 3]['token'] == ']' &&
              /* might be anything as index */
            $ts[$last - 5]['token'] == '[' &&
            $ts[$last - 6]['token'] == T_VARIABLE ) {

            /* $object[...]->method( */

            /* $object has to exist and has to be a object */
            $objname = $ts[$last - 6]['value'];

            if (!isset($GLOBALS[ltrim($objname, '$')])) {
              throw new Exception(sprintf('Variable \'%s\' is not set', $objname));
            }
            $array = $GLOBALS[ltrim($objname, '$')];

            if (!is_array($array)) {
              throw new Exception(sprintf('Variable \'%s\' is not a array', $objname));
            }

            $andx = $ts[$last - 4]['value'];

            if (!isset($array[$andx])) {
              throw new Exception(sprintf('%s[\'%s\'] is not set', $objname, $andx));
            }

            $object = $array[$andx];

            if (!is_object($object)) {
              throw new Exception(sprintf('Variable \'%s\' is not a class', $objname));
            }

            $method = $ts[$last - 1]['value'];

            /* obj */

            if (!method_exists($object, $method)) {
              throw new Exception(sprintf("Variable %s (Class '%s') doesn't have a method named '%s'", 
                $objname, get_class($object), $method));
            }

          } else if ($last >= 3 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_DOUBLE_COLON &&
            $ts[$last - 3]['token'] == T_STRING ) {

            /* Class::method() */

            /* $object has to exist and has to be a object */
            $classname = $ts[$last - 3]['value'];

            if (!class_exists($classname)) {
              throw new Exception(sprintf('Class \'%s\' doesn\'t exist', $classname));
            }

            $method = $ts[$last - 1]['value'];

            if (!in_array($method, get_class_methods($classname))) {
              throw new Exception(sprintf("Class '%s' doesn't have a method named '%s'", 
                $classname, $method));
            }
          } else if ($last >= 3 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_VARIABLE &&
            $ts[$last - 2]['token'] == T_DOUBLE_COLON &&
            $ts[$last - 3]['token'] == T_STRING ) {

            /* $var::method() */

            /* $object has to exist and has to be a object */
            $classname = $ts[$last - 3]['value'];

            if (!class_exists($classname)) {
              throw new Exception(sprintf('Class \'%s\' doesn\'t exist', $classname));
            }

            $methodname = $ts[$last - 1]['value'];

            if (!isset($GLOBALS[ltrim($methodname, '$')])) {
              throw new Exception(sprintf('Variable \'%s\' is not set', $methodname));
            }
            $method = $GLOBALS[ltrim($methodname, '$')];

            if (!in_array($method, get_class_methods($classname))) {
              throw new Exception(sprintf("Class '%s' doesn't have a method named '%s'", 
                $classname, $method));
            }

          } else if ($last >= 2 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_NEW ) {

            /* new Class() */

            /* don't care about this in a class ... { ... } */

            $classname = $ts[$last - 1]['value'];

            if (!class_exists($classname)) {
              throw new Exception(sprintf('Class \'%s\' doesn\'t exist', $classname));
            }

            $r = new ReflectionClass($classname);

            if ($r->isAbstract()) {
              throw new Exception(sprintf("Can't instantiate abstract Class '%s'", $classname));
            }

            if (!$r->isInstantiable()) {
              throw new Exception(sprintf('Class \'%s\' can\'t be instantiated. Is the class abstract ?', $classname));
            }

          } else if ($last >= 2 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_FUNCTION ) {

            /* make sure we are not a in class definition */

            /* function a() */

            $func = $ts[$last - 1]['value'];

            if (function_exists($func)) {
              throw new Exception(sprintf('Function \'%s\' is already defined', $func));
            }
          } else if ($last >= 4 &&
            $ts[0]['token'] == T_CLASS &&
            $ts[1]['token'] == T_STRING &&
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_FUNCTION ) {

            /* make sure we are not a in class definition */

            /* class a { .. function a() ... } */

            $func = $ts[$last - 1]['value'];
            $classname = $ts[1]['value'];

            if (isset($methods[$func])) {
              throw new Exception(sprintf("Can't redeclare method '%s' in Class '%s'", $func, $classname));
            }

            $methods[$func] = 1;

          } else if ($last >= 1 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_STRING ) {
            /* func() */
            $funcname = $ts[$last - 1]['value'];

            if (!function_exists($funcname)) {
              //throw new Exception(sprintf("Function %s() doesn't exist", $funcname));
            }
          } else if ($last >= 1 &&
            $ts[0]['token'] != T_CLASS && /* if we are not in a class definition */
            $ts[$last - 1]['token'] == T_VARIABLE ) {

            /* $object has to exist and has to be a object */
            $funcname = $ts[$last - 1]['value'];

            if (!isset($GLOBALS[ltrim($funcname, '$')])) {
              throw new Exception(sprintf('Variable \'%s\' is not set', $funcname));
            }
            $func = $GLOBALS[ltrim($funcname, '$')];

            if (!function_exists($func)) {
              throw new Exception(sprintf("Function %s() doesn't exist", $func));
            }

          }

          array_push($braces, $token);
          break;
        case '{':
          $need_return = 0;

          if ($last >= 2 &&
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_CLASS ) {

            /* class name { */

            $classname = $ts[$last - 1]['value'];

            if (class_exists($classname, false)) {
              throw new Exception(sprintf("Class '%s' can't be redeclared", $classname));
            }
          } else if ($last >= 4 &&
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_EXTENDS &&
            $ts[$last - 3]['token'] == T_STRING &&
            $ts[$last - 4]['token'] == T_CLASS ) {

            /* class classname extends classname { */

            $classname = $ts[$last - 3]['value'];
            $extendsname = $ts[$last - 1]['value'];

            if (class_exists($classname, false)) {
              throw new Exception(sprintf("Class '%s' can't be redeclared", 
                $classname));
            }
            if (!class_exists($extendsname, true)) {
              throw new Exception(sprintf("Can't extend '%s' ... from not existing Class '%s'", 
                $classname, $extendsname));
            }
          } else if ($last >= 4 &&
            $ts[$last - 1]['token'] == T_STRING &&
            $ts[$last - 2]['token'] == T_IMPLEMENTS &&
            $ts[$last - 3]['token'] == T_STRING &&
            $ts[$last - 4]['token'] == T_CLASS ) {

            /* class name implements interface { */

            $classname = $ts[$last - 3]['value'];
            $implements = $ts[$last - 1]['value'];

            if (class_exists($classname, false)) {
              throw new Exception(sprintf("Class '%s' can't be redeclared", 
                $classname));
            }
            if (!interface_exists($implements, false)) {
              throw new Exception(sprintf("Can't implement not existing Interface '%s' for Class '%s'", 
                $implements, $classname));
            }
          }

          array_push($braces, $token);
          break;
        case '}':
          $need_return = 0;
        case ')':
          array_pop($braces);
          break;
        case '[':
          if ($ts[$last - 1]['token'] == T_VARIABLE) {
            /* $a[] only works on array and string */

            /* $object has to exist and has to be a object */
            $objname = $ts[$last - 1]['value'];

            if (!isset($GLOBALS[ltrim($objname, '$')])) {
              throw new Exception(sprintf('Variable \'%s\' is not set', $objname));
            }
            $obj = $GLOBALS[ltrim($objname, '$')];

            // if (is_object($obj)) {
            //  throw new Exception(sprintf('Objects (%s) don\'t support array access operators', $objname));
            // }
          }
          break;
        }

        $eval .= $token;
      }    
    }

    $last = count($ts) - 1;
    if ($last >= 2 &&
      $ts[$last - 0]['token'] == T_STRING &&
      $ts[$last - 1]['token'] == T_DOUBLE_COLON &&
      $ts[$last - 2]['token'] == T_STRING ) {

      /* Class::constant */

      /* $object has to exist and has to be a object */
      $classname = $ts[$last - 2]['value'];

      if (!class_exists($classname)) {
        throw new Exception(sprintf('Class \'%s\' doesn\'t exist', $classname));
      }

      $constname = $ts[$last - 0]['value'];

      $c = new ReflectionClass($classname);
      if (!$c->hasConstant($constname)) {
        throw new Exception(sprintf("Class '%s' doesn't have a constant named '%s'", 
          $classname, $constname));
      }
    } else if ($last == 0 &&
      $ts[$last - 0]['token'] == T_VARIABLE ) {

      /* $var */

      $varname = $ts[$last - 0]['value'];

      if (!isset($GLOBALS[ltrim($varname, '$')])) {
        throw new Exception(sprintf('Variable \'%s\' is not set', $varname));
      }
    }


    $need_more = count($braces);

    if ($need_more || ';' === $token) {
      $need_semicolon = 0;
    }  

    if ($need_return) {
      $eval = "return ".$eval;
    }

    /* add a traling ; if necessary */ 
    if ($need_semicolon) $eval .= ';';

    if (!$need_more) {
      $this->code = $eval;
    }

    return $need_more;
  }

  /**
  * show the prompt and fetch a single line
  * 
  * uses readline() if avaialbe
  * 
  * @return string a input-line
  */
  public function readline() {
    if (empty($this->code)) print PHP_EOL;

    $prompt = (empty($this->code)) ? '>> ' : '.. ';

    if ($this->have_readline) {
      $l = readline($prompt);

      readline_add_history($l);
    } else {
      print $prompt;

      if (is_null($this->stdin)) {
        if (false === ($this->stdin = fopen("php://stdin", "r"))) {
          return false;
        }
      }
      $l = fgets($this->stdin);
    }
    return $l;
  }

  /**
  * get the inline help
  *
  * @return string the inline help as string 
  */
  public function cmdHelp($l) {
    $o = 'Inline Help:'.PHP_EOL;

    $cmds = PHP_Shell_Commands::getInstance()->getCommands();

    foreach ($cmds as $cmd) {
      $o .= sprintf('  >> %s'.PHP_EOL.'    %s'.PHP_EOL,
        $cmd['command'],
        $cmd['description']
      );
    }

    return var_export($o, 1);
  }

  /**
  * get the license string
  *
  * @return string the inline help as string 
  */
  public function cmdLicense($l) {
    $o = <<<EOF
(c) 2006 Jan Kneschke <jan@kneschke.de>

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
EOF;

    return var_export($o, 1);
  }

  /**
  * handle the 'quit' command
  *
  * @return bool false to leave the input() call
  * @see input
  */
  protected function cmdQuit($l) {
    return false;
  }

  /**
  * handle the input line
  *
  * read the input and handle the commands of the shell
  *
  * @return bool false on 'quit' or EOF, true otherwise
  */
  public function input() {
    $l = $this->readline();

    /* got EOF ? */
    if (false === $l) return false;

    $l = trim($l);

    if (empty($this->code)) {
      $this->verbose = 0;

      $cmds = PHP_Shell_Commands::getInstance()->getCommands();

      foreach ($cmds as $cmd) {
        if (preg_match($cmd['regex'], $l)) {
          $obj = $cmd['obj'];
          $func = $cmd['method'];

          if (false === ($l = $obj->$func($l))) {
            ## quit
            return false;
          }
          break;
        }
      }
    }

    $this->appendCode($l); 

    return true;
  }

  /**
  * get the code-buffer
  * 
  * @return string the code-buffer
  */
  public function getCode() {
    return $this->code;
  }

  /**
  * reset the code-buffer
  */
  public function resetCode() {
    $this->code = '';
  }

  /**
  * append code to the code-buffer
  *
  * @param string $code input buffer
  */
  public function appendCode($code) {
    $this->code .= $code;
  }

  /**
  * check if readline support is enabled
  *
  * @return bool true if enabled, false otherwise
  */
  public function hasReadline() {
    return $this->have_readline;
  }

  /**
  * get version of the class
  *
  * @return string version-string
  */
  public function getVersion() {
    return $this->version;
  }
}

/**
* a readline completion callback
*
* @param string $str linebuffer
* @param integer $pos position in linebuffer
* @return array list of possible matches
*/
function __shell_readline_complete($str, $pos) {
  $in = readline_info('line_buffer');

  /**
  * parse the line-buffer backwards to see if we have a 
  * - constant
  * - function 
  * - variable
  */

  $m = array();

  if (preg_match('#\$([A-Za-z0-9_]+)->#', $in, $a)) {
    /* check for $o->... */
    $name = $a[1];

    if (isset($GLOBALS[$name]) && is_object($GLOBALS[$name])) {
      $c = get_class_methods($GLOBALS[$name]);

      foreach ($c as $v) {
        $m[] = $v.'(';
      }
      $c = get_class_vars(get_class($GLOBALS[$name]));

      foreach ($c as $k => $v) {
        $m[] = $k;
      }

      return $m;
    }
  } else if (preg_match('#\$([A-Za-z0-9_]+)\[([^\]]+)\]->#', $in, $a)) {
    /* check for $o[...]->... */
    $name = $a[1];

    if (isset($GLOBALS[$name]) && 
      is_array($GLOBALS[$name]) &&
      isset($GLOBALS[$name][$a[2]])) {

      $c = get_class_methods($GLOBALS[$name][$a[2]]);

      foreach ($c as $v) {
        $m[] = $v.'(';
      }
      $c = get_class_vars(get_class($GLOBALS[$name][$a[2]]));

      foreach ($c as $k => $v) {
        $m[] = $k;
      }
      return $m;
    }

  } else if (preg_match('#([A-Za-z0-9_]+)::#', $in, $a)) {
    /* check for Class:: */
    $name = $a[1];

    if (class_exists($name, false)) {
      $c = get_class_methods($name);

      foreach ($c as $v) {
        $m[] = sprintf('%s::%s(', $name, $v);
      }

      $cl = new ReflectionClass($name);
      $c = $cl->getConstants();

      foreach ($c as $k => $v) {
        $m[] = sprintf('%s::%s', $name, $k);
      }

      return $m;
    }
  } else if (preg_match('#\$([a-zA-Z]?[a-zA-Z0-9_]*)$#', $in)) {
    $m = array_keys($GLOBALS);

    return $m;
  } else if (preg_match('#new #', $in)) {
    $c = get_declared_classes();

    foreach ($c as $v) {
      $m[] = $v.'(';
    }

    return $m;
  } else if (preg_match('#^:set #', $in)) {
    foreach (PHP_Shell_Options::getInstance()->getOptions() as $v) {
      $m[] = $v;
    }

    return $m;
  }

  $f = get_defined_functions();

  foreach ($f['internal'] as $v) {
    $m[] = $v.'(';
  }

  foreach ($f['user'] as $v) {
    $m[] = $v.'(';
  }

  $c = get_declared_classes();

  foreach ($c as $v) {
    $m[] = $v.'::';
  }

  $c = get_defined_constants();

  foreach ($c as $k => $v) {
    $m[] = $k;
  }

  /* taken from http://de3.php.net/manual/en/reserved.php */
  $m[] = 'abstract';
  $m[] = 'and';
  $m[] = 'array(';
  $m[] = 'as';
  $m[] = 'break';
  $m[] = 'case';
  $m[] = 'catch';
  $m[] = 'class';
  $m[] = 'const';
  $m[] = 'continue';
  # $m[] = 'declare';
  $m[] = 'default';
  $m[] = 'die(';
  $m[] = 'do';
  $m[] = 'echo(';
  $m[] = 'else';
  $m[] = 'elseif';
  $m[] = 'empty(';
  # $m[] = 'enddeclare';
  $m[] = 'eval(';
  $m[] = 'exception';
  $m[] = 'extends';
  $m[] = 'exit(';
  $m[] = 'extends';
  $m[] = 'final';
  $m[] = 'for (';
  $m[] = 'foreach (';
  $m[] = 'function';
  $m[] = 'global';
  $m[] = 'if';
  $m[] = 'implements';
  $m[] = 'include "';
  $m[] = 'include_once "';
  $m[] = 'interface';
  $m[] = 'isset(';
  $m[] = 'list(';
  $m[] = 'new';
  $m[] = 'or';
  $m[] = 'print(';
  $m[] = 'private';
  $m[] = 'protected';
  $m[] = 'public';
  $m[] = 'require "';
  $m[] = 'require_once "';
  $m[] = 'return';
  $m[] = 'static';
  $m[] = 'switch (';
  $m[] = 'throw';
  $m[] = 'try';
  $m[] = 'unset(';
  # $m[] = 'use';
  $m[] = 'var';
  $m[] = 'while';
  $m[] = 'xor';
  $m[] = '__FILE__';
  $m[] = '__FUNCTION__';
  $m[] = '__CLASS__';
  $m[] = '__LINE__';
  $m[] = '__METHOD__';

  # printf("%s ... %s\n", $str, $pos);
  return $m;
}

// initialize database manager

// pear-shell-cmd.php
@ob_end_clean();
error_reporting(E_ALL);
set_time_limit(0);

$__shell = new PHP_Shell();

$f = 'Loading development environment...';

printf($f, 
  $__shell->getVersion(), 
  $__shell->hasReadline() ? ', with readline() support' : '');
unset($f);

while($__shell->input()) {
  try {
    if ($__shell->parse() == 0) {
      ## we have a full command, execute it

      $__shell_retval = eval($__shell->getCode()); 
      if (isset($__shell_retval)) {
        //var_export($__shell_retval);
        if (is_array($__shell_retval))
        {
          echo "[";
          $p = 0;
          foreach ($__shell_retval as $num=>$array)
          {
            $obj = '';
            if (is_object($array)) $obj = method_exists ( $array, '__toString' )?$array:get_class($array);

            if ($p) echo ', ';
            echo "#< " . $obj . ' ';
            if (!is_object($array))
            {
              if (is_array($array)) echo $num . ": ";
              ob_start();
              var_export($array);
              $output = ob_get_clean();
              $output = str_replace('array ','',$output);
              $output = str_replace("\r",'',$output);
              $output = str_replace("\n",'',$output);
              echo trim($output) . " ";
            }
            else
            {
              $vars = get_object_vars($array);
              $f = false;
              foreach ($vars as $key=>$value)
              {
                if ($key == "__conf") continue;
                if ($f) echo ', ';
                echo $key . ': "' . $value . '"';
                $f = true;
              }
            }
            echo '>';

            $p = 1;
          }
          echo ']';
        }
        else if (!is_object($__shell_retval)) var_export($__shell_retval);
        else
        {
          if (is_object($__shell_retval)) $obj = method_exists ( $__shell_retval, '__toString' )?$__shell_retval:get_class($__shell_retval);
          $vars = get_object_vars($__shell_retval);
          echo " #<" . $obj . ' ';
          $f = false;
          foreach ($vars as $key=>$value)
          {
            if ($key == "__conf") continue;
            if ($f) echo ', ';
            $varname = "";
            if (!is_object($value)) $varname = $value;
            else
            {
              $varname = get_class($value);
            }
            echo $key . ': "' . $varname . '"';
            $f = true;
          }
          echo '>';
        }
      }
      ## cleanup the variable namespace
      unset($__shell_retval);
      $__shell->resetCode();
    }
  } catch(Exception $__shell_exception) {
    print $__shell_exception->getMessage();

    $__shell->resetCode();

    ## cleanup the variable namespace
    unset($__shell_exception);
  }
}


?>
