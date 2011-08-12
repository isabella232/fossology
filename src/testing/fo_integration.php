<?php
/*
 Copyright (C) 2011 Hewlett-Packard Development Company, L.P.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 *
 * \brief base class for fossology integration.
 *
 * This class holds properties that extenders of this class should use, such
 * as srcPath or logPath.
 *
 * @param string $sourcePath The fully qualified path to the fossology sources.
 * If no source path is supplied, the current working directory will be used.
 * This may or may not work for the caller, but is better than failing.
 * Operating in this way should allow the code to run standalone or in Jenkins.
 *
 * @param string $logPath The fully qualified path to the log file. The default
 * is to use the current working directory.  If the the logfile cannot be opened
 * an expection is thrown.
 *
 * @return object or exception
 *
 * @version "$Id$"
 *
 * @author markd
 *
 * Created on Aug 11, 2011 by Mark Donohoe
 */
class FoIntegration
{
  public $srcPath;
  public $logPath;
  protected $LOGFD;

  public function __construct($sourcePath, $logPath=NULL)
  {
    if(empty($sourcePath))
    {
      $this->srcPath = getcwd();
    }
    else
    {
      $this->srcPath = $sourcePath;
    }
    if(is_NULL($logPath))
    {
      $this->logPath = getcwd() . "/fo_integration.log";
      echo "DB: logpath is:$this->logPath\n";
    }
    else
    {
      $this->logPath = $logPath;
      echo "DB: logpath is:$this->logPath\n";
    }

    $this->LOGFD = fopen($this->logPath, 'a+');
    if($this->LOGFD === FALSE)
    {
      $error = "Error! cannot open $this->logPath" . " File: " . __FILE__ .
        " on line: " . __LINE__;
      throw new exception($error);
    }

  } // __construct

  /**
   * \brief log a message in a file
   *
   * @param string $message The message to log.
   *
   * @return boolean, false if the write failed, true otherwise.
   *
   */
  protected function log($message)
  {
    if(fwrite($this->LOGFD, $message) === FALSE)
    {
      // remove the warning? and have caller do it?
      echo "WARNING! cannot write to log file, there may be no log messages\n";
      return(FALSE);
    }
    return(TRUE);
  } // log

} //fo_integration


class Build extends FoIntegration
{

  /**
   * \brief make fossology
   *
   * Cd's into the supplied source path.  Does a make clean then a make.  Checks
   * the result of the make and returns a boolean.
   *
   * @return boolean true for no make errors, false for 1 or more make errors
   *
   */
  function __construct($srcPath, $logPath=NULL)
  {
    parent::__construct($srcPath,$logPath);
    if (!chdir($this->srcPath))
    {
      throw new exception("FATAL! can't cd to $this->srcPath\n");
    }
    $mcLast = exec('make clean > make-clean.out 2>&1', $results, $rtn);
    //print "results of the make clean are:$rtn, $mcLast\n";
    $makeLast = exec('make > make.out 2>&1', $results, $rtn);
    //print "results of the make are:$rtn, $makeLast\n"; print_r($results) . "\n";
    if ($rtn == 0)
    {
      //print "results of the make are:\n"; print_r($results) . "\n";
      if (array_search('Error', $results))
      {
        //print "Found Error string in the Make output\n";
        // TODO: write results out to: make.out ?? what ??
        throw new exception("Errors in make, inspect make.out for details\n");
      }
    }
    else
    {
        throw new exception("Errors in make, inspect make.out for details\n");
    }
  } // makeFossology

} // build
?>