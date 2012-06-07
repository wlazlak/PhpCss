<?php
/**
* List of Css Selector Sequences.
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* List of Css Selector Sequences.
*
* This is the root element of a standard selector string like:
* "element, .class"
*
* Because it is a list the some standard interfaces are implemented for
* easier usage.
*
* @package PhpCss
* @subpackage Ast
*/
class PhpCssAstSelectorSequenceList
  extends PhpCssAstSelector
  implements ArrayAccess, Countable, IteratorAggregate {

  private $_sequences = array();

  /**
  * Create the object and assign sequences if provided. They
  * can be added later of course.
  *
  * @param array $sequences
  */
  public function __construct(array $sequences = array()) {
    foreach ($sequences as $sequence) {
      $this->offsetSet(NULL, $sequence);
    }
  }

  /**
  * Check if a sequence at the given position is available in the list.
  *
  * @see ArrayAccess::offsetExists()
  * @param integer $offset
  * @return boolean
  */
  public function offsetExists($offset) {
    return isset($this->_sequences[$offset]);
  }

  /**
  * Return the sequence at the given position.
  *
  * @see ArrayAccess::offsetGet()
  * @param integer $offset
  * @return PhpCssAstSelectorSequence
  */
  public function offsetGet($offset) {
    return $this->_sequences[$offset];
  }

  /**
  * Set/Add and sequence at the given position or top the end
  *
  * @see ArrayAccess::offsetSet()
  * @param integer|NULL $offset
  * @param PhpCssAstSelectorSequence
  */
  public function offsetSet($offset, $sequence) {
    if (!$sequence instanceOf PhpCssAstSelectorSequence) {
      throw new InvalidArgumentException(
        sprintf(
          '$sequence is not an instance of PhpCssAstSelectorSequence but %s.',
          is_object($sequence) ? get_class($sequence) : gettype($sequence)
        )
      );
    }
    if (is_null($offset)) {
      $this->_sequences[] = $sequence;
    } else {
      $this->_sequences[(int)$offset] = $sequence;
      $this->_sequences = array_values($this->_sequences);
    }
  }

  /**
  * Remove the sequence at the given position
  *
  * @see ArrayAccess::offsetUnset()
  * @param integer $offset
  */
  public function offsetUnset($offset) {
    unset($this->_sequences[$offset]);
    $this->_sequences = array_values($this->_sequences);
  }

  /**
  * Return the sequence list count.
  *
  * @see Countable::count()
  * @return integer
  */
  public function count() {
    return count($this->_sequences);
  }

  /**
  * Return an iterator for the sequences
  *
  * @see IteratorAggregate::getIterator()
  * @return Iterator
  */
  public function getIterator() {
    return new ArrayIterator($this->_sequences);
  }

  /**
  * Accept visitors, because this element has children, enter and leave are called.
  *
  * @param PhpCssAstVisitor $visitor
  */
  public function accept(PhpCssAstVisitor $visitor) {
    if ($visitor->visitEnter($this)) {
      foreach ($this as $sequence) {
        $sequence->accept($visitor);
      }
      return $visitor->visitLeave($this);
    }
    return NULL;
  }
}